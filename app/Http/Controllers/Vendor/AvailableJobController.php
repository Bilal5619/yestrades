<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Helpers\PostcodeGeocoder;
use App\Models\Language;
use App\Models\Services\ServiceBooking;
use App\Models\Services\Services;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AvailableJobController extends Controller
{
  // Jobs farther than this from the vendor's service area are hidden (miles).
  private const RADIUS_MILES = 30;

  public function index()
  {
    $vendorId = Auth::guard('vendor')->id();
    $language = Language::where('is_default', 1)->first();
    $languageId = optional($language)->id;

    $vendor = Vendor::with(['serviceCategories', 'vendor_info'])->findOrFail($vendorId);
    $categoryIds = $vendor->serviceCategories->pluck('id')->map(fn ($id) => (int) $id)->all();

    if (empty($categoryIds)) {
      return view('vendors.available-jobs.index', [
        'jobs' => collect(),
        'needsCategories' => true,
      ]);
    }

    // Origins to measure distance from = the vendor's service postcodes, falling
    // back to the postcode they gave at signup.
    $origins = $this->vendorOrigins($vendor);

    // Unassigned, pending jobs in one of the vendor's categories.
    $bookings = ServiceBooking::with(['service', 'serviceContent' => function ($q) use ($languageId) {
      $q->where('language_id', $languageId);
    }])
      ->where('vendor_id', 0)
      ->where('order_status', 'pending')
      ->whereHas('serviceContent', function ($q) use ($categoryIds, $languageId) {
        $q->where('language_id', $languageId)->whereIn('category_id', $categoryIds);
      })
      ->orderByDesc('id')
      ->get();

    $jobs = $bookings->map(function (ServiceBooking $booking) use ($origins) {
      $distance = null;
      $coords = PostcodeGeocoder::lookup($booking->site_zip_code);
      if ($coords && !empty($origins)) {
        $distance = collect($origins)
          ->map(fn ($o) => PostcodeGeocoder::distanceMiles($o['latitude'], $o['longitude'], $coords['latitude'], $coords['longitude']))
          ->min();
      }
      $booking->setAttribute('match_distance', $distance);
      return $booking;
    });

    // If we have origins, drop jobs clearly outside the radius (keep ones we
    // couldn't geocode so nothing relevant is silently lost).
    if (!empty($origins)) {
      $jobs = $jobs->filter(function ($job) {
        return $job->match_distance === null || $job->match_distance <= self::RADIUS_MILES;
      });
    }

    // Nearest first; unknown-distance jobs last.
    $jobs = $jobs->sortBy(fn ($job) => $job->match_distance ?? PHP_INT_MAX)->values();

    return view('vendors.available-jobs.index', [
      'jobs' => $jobs,
      'needsCategories' => false,
    ]);
  }

  public function claim(Request $request, $id)
  {
    $vendorId = Auth::guard('vendor')->id();
    $claimed = false;

    DB::transaction(function () use ($id, $vendorId, &$claimed) {
      $booking = ServiceBooking::where('id', $id)
        ->where('vendor_id', 0)
        ->where('order_status', 'pending')
        ->lockForUpdate()
        ->first();

      if (!$booking) {
        return; // already taken / not claimable
      }

      $booking->update(['vendor_id' => $vendorId]);
      Vendor::where('id', $vendorId)->increment('total_appointment');
      $claimed = true;
    });

    if (!$claimed) {
      return redirect()->back()->with('warning', 'This job is no longer available.');
    }

    return redirect()
      ->route('vendor.pending_appointment', ['language' => request()->language])
      ->with('success', 'Job claimed successfully. It is now in your pending appointments.');
  }

  /**
   * Coordinates for the vendor's service postcodes (fallback: signup postcode).
   */
  private function vendorOrigins(Vendor $vendor): array
  {
    $origins = Services::where('vendor_id', $vendor->id)
      ->whereNotNull('latitude')
      ->whereNotNull('longitude')
      ->get(['latitude', 'longitude'])
      ->map(fn ($s) => ['latitude' => (float) $s->latitude, 'longitude' => (float) $s->longitude])
      ->all();

    if (empty($origins) && $vendor->vendor_info && !empty($vendor->vendor_info->postcode)) {
      $coords = PostcodeGeocoder::lookup($vendor->vendor_info->postcode);
      if ($coords) {
        $origins[] = $coords;
      }
    }

    return $origins;
  }
}
