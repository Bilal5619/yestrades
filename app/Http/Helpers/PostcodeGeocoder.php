<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Converts UK postcodes (full postcodes or bare outcodes like "EH12") to
 * coordinates using the free postcodes.io API. Results are cached in the
 * postcode_geocodes table so we never hit the API twice for the same postcode.
 *
 * Vendors never enter latitude/longitude directly — the backend derives them
 * from the Service Area Postcode they provide.
 */
class PostcodeGeocoder
{
  /**
   * Lenient UK postcode / outcode validation. Accepts a full postcode
   * (e.g. "EH12 9DH") or just the outcode (e.g. "EH12", "G1").
   */
  public static function isValidUkPostcode(?string $postcode): bool
  {
    if (empty($postcode)) {
      return false;
    }

    $value = strtoupper(trim($postcode));

    // Full UK postcode
    $full = '/^[A-Z]{1,2}\d[A-Z\d]?\s*\d[A-Z]{2}$/';
    // Outcode only (area + district)
    $outcode = '/^[A-Z]{1,2}\d[A-Z\d]?$/';

    return (bool) (preg_match($full, $value) || preg_match($outcode, $value));
  }

  /**
   * Normalize to uppercase, no spaces — used as the cache key.
   */
  public static function normalize(?string $postcode): string
  {
    return strtoupper(preg_replace('/\s+/', '', (string) $postcode));
  }

  /**
   * Returns ['latitude' => float, 'longitude' => float] or null on failure.
   * Never throws — a failed lookup must not block saving the record.
   */
  public static function lookup(?string $postcode): ?array
  {
    if (!self::isValidUkPostcode($postcode)) {
      return null;
    }

    $key = self::normalize($postcode);

    $cached = DB::table('postcode_geocodes')->where('postcode', $key)->first();
    if ($cached) {
      if ($cached->latitude === null || $cached->longitude === null) {
        return null;
      }
      return ['latitude' => (float) $cached->latitude, 'longitude' => (float) $cached->longitude];
    }

    $coords = self::fetchFromApi($key);

    // Cache the result (including misses, stored as null lat/lng) to avoid retries.
    try {
      DB::table('postcode_geocodes')->updateOrInsert(
        ['postcode' => $key],
        [
          'latitude' => $coords['latitude'] ?? null,
          'longitude' => $coords['longitude'] ?? null,
          'updated_at' => now(),
          'created_at' => now(),
        ]
      );
    } catch (\Throwable $e) {
      // ignore cache write failures
    }

    return $coords;
  }

  private static function fetchFromApi(string $normalized): ?array
  {
    // Decide whether this looks like a full postcode (has an inward code) or an outcode.
    $isFull = (bool) preg_match('/^[A-Z]{1,2}\d[A-Z\d]?\d[A-Z]{2}$/', $normalized);

    try {
      if ($isFull) {
        $response = Http::timeout(6)->get('https://api.postcodes.io/postcodes/' . urlencode($normalized));
        if ($response->ok() && ($result = $response->json('result'))) {
          return self::coordsFrom($result);
        }
        // Fall back to the outcode portion if the full lookup misses.
        $outcode = self::outcodeOf($normalized);
        if ($outcode) {
          return self::fetchOutcode($outcode);
        }
        return null;
      }

      return self::fetchOutcode($normalized);
    } catch (\Throwable $e) {
      return null;
    }
  }

  private static function fetchOutcode(string $outcode): ?array
  {
    $response = Http::timeout(6)->get('https://api.postcodes.io/outcodes/' . urlencode($outcode));
    if ($response->ok() && ($result = $response->json('result'))) {
      return self::coordsFrom($result);
    }
    return null;
  }

  private static function coordsFrom(array $result): ?array
  {
    if (isset($result['latitude'], $result['longitude'])) {
      return [
        'latitude' => (float) $result['latitude'],
        'longitude' => (float) $result['longitude'],
      ];
    }
    return null;
  }

  private static function outcodeOf(string $normalized): ?string
  {
    if (preg_match('/^([A-Z]{1,2}\d[A-Z\d]?)\d[A-Z]{2}$/', $normalized, $m)) {
      return $m[1];
    }
    return null;
  }

  /**
   * Great-circle distance in miles between two coordinate pairs.
   */
  public static function distanceMiles(float $lat1, float $lon1, float $lat2, float $lon2): float
  {
    $earthRadius = 3958.8; // miles
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat / 2) ** 2
      + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earthRadius * $c;
  }
}
