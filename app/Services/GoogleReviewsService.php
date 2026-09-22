<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Pulls the business rating + latest reviews from the Google Places API (New).
 * Configured in Admin > Basic Settings > Plugins > Google Reviews.
 * Results are cached for 12 hours so the API is not hit on every page view.
 */
class GoogleReviewsService
{
  const CACHE_KEY = 'google_reviews_v1';
  const CACHE_TTL = 60 * 60 * 12;

  /**
   * @return array{rating: ?float, count: ?int, url: ?string, reviews: array}|null
   */
  public static function get(): ?array
  {
    $settings = DB::table('basic_settings')
      ->where('uniqid', 12345)
      ->select('google_places_api_key', 'google_place_id', 'google_reviews_status')
      ->first();

    if (!$settings || (int) ($settings->google_reviews_status ?? 0) !== 1
      || empty($settings->google_places_api_key) || empty($settings->google_place_id)) {
      return null;
    }

    return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () use ($settings) {
      return self::fetch($settings->google_places_api_key, $settings->google_place_id) ?? [];
    }) ?: null;
  }

  public static function forget(): void
  {
    Cache::forget(self::CACHE_KEY);
  }

  private static function fetch(string $apiKey, string $placeId): ?array
  {
    try {
      $response = Http::timeout(8)
        ->withHeaders([
          'X-Goog-Api-Key' => $apiKey,
          'X-Goog-FieldMask' => 'rating,userRatingCount,googleMapsUri,reviews',
        ])
        ->get('https://places.googleapis.com/v1/places/' . rawurlencode($placeId), ['languageCode' => 'en-GB']);

      if (!$response->ok()) {
        Log::warning('Google Places reviews request failed', ['status' => $response->status(), 'body' => mb_substr($response->body(), 0, 500)]);
        return null;
      }

      $data = $response->json();
      $reviews = collect($data['reviews'] ?? [])
        ->filter(fn ($r) => !empty(trim($r['text']['text'] ?? '')))
        ->map(fn ($r) => [
          'name' => $r['authorAttribution']['displayName'] ?? 'Google user',
          'photo' => $r['authorAttribution']['photoUri'] ?? null,
          'rating' => (int) ($r['rating'] ?? 5),
          'text' => trim($r['text']['text']),
          'time' => $r['relativePublishTimeDescription'] ?? null,
          'url' => $r['googleMapsUri'] ?? null,
        ])
        ->sortByDesc('rating')
        ->values()
        ->all();

      return [
        'rating' => isset($data['rating']) ? round((float) $data['rating'], 1) : null,
        'count' => isset($data['userRatingCount']) ? (int) $data['userRatingCount'] : null,
        'url' => $data['googleMapsUri'] ?? null,
        'reviews' => $reviews,
      ];
    } catch (\Throwable $e) {
      Log::warning('Google Places reviews exception: ' . $e->getMessage());
      return null;
    }
  }
}
