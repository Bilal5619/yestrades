<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\VisitorPageView;
use App\Models\WebsiteVisitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VisitorTrackingController extends Controller
{
  public function store(Request $request)
  {
    $validated = $request->validate([
      'visitor_uuid' => ['required', 'string', 'max:100'],
      'page_url' => ['required', 'string', 'max:2048'],
      'page_title' => ['nullable', 'string', 'max:255'],
      'referrer' => ['nullable', 'string', 'max:2048'],
      'utm_source' => ['nullable', 'string', 'max:255'],
      'utm_medium' => ['nullable', 'string', 'max:255'],
      'utm_campaign' => ['nullable', 'string', 'max:255'],
      'screen_width' => ['nullable', 'integer'],
      'screen_height' => ['nullable', 'integer'],
    ]);

    $now = now();
    $userId = Auth::guard('web')->check() ? Auth::guard('web')->id() : null;
    $userAgent = (string) $request->userAgent();
    $device = $this->parseUserAgent($userAgent);

    $visitor = WebsiteVisitor::firstOrNew([
      'visitor_uuid' => $validated['visitor_uuid'],
    ]);

    if (!$visitor->exists) {
      $visitor->first_visited_at = $now;
    }

    $visitor->fill([
      'user_id' => $userId ?: $visitor->user_id,
      'ip_address' => $request->ip(),
      'country' => $this->headerValue($request, ['CF-IPCountry', 'X-Country-Code', 'X-Geo-Country']),
      'city' => $this->headerValue($request, ['CF-IPCity', 'X-City', 'X-Geo-City']),
      'device_type' => $device['device_type'],
      'browser' => $device['browser'],
      'os' => $device['os'],
      'referrer' => $validated['referrer'] ?? null,
      'utm_source' => $validated['utm_source'] ?? $visitor->utm_source,
      'utm_medium' => $validated['utm_medium'] ?? $visitor->utm_medium,
      'utm_campaign' => $validated['utm_campaign'] ?? $visitor->utm_campaign,
      'last_activity_at' => $now,
    ]);
    $visitor->save();

    VisitorPageView::create([
      'visitor_uuid' => $visitor->visitor_uuid,
      'user_id' => $userId,
      'page_url' => $validated['page_url'],
      'page_title' => $validated['page_title'] ?? null,
      'referrer' => $validated['referrer'] ?? null,
      'visited_at' => $now,
    ]);

    return response()->json(['success' => true]);
  }

  private function headerValue(Request $request, array $headers): ?string
  {
    foreach ($headers as $header) {
      $value = $request->headers->get($header);

      if (!empty($value)) {
        return Str::limit((string) $value, 255, '');
      }
    }

    return null;
  }

  private function parseUserAgent(string $userAgent): array
  {
    $agent = Str::lower($userAgent);

    $deviceType = 'Desktop';
    if (Str::contains($agent, ['mobile', 'iphone', 'android'])) {
      $deviceType = 'Mobile';
    } elseif (Str::contains($agent, ['ipad', 'tablet'])) {
      $deviceType = 'Tablet';
    }

    $browser = 'Unknown';
    if (Str::contains($agent, 'edg/')) {
      $browser = 'Edge';
    } elseif (Str::contains($agent, 'chrome/')) {
      $browser = 'Chrome';
    } elseif (Str::contains($agent, 'safari/') && !Str::contains($agent, 'chrome/')) {
      $browser = 'Safari';
    } elseif (Str::contains($agent, 'firefox/')) {
      $browser = 'Firefox';
    }

    $os = 'Unknown';
    if (Str::contains($agent, 'windows')) {
      $os = 'Windows';
    } elseif (Str::contains($agent, ['iphone', 'ipad', 'ios'])) {
      $os = 'iOS';
    } elseif (Str::contains($agent, 'android')) {
      $os = 'Android';
    } elseif (Str::contains($agent, 'mac os')) {
      $os = 'macOS';
    } elseif (Str::contains($agent, 'linux')) {
      $os = 'Linux';
    }

    return [
      'device_type' => $deviceType,
      'browser' => $browser,
      'os' => $os,
    ];
  }
}
