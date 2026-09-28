<?php

namespace App\Http\Helpers;

use App\Models\Services\ServiceBooking;
use App\Models\VendorDocument;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Everything that carries an expiry date and has to be chased before it lapses:
 *   - job certificates issued to customers (gas safety, EICR, EPC, PAT …)
 *   - vendor documents (insurance, registrations)
 *   - employee training records and employee documents (company HR)
 *
 * Used by the admin Compliance & Tasks page and by the daily reminder email
 * (certificates:remind), which warns Yes Trades a month before anything runs out.
 */
class CertificateExpiry
{
  /** Days before expiry that the reminder email goes out. */
  public const REMINDER_DAYS = 30;

  private const LIMIT = 200;

  /**
   * Certificates/documents expiring within the next $days days (not yet expired).
   */
  public static function expiringSoon(int $days = self::REMINDER_DAYS, ?int $languageId = null): Collection
  {
    $from = Carbon::today();
    $to = Carbon::today()->addDays($days);

    return self::collect($from, $to, $languageId, 'upcoming');
  }

  /**
   * Certificates/documents whose expiry date has already passed.
   */
  public static function expired(?int $languageId = null): Collection
  {
    return self::collect(null, Carbon::yesterday(), $languageId, 'expired');
  }

  public static function expiringSoonCount(int $days = self::REMINDER_DAYS): int
  {
    return self::expiringSoon($days)->count();
  }

  public static function expiredCount(): int
  {
    return self::expired()->count();
  }

  /**
   * @param  Carbon|null  $from  null = no lower bound (used for "already expired")
   */
  private static function collect(?Carbon $from, Carbon $to, ?int $languageId, string $stage): Collection
  {
    $rows = collect();

    // ── Job certificates issued to customers ────────────────────────────
    if (Schema::hasColumn('service_bookings', 'job_certificate_expiry')) {
      $bookings = ServiceBooking::query()
        ->whereNotNull('job_certificate_expiry')
        ->when($from, fn ($q) => $q->whereDate('job_certificate_expiry', '>=', $from->toDateString()))
        ->whereDate('job_certificate_expiry', '<=', $to->toDateString())
        ->with(['serviceContent' => function ($q) use ($languageId) {
          if ($languageId) {
            $q->where('language_id', $languageId);
          }
        }])
        ->orderBy('job_certificate_expiry')
        ->limit(self::LIMIT)
        ->get();

      foreach ($bookings as $booking) {
        $rows->push(self::row(
          'job_certificate',
          $booking->id,
          __('Job certificate'),
          optional($booking->serviceContent->first())->name ?: ($booking->service_name ?? __('Service')),
          trim(($booking->customer_name ?? $booking->site_name ?? '') . ' ' . ($booking->customer_phone ?? '')) ?: ($booking->customer_email ?? '—'),
          $booking->job_certificate_expiry,
          $stage,
          route('admin.appointment.details', ['id' => $booking->id, 'language' => request()->language ?? 'en'])
        ));
      }
    }

    // ── Vendor documents (insurance, registrations) ─────────────────────
    $documents = VendorDocument::query()
      ->whereNotNull('expiry_date')
      ->when($from, fn ($q) => $q->whereDate('expiry_date', '>=', $from->toDateString()))
      ->whereDate('expiry_date', '<=', $to->toDateString())
      ->whereHas('vendor')
      ->with(['vendor.vendor_info'])
      ->orderBy('expiry_date')
      ->limit(self::LIMIT)
      ->get();

    foreach ($documents as $doc) {
      $rows->push(self::row(
        'vendor_document',
        $doc->id,
        __('Vendor document'),
        $doc->type ?: __('Document'),
        optional($doc->vendor)->username ?? '—',
        $doc->expiry_date,
        $stage,
        null
      ));
    }

    // ── Employee training certificates (company HR) ─────────────────────
    if (Schema::hasTable('employee_training_records')) {
      $training = DB::table('employee_training_records as t')
        ->leftJoin('employees as e', 'e.id', '=', 't.employee_id')
        ->leftJoin('training_courses as c', 'c.id', '=', 't.training_course_id')
        ->whereNull('t.deleted_at')
        ->whereNotNull('t.expiry_date')
        ->when($from, fn ($q) => $q->whereDate('t.expiry_date', '>=', $from->toDateString()))
        ->whereDate('t.expiry_date', '<=', $to->toDateString())
        ->select('t.id', 't.expiry_date', 'c.name as course', 'e.first_name', 'e.last_name')
        ->orderBy('t.expiry_date')
        ->limit(self::LIMIT)
        ->get();

      foreach ($training as $item) {
        $rows->push(self::row(
          'employee_training',
          $item->id,
          __('Staff training'),
          $item->course ?: __('Training record'),
          trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? '')) ?: '—',
          $item->expiry_date,
          $stage,
          null
        ));
      }
    }

    // ── Employee documents (right to work, DBS, licences …) ─────────────
    if (Schema::hasTable('employee_documents')) {
      $empDocs = DB::table('employee_documents as d')
        ->leftJoin('employees as e', 'e.id', '=', 'd.employee_id')
        ->leftJoin('employee_document_types as dt', 'dt.id', '=', 'd.document_type_id')
        ->whereNull('d.deleted_at')
        ->whereNotNull('d.expiry_date')
        ->when($from, fn ($q) => $q->whereDate('d.expiry_date', '>=', $from->toDateString()))
        ->whereDate('d.expiry_date', '<=', $to->toDateString())
        ->select('d.id', 'd.expiry_date', 'd.title', 'dt.name as type', 'e.first_name', 'e.last_name')
        ->orderBy('d.expiry_date')
        ->limit(self::LIMIT)
        ->get();

      foreach ($empDocs as $item) {
        $rows->push(self::row(
          'employee_document',
          $item->id,
          __('Staff document'),
          ($item->title ?: $item->type) ?: __('Document'),
          trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? '')) ?: '—',
          $item->expiry_date,
          $stage,
          null
        ));
      }
    }

    return $rows->sortBy('expiry_date')->values();
  }

  private static function row(string $source, $id, string $typeLabel, ?string $title, ?string $who, $expiry, string $stage, ?string $url): array
  {
    $expiryDate = $expiry instanceof Carbon ? $expiry : Carbon::parse($expiry);

    return [
      'source' => $source,
      'record_id' => (int) $id,
      'type' => $typeLabel,
      'title' => $title ?: '—',
      'who' => $who ?: '—',
      'expiry_date' => $expiryDate->toDateString(),
      'expiry_display' => $expiryDate->format('d M Y'),
      'days_left' => Carbon::today()->diffInDays($expiryDate, false),
      'stage' => $stage,
      'url' => $url,
    ];
  }
}
