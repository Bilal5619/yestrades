<?php

namespace App\Console\Commands;

use App\Http\Helpers\BasicMailer;
use App\Http\Helpers\CertificateExpiry;
use App\Models\BasicSettings\Basic;
use App\Models\BasicSettings\MailTemplate;
use App\Models\Services\ServiceBooking;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Tells the office about certificates coming up for renewal.
 *
 *  - Job certificates issued to customers: one email per booking at 60, 30 and
 *    14 days before expiry (and once after it lapses), using the editable
 *    "certificate_expiry_reminder" mail template. Same thresholds as 11Done.
 *  - Vendor documents and staff training/documents: a single digest 30 days out,
 *    since those are internal records rather than a customer to call back.
 *
 * Every reminder is recorded in certificate_expiry_reminders, so nothing is
 * ever emailed twice, and a missed cron run simply catches up on the next run
 * (thresholds are matched with "<=", not on an exact day).
 */
class SendCertificateExpiryReminders extends Command
{
  protected $signature = 'certificates:remind {--dry-run : List what would be sent without emailing}';

  protected $description = 'Email the office about certificates expiring at 60/30/14 days, and ones that have expired';

  /** Days before expiry that a job certificate reminder goes out. */
  private const THRESHOLDS = [60, 30, 14];

  public function handle()
  {
    $dryRun = (bool) $this->option('dry-run');
    $recipients = $this->recipients();

    if (empty($recipients)) {
      $this->error('No reminder email address configured (Admin > Basic Settings > Email).');
      return Command::FAILURE;
    }

    $sent = $this->sendJobCertificateReminders($recipients, $dryRun);
    $sent += $this->sendInternalDigest($recipients, $dryRun);

    if ($sent === 0) {
      $this->info('No new certificate reminders to send.');
    }

    return Command::SUCCESS;
  }

  /**
   * One email per customer certificate, per threshold, from the mail template
   * so the office can edit the wording in the admin panel.
   */
  private function sendJobCertificateReminders(array $recipients, bool $dryRun): int
  {
    $template = MailTemplate::where('mail_type', 'certificate_expiry_reminder')->first();

    if (!$template) {
      $this->warn('certificate_expiry_reminder mail template is missing — job certificate reminders skipped.');
      return 0;
    }

    $today = Carbon::today();

    $bookings = ServiceBooking::with('serviceContent')
      ->whereNotNull('job_certificate_expiry')
      ->where('job_certificate_has_no_expiry', 0)
      ->whereDate('job_certificate_expiry', '<=', $today->copy()->addDays(max(self::THRESHOLDS))->toDateString())
      ->get();

    $sent = 0;

    foreach ($bookings as $booking) {
      $expiry = Carbon::parse($booking->job_certificate_expiry);
      $daysRemaining = $today->diffInDays($expiry, false);

      // Expired certificates get one final notice; upcoming ones get the
      // highest threshold they have reached and not yet been told about.
      $stages = $daysRemaining < 0
        ? ['expired']
        : array_map('strval', array_filter(self::THRESHOLDS, fn ($t) => $daysRemaining <= $t));

      $due = array_values(array_filter(
        $stages,
        fn ($stage) => !$this->alreadySent('job_certificate', $booking->id, $expiry->toDateString(), $stage)
      ));

      if (empty($due)) {
        continue;
      }

      // A date entered late (or corrected) can make several thresholds due at
      // once. Only the most urgent one is worth an email; the rest are marked
      // as handled so they do not arrive as a burst of duplicates.
      $stageToSend = $due[count($due) - 1];

      if ($dryRun) {
        $this->line("  [{$stageToSend}] Job certificate — {$booking->customer_name} — {$expiry->format('d M Y')} ({$daysRemaining} days)");
        $sent++;
        continue;
      }

      $this->emailBooking($booking, $template, $recipients, $expiry, $daysRemaining);

      foreach ($due as $stage) {
        $this->markSent('job_certificate', $booking->id, $expiry->toDateString(), $stage);
      }

      $sent++;
    }

    if ($sent > 0 && !$dryRun) {
      $this->info("Sent {$sent} job certificate reminder(s).");
    }

    return $sent;
  }

  /**
   * Vendor documents and staff certificates, as one digest 30 days out.
   */
  private function sendInternalDigest(array $recipients, bool $dryRun): int
  {
    $isInternal = fn ($row) => $row['source'] !== 'job_certificate';

    $upcoming = CertificateExpiry::expiringSoon(CertificateExpiry::REMINDER_DAYS)
      ->filter($isInternal)
      ->reject(fn ($row) => $this->alreadySent($row['source'], $row['record_id'], $row['expiry_date'], 'upcoming'))
      ->values();

    $expired = CertificateExpiry::expired()
      ->filter($isInternal)
      ->reject(fn ($row) => $this->alreadySent($row['source'], $row['record_id'], $row['expiry_date'], 'expired'))
      ->values();

    if ($upcoming->isEmpty() && $expired->isEmpty()) {
      return 0;
    }

    if ($dryRun) {
      foreach ($upcoming->concat($expired) as $row) {
        $this->line("  [{$row['stage']}] {$row['type']} — {$row['title']} ({$row['who']}) — {$row['expiry_display']}");
      }
      return $upcoming->count() + $expired->count();
    }

    $body = view('emails.certificate-expiry', [
      'upcoming' => $upcoming,
      'expired' => $expired,
      'days' => CertificateExpiry::REMINDER_DAYS,
    ])->render();

    $subject = $expired->isNotEmpty()
      ? __('Documents expiring soon — and :count expired', ['count' => $expired->count()])
      : __('Documents expiring in the next :days days', ['days' => CertificateExpiry::REMINDER_DAYS]);

    foreach ($recipients as $address) {
      BasicMailer::sendMail([
        'subject' => $subject,
        'body' => $body,
        'recipient' => $address,
      ]);
    }

    foreach ($upcoming->concat($expired) as $row) {
      $this->markSent($row['source'], $row['record_id'], $row['expiry_date'], $row['stage']);
    }

    $this->info("Sent internal document digest: {$upcoming->count()} expiring, {$expired->count()} expired.");

    return $upcoming->count() + $expired->count();
  }

  private function emailBooking(ServiceBooking $booking, MailTemplate $template, array $recipients, Carbon $expiry, int $daysRemaining): void
  {
    $serviceName = $booking->serviceContent->isNotEmpty()
      ? $booking->serviceContent->first()->name
      : ($booking->package_name ?? 'Service #' . $booking->service_id);

    $bookingLink = '<a href="' . route('admin.appointment.details', ['id' => $booking->id]) . '">' . __('View booking') . '</a>';

    $body = str_replace(
      ['{customer_name}', '{customer_phone}', '{customer_email}', '{service_title}', '{booking_number}', '{expiry_date}', '{days_remaining}', '{booking_link}'],
      [
        $booking->customer_name,
        $booking->customer_phone,
        $booking->customer_email,
        $serviceName,
        $booking->order_number,
        $expiry->format('d M Y'),
        $daysRemaining,
        $bookingLink,
      ],
      $template->mail_body
    );

    $subject = str_replace('{customer_name}', $booking->customer_name, $template->mail_subject);

    foreach ($recipients as $address) {
      BasicMailer::sendMail([
        'recipient' => $address,
        'subject' => $subject,
        'body' => $body,
      ]);
    }
  }

  /**
   * Where reminders go: the dedicated certificate address if one is set,
   * otherwise the site's "receive emails at" address. The dedicated field
   * exists so changing it does not also redirect contact-form mail.
   */
  private function recipients(): array
  {
    $settings = Basic::select('certificate_reminder_email', 'to_mail')->first();

    $addresses = [
      trim((string) ($settings->certificate_reminder_email ?? '')),
      trim((string) ($settings->to_mail ?? '')),
    ];

    // The fallback only applies when no dedicated address is configured.
    if ($addresses[0] !== '') {
      $addresses = [$addresses[0]];
    }

    return array_values(array_filter($addresses, fn ($a) => filter_var($a, FILTER_VALIDATE_EMAIL)));
  }

  private function alreadySent(string $source, $recordId, string $expiryDate, string $stage): bool
  {
    return DB::table('certificate_expiry_reminders')
      ->where('source', $source)
      ->where('record_id', (int) $recordId)
      ->whereDate('expiry_date', $expiryDate)
      ->where('stage', $stage)
      ->exists();
  }

  private function markSent(string $source, $recordId, string $expiryDate, string $stage): void
  {
    DB::table('certificate_expiry_reminders')->updateOrInsert(
      [
        'source' => $source,
        'record_id' => (int) $recordId,
        'expiry_date' => $expiryDate,
        'stage' => $stage,
      ],
      [
        'sent_at' => Carbon::now(),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ]
    );
  }
}
