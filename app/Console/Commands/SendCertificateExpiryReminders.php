<?php

namespace App\Console\Commands;

use App\Http\Helpers\BasicMailer;
use App\Http\Helpers\CertificateExpiry;
use App\Models\BasicSettings\Basic;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Emails Yes Trades one month before any certificate expires (and once more
 * when one has lapsed), so a renewal can be booked in time.
 *
 * Every reminder is logged in certificate_expiry_reminders, so each certificate
 * is only ever emailed once per expiry date and stage — the command is safe to
 * run as often as the host's cron fires.
 */
class SendCertificateExpiryReminders extends Command
{
  protected $signature = 'certificates:remind {--days= : Days before expiry to warn (default 30)} {--dry-run : List what would be sent without emailing}';

  protected $description = 'Email Yes Trades about certificates expiring within the next month, and ones that have expired';

  public function handle()
  {
    $days = (int) ($this->option('days') ?: CertificateExpiry::REMINDER_DAYS);
    $dryRun = (bool) $this->option('dry-run');

    $upcoming = CertificateExpiry::expiringSoon($days);
    $expired = CertificateExpiry::expired();

    $newUpcoming = $upcoming->reject(fn ($row) => $this->alreadySent($row))->values();
    $newExpired = $expired->reject(fn ($row) => $this->alreadySent($row))->values();

    if ($newUpcoming->isEmpty() && $newExpired->isEmpty()) {
      $this->info('No new certificate reminders to send.');
      return Command::SUCCESS;
    }

    $recipient = $this->recipient();

    if (empty($recipient)) {
      $this->error('No reminder email address configured (Admin > Basic Settings > Email > Certificate reminder email).');
      return Command::FAILURE;
    }

    $subject = $newExpired->isNotEmpty()
      ? __('Certificates expiring soon — and :count expired', ['count' => $newExpired->count()])
      : __('Certificates expiring in the next :days days', ['days' => $days]);

    $body = view('emails.certificate-expiry', [
      'upcoming' => $newUpcoming,
      'expired' => $newExpired,
      'days' => $days,
    ])->render();

    if ($dryRun) {
      $this->info("Would email {$recipient}: {$newUpcoming->count()} expiring, {$newExpired->count()} expired.");
      foreach ($newUpcoming->concat($newExpired) as $row) {
        $this->line("  [{$row['stage']}] {$row['type']} — {$row['title']} ({$row['who']}) — {$row['expiry_display']}");
      }
      return Command::SUCCESS;
    }

    $sent = BasicMailer::sendMail([
      'subject' => $subject,
      'body' => $body,
      'recipient' => $recipient,
    ]);

    if (!$sent) {
      Log::warning('Certificate expiry reminder email failed', ['recipient' => $recipient]);
      $this->error('Reminder email could not be sent — check the SMTP settings.');
      return Command::FAILURE;
    }

    foreach ($newUpcoming->concat($newExpired) as $row) {
      $this->markSent($row);
    }

    $this->info("Reminder sent to {$recipient}: {$newUpcoming->count()} expiring, {$newExpired->count()} expired.");

    return Command::SUCCESS;
  }

  /**
   * Where the reminder goes: the address set in admin, otherwise the site's
   * "receive emails at" address.
   */
  private function recipient(): ?string
  {
    $settings = Basic::select('certificate_reminder_email', 'to_mail')->first();

    $configured = trim((string) ($settings->certificate_reminder_email ?? ''));

    return $configured !== '' ? $configured : trim((string) ($settings->to_mail ?? ''));
  }

  private function alreadySent(array $row): bool
  {
    return DB::table('certificate_expiry_reminders')
      ->where('source', $row['source'])
      ->where('record_id', $row['record_id'])
      ->whereDate('expiry_date', $row['expiry_date'])
      ->where('stage', $row['stage'])
      ->exists();
  }

  private function markSent(array $row): void
  {
    DB::table('certificate_expiry_reminders')->updateOrInsert(
      [
        'source' => $row['source'],
        'record_id' => $row['record_id'],
        'expiry_date' => $row['expiry_date'],
        'stage' => $row['stage'],
      ],
      [
        'sent_at' => Carbon::now(),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ]
    );
  }
}
