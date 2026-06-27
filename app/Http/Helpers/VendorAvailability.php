<?php

namespace App\Http\Helpers;

use App\Models\Services\ServiceBooking;
use App\Models\VendorAvailabilityException;
use App\Models\VendorWeeklyHour;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Single source of truth for vendor availability.
 *
 * A vendor is "unavailable" for a given appointment slot when ANY of these hold:
 *   1. They already have another non-rejected appointment on the same date whose time overlaps.
 *   2. Their configured weekly schedule marks that weekday closed, or the slot falls
 *      outside their working hours (only when they have configured a weekly schedule).
 *   3. They have a date exception (full-day block, or a blocked time range that overlaps).
 *
 * Vendor id 0 (admin / unassigned) is never unavailable.
 */
class VendorAvailability
{
  /**
   * Human-readable reasons why the vendor is unavailable for this slot.
   * Empty array => the vendor is available.
   */
  public static function reasons($vendorId, $bookingDate, $startTime, $endTime, ?int $excludeBookingId = null): array
  {
    $vendorId = (int) $vendorId;
    $date = self::normalizeDate($bookingDate);

    if ($vendorId <= 0 || $date === null) {
      return [];
    }

    $weekly = VendorWeeklyHour::where('vendor_id', $vendorId)->get();
    $exceptions = VendorAvailabilityException::where('vendor_id', $vendorId)
      ->whereDate('date', $date)
      ->get();
    $conflicts = self::appointmentConflicts($vendorId, $date, $startTime, $endTime, $excludeBookingId);

    return self::evaluate($date, $startTime, $endTime, $weekly, $exceptions, $conflicts);
  }

  public static function isBusy($vendorId, $bookingDate, $startTime, $endTime, ?int $excludeBookingId = null): bool
  {
    return !empty(self::reasons($vendorId, $bookingDate, $startTime, $endTime, $excludeBookingId));
  }

  /**
   * Out of the given vendors, return the ids that are unavailable for this appointment's slot.
   * Batch-loads schedules/exceptions/bookings to avoid per-vendor queries.
   */
  public static function busyVendorIdsFor(ServiceBooking $appointment, Collection $vendors): array
  {
    $map = self::busyMapForAppointments(collect([$appointment]), $vendors);

    return $map[$appointment->id] ?? [];
  }

  /**
   * Build a map [appointmentId => [busyVendorId, ...]] for a page of appointments,
   * batch-loading every dependency up front.
   */
  public static function busyMapForAppointments(Collection $appointments, Collection $vendors): array
  {
    if ($appointments->isEmpty() || $vendors->isEmpty()) {
      return [];
    }

    $vendorIds = $vendors->pluck('id')->map(fn ($id) => (int) $id)->filter()->unique()->all();

    if (empty($vendorIds)) {
      return [];
    }

    $dates = $appointments
      ->pluck('booking_date')
      ->map(fn ($date) => self::normalizeDate($date))
      ->filter()
      ->unique()
      ->values()
      ->all();

    if (empty($dates)) {
      return [];
    }

    $weeklyByVendor = VendorWeeklyHour::whereIn('vendor_id', $vendorIds)
      ->get()
      ->groupBy('vendor_id');

    $exceptionsByVendor = VendorAvailabilityException::whereIn('vendor_id', $vendorIds)
      ->whereIn('date', $dates)
      ->get()
      ->groupBy('vendor_id');

    $bookings = ServiceBooking::whereIn('vendor_id', $vendorIds)
      ->whereIn(\DB::raw('DATE(booking_date)'), $dates)
      ->where('order_status', '!=', 'rejected')
      ->get(['id', 'vendor_id', 'booking_date', 'start_date', 'end_date'])
      ->groupBy('vendor_id');

    $map = [];

    foreach ($appointments as $appointment) {
      $date = self::normalizeDate($appointment->booking_date);
      $busy = [];

      if ($date === null) {
        $map[$appointment->id] = $busy;
        continue;
      }

      foreach ($vendorIds as $vendorId) {
        $weekly = $weeklyByVendor->get($vendorId, collect());
        $exceptions = ($exceptionsByVendor->get($vendorId, collect()))
          ->filter(fn ($exception) => self::normalizeDate($exception->date) === $date)
          ->values();
        $conflicts = ($bookings->get($vendorId, collect()))
          ->filter(function (ServiceBooking $booking) use ($appointment, $date) {
            return (int) $booking->id !== (int) $appointment->id
              && self::normalizeDate($booking->booking_date) === $date
              && self::timesOverlap($appointment->start_date, $appointment->end_date, $booking->start_date, $booking->end_date);
          })
          ->values();

        $reasons = self::evaluate($date, $appointment->start_date, $appointment->end_date, $weekly, $exceptions, $conflicts);

        if (!empty($reasons)) {
          $busy[] = (int) $vendorId;
        }
      }

      $map[$appointment->id] = $busy;
    }

    return $map;
  }

  /**
   * Every current availability conflict for an ASSIGNED vendor on an upcoming,
   * active job: double-bookings (overlapping appointments), self-marked
   * unavailability (date exceptions), and weekly-schedule clashes (day closed /
   * outside working hours).
   *
   * Returns [appointmentId => [reason, ...]] containing only conflicting jobs.
   * Powers the admin availability-compliance report, its sidebar badge and the
   * dashboard card.
   */
  public static function conflictMap(): array
  {
    $today = Carbon::today()->toDateString();

    $candidates = ServiceBooking::where('vendor_id', '>', 0)
      ->whereNotIn('order_status', ['rejected', 'completed'])
      ->whereDate('booking_date', '>=', $today)
      ->get(['id', 'vendor_id', 'booking_date', 'start_date', 'end_date']);

    if ($candidates->isEmpty()) {
      return [];
    }

    $vendorIds = $candidates->pluck('vendor_id')->map(fn ($id) => (int) $id)->filter()->unique()->all();
    $dates = $candidates->pluck('booking_date')->map(fn ($d) => self::normalizeDate($d))->filter()->unique()->all();

    $weeklyByVendor = VendorWeeklyHour::whereIn('vendor_id', $vendorIds)
      ->get()
      ->groupBy('vendor_id');

    $exceptionsByVendor = VendorAvailabilityException::whereIn('vendor_id', $vendorIds)
      ->whereIn('date', $dates)
      ->get()
      ->groupBy('vendor_id');

    // All non-rejected bookings for these vendors on these dates, used to detect
    // overlapping double-bookings (not limited to the candidate set).
    $bookingsByVendor = ServiceBooking::whereIn('vendor_id', $vendorIds)
      ->whereIn(\DB::raw('DATE(booking_date)'), $dates)
      ->where('order_status', '!=', 'rejected')
      ->get(['id', 'vendor_id', 'booking_date', 'start_date', 'end_date'])
      ->groupBy('vendor_id');

    $map = [];

    foreach ($candidates as $appointment) {
      $vendorId = (int) $appointment->vendor_id;
      $date = self::normalizeDate($appointment->booking_date);

      if ($date === null) {
        continue;
      }

      $weekly = $weeklyByVendor->get($vendorId, collect());
      $exceptions = ($exceptionsByVendor->get($vendorId, collect()))
        ->filter(fn ($exception) => self::normalizeDate($exception->date) === $date)
        ->values();
      $conflicts = ($bookingsByVendor->get($vendorId, collect()))
        ->filter(function (ServiceBooking $booking) use ($appointment, $date) {
          return (int) $booking->id !== (int) $appointment->id
            && self::normalizeDate($booking->booking_date) === $date
            && self::timesOverlap($appointment->start_date, $appointment->end_date, $booking->start_date, $booking->end_date);
        })
        ->values();

      $reasons = self::evaluate($date, $appointment->start_date, $appointment->end_date, $weekly, $exceptions, $conflicts);

      if (!empty($reasons)) {
        $map[$appointment->id] = $reasons;
      }
    }

    return $map;
  }

  /**
   * Count of unresolved conflicts, for the sidebar badge and dashboard card.
   */
  public static function unresolvedConflictCount(): int
  {
    return count(self::conflictMap());
  }

  /**
   * Core decision logic shared by single-vendor and batch paths.
   */
  private static function evaluate($date, $startTime, $endTime, Collection $weekly, Collection $exceptions, Collection $conflicts): array
  {
    $reasons = [];

    foreach ($conflicts as $booking) {
      $when = trim(self::formatTime($booking->start_date) . ' - ' . self::formatTime($booking->end_date), ' -');
      $reasons[] = $when !== ''
        ? 'Already booked (' . $when . ')'
        : 'Already booked on this date';
    }

    // Date exceptions (vendor-marked unavailability).
    foreach ($exceptions as $exception) {
      if ($exception->is_full_day || self::toMinutes($exception->start_time) === null || self::toMinutes($exception->end_time) === null) {
        $reasons[] = trim('Marked unavailable' . ($exception->reason ? ' (' . $exception->reason . ')' : ''));
      } elseif (self::timesOverlap($startTime, $endTime, $exception->start_time, $exception->end_time)) {
        $reasons[] = trim('Marked unavailable ' . self::formatTime($exception->start_time) . ' - ' . self::formatTime($exception->end_time));
      }
    }

    // Weekly schedule, only enforced when the vendor has configured one.
    if ($weekly->isNotEmpty()) {
      $dayOfWeek = Carbon::parse($date)->dayOfWeek;
      $day = $weekly->firstWhere('day_of_week', $dayOfWeek);

      if ($day && !$day->is_open) {
        $reasons[] = 'Not available on ' . Carbon::parse($date)->format('l') . 's';
      } elseif ($day && $day->is_open) {
        $slotStart = self::toMinutes($startTime);
        $slotEnd = self::toMinutes($endTime);
        $openStart = self::toMinutes($day->start_time);
        $openEnd = self::toMinutes($day->end_time);

        if ($slotStart !== null && $slotEnd !== null && $openStart !== null && $openEnd !== null
          && ($slotStart < $openStart || $slotEnd > $openEnd)) {
          $reasons[] = 'Outside working hours (' . self::formatTime($day->start_time) . ' - ' . self::formatTime($day->end_time) . ')';
        }
      }
    }

    return array_values(array_unique($reasons));
  }

  private static function appointmentConflicts(int $vendorId, string $date, $startTime, $endTime, ?int $excludeBookingId): Collection
  {
    return ServiceBooking::where('vendor_id', $vendorId)
      ->whereDate('booking_date', $date)
      ->where('order_status', '!=', 'rejected')
      ->when($excludeBookingId, fn ($query, $excludeBookingId) => $query->where('id', '!=', $excludeBookingId))
      ->get()
      ->filter(fn (ServiceBooking $booking) => self::timesOverlap($startTime, $endTime, $booking->start_date, $booking->end_date))
      ->values();
  }

  /**
   * Two time ranges overlap when start_a < end_b AND end_a > start_b.
   * When either range has no parseable time, fall back to a whole-day conflict.
   */
  private static function timesOverlap($startA, $endA, $startB, $endB): bool
  {
    $aStart = self::toMinutes($startA);
    $aEnd = self::toMinutes($endA);
    $bStart = self::toMinutes($startB);
    $bEnd = self::toMinutes($endB);

    if ($aStart === null || $aEnd === null || $bStart === null || $bEnd === null) {
      return true;
    }

    return $aStart < $bEnd && $aEnd > $bStart;
  }

  private static function toMinutes($time): ?int
  {
    if (empty($time)) {
      return null;
    }

    try {
      $parsed = Carbon::parse($time);

      return $parsed->hour * 60 + $parsed->minute;
    } catch (\Exception $exception) {
      return null;
    }
  }

  private static function formatTime($time): string
  {
    if (empty($time)) {
      return '';
    }

    try {
      return Carbon::parse($time)->format('g:i A');
    } catch (\Exception $exception) {
      return (string) $time;
    }
  }

  private static function normalizeDate($date): ?string
  {
    if (empty($date)) {
      return null;
    }

    try {
      return Carbon::parse($date)->toDateString();
    } catch (\Exception $exception) {
      return null;
    }
  }
}
