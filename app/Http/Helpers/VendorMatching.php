<?php

namespace App\Http\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Works out which vendors are eligible to be assigned to a given job, based on
 * the job's service category. A vendor is eligible when they either:
 *   - selected that service category during sign up (vendor_service_categories), or
 *   - already offer a service in that category ("serve the service").
 *
 * Categories are matched by slug so the match holds across languages.
 */
class VendorMatching
{
  /**
   * [appointmentId => [eligibleVendorId, ...]] for a page of appointments.
   * If a job's service has no category information, every vendor is returned
   * (so assignment is never accidentally blocked).
   */
  public static function eligibleMapForAppointments(Collection $appointments, Collection $vendors): array
  {
    if ($appointments->isEmpty() || $vendors->isEmpty()) {
      return [];
    }

    $vendorIds = $vendors->pluck('id')->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();
    $serviceIds = $appointments->pluck('service_id')->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();

    if (empty($vendorIds)) {
      return [];
    }

    $vendorSlugs = self::vendorSlugSets($vendorIds);
    $serviceSlugs = self::serviceSlugSets($serviceIds);

    $map = [];
    foreach ($appointments as $appointment) {
      $serviceId = (int) $appointment->service_id;
      $needed = $serviceSlugs[$serviceId] ?? [];

      if (empty($needed)) {
        // No category to match against -> don't hide anyone.
        $map[$appointment->id] = $vendorIds;
        continue;
      }

      $eligible = [];
      foreach ($vendorIds as $vendorId) {
        $have = $vendorSlugs[$vendorId] ?? [];
        if (!empty($have) && !empty(array_intersect_key($needed, $have))) {
          $eligible[] = $vendorId;
        }
      }
      $map[$appointment->id] = $eligible;
    }

    return $map;
  }

  /**
   * Convenience for a single appointment.
   */
  public static function eligibleVendorIdsFor($appointment, Collection $vendors): array
  {
    $map = self::eligibleMapForAppointments(collect([$appointment]), $vendors);
    return $map[$appointment->id] ?? [];
  }

  /**
   * [vendorId => [slug => true, ...]] from sign-up categories + owned services.
   */
  private static function vendorSlugSets(array $vendorIds): array
  {
    $sets = [];

    $signup = DB::table('vendor_service_categories as vsc')
      ->join('service_categories as sc', 'sc.id', '=', 'vsc.service_category_id')
      ->whereIn('vsc.vendor_id', $vendorIds)
      ->select('vsc.vendor_id', 'sc.slug')
      ->get();
    foreach ($signup as $row) {
      $sets[(int) $row->vendor_id][$row->slug] = true;
    }

    $owned = DB::table('services as s')
      ->join('service_contents as scn', 'scn.service_id', '=', 's.id')
      ->join('service_categories as sc', 'sc.id', '=', 'scn.category_id')
      ->whereIn('s.vendor_id', $vendorIds)
      ->whereNotNull('scn.category_id')
      ->select('s.vendor_id', 'sc.slug')
      ->get();
    foreach ($owned as $row) {
      $sets[(int) $row->vendor_id][$row->slug] = true;
    }

    return $sets;
  }

  /**
   * [serviceId => [slug => true, ...]] for the booked services' categories.
   */
  private static function serviceSlugSets(array $serviceIds): array
  {
    if (empty($serviceIds)) {
      return [];
    }

    $sets = [];
    $rows = DB::table('service_contents as scn')
      ->join('service_categories as sc', 'sc.id', '=', 'scn.category_id')
      ->whereIn('scn.service_id', $serviceIds)
      ->whereNotNull('scn.category_id')
      ->select('scn.service_id', 'sc.slug')
      ->get();
    foreach ($rows as $row) {
      $sets[(int) $row->service_id][$row->slug] = true;
    }

    return $sets;
  }
}
