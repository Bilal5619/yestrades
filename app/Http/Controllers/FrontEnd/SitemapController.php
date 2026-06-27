<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\CustomPage\Page;
use App\Models\CustomPage\PageContent;
use App\Models\Language;
use App\Models\Services\ServiceCategory;
use App\Models\Services\ServiceContent;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
  private string $baseUrl = 'https://11done.co.uk';

  public function index(): Response
  {
    $urls = collect([
      $this->baseUrl . '/',
      $this->baseUrl . '/services',
      $this->baseUrl . '/landlords',
      $this->baseUrl . '/contact',
      $this->baseUrl . '/privacy-policy',
      $this->baseUrl . '/cookie-policy',
    ]);

    $languageId = optional(Language::where('is_default', 1)->first())->id;

    $customPages = Page::query()
      ->join('page_contents', 'pages.id', '=', 'page_contents.page_id')
      ->where('pages.status', 1)
      ->when($languageId, fn ($query) => $query->where('page_contents.language_id', $languageId))
      ->pluck('page_contents.slug')
      ->filter(fn ($slug) => $this->isPublicCustomSlug($slug))
      ->map(fn ($slug) => $this->baseUrl . '/' . ltrim($slug, '/'));

    $categoryUrls = ServiceCategory::query()
      ->where('status', 1)
      ->when($languageId, fn ($query) => $query->where('language_id', $languageId))
      ->orderBy('serial_number')
      ->orderBy('name')
      ->pluck('id')
      ->map(fn ($id) => $this->baseUrl . '/services?category_id=' . $id);

    $serviceUrls = ServiceContent::query()
      ->join('services', 'services.id', '=', 'service_contents.service_id')
      ->where('services.status', 1)
      ->when($languageId, fn ($query) => $query->where('service_contents.language_id', $languageId))
      ->whereNotNull('service_contents.slug')
      ->orderByDesc('services.id')
      ->limit(500)
      ->get(['services.id', 'service_contents.slug'])
      ->map(fn ($service) => $this->baseUrl . '/services/details/' . $service->slug . '/' . $service->id);

    $urls = $urls
      ->merge($customPages)
      ->merge($categoryUrls)
      ->merge($serviceUrls)
      ->unique()
      ->values();

    $xml = view('frontend.sitemap', compact('urls'))->render();

    return response($xml, 200)
      ->header('Content-Type', 'application/xml; charset=UTF-8');
  }

  private function isPublicCustomSlug(?string $slug): bool
  {
    if (empty($slug)) {
      return false;
    }

    return !in_array($slug, [
      'admin',
      'cart',
      'checkout',
      'dashboard',
      'login',
      'logout',
      'signup',
      'vendor',
      'user',
    ], true);
  }
}
