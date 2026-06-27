<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Journal\Blog;
use App\Models\Journal\BlogCategory;
use App\Models\Journal\BlogInformation;
use App\Models\Language;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Mews\Purifier\Facades\Purifier;

class AutoSeoWebhookController extends Controller
{
  public function health(): JsonResponse
  {
    return response()->json([
      'success' => true,
      'message' => 'AutoSEO webhook endpoint is active.',
    ], 200);
  }

  public function store(Request $request): JsonResponse
  {
    $configuredSecret = (string) config('services.autoseo.webhook_secret');
    $providedSecret = (string) ($request->bearerToken() ?: $request->header('X-AUTOSEO-SECRET'));

    if ($configuredSecret === '') {
      return response()->json([
        'message' => 'AutoSEO webhook secret is not configured.'
      ], 503);
    }

    if ($providedSecret === '' || !hash_equals($configuredSecret, $providedSecret)) {
      return response()->json([
        'message' => 'Unauthorized.'
      ], 401);
    }

    if (!$this->hasValidSignature($request, $configuredSecret)) {
      return response()->json([
        'message' => 'Unauthorized.'
      ], 401);
    }

    $payload = $this->normalizePayload($request->all());
    $payload['event'] = $payload['event'] ?: $request->header('X-AutoSEO-Event');

    if (($payload['event'] ?? null) === 'test') {
      return response()->json([
        'url' => 'https://11done.co.uk/test',
      ], 200);
    }

    $validator = Validator::make($payload, [
      'event' => 'required|string|in:article.published,article.updated',
      'title' => 'required|string|max:255',
      'slug' => 'required|string|max:255',
      'content' => 'required|string|min:30',
      'category' => 'nullable|string|max:255',
      'meta_description' => 'nullable|string|max:1000',
      'meta_keywords' => 'nullable',
      'featured_image' => 'nullable|string|max:2048',
      'image_alt' => 'nullable|string|max:255',
      'infographic_image' => 'nullable|string|max:2048',
      'published_at' => 'nullable|date',
      'author' => 'nullable|string|max:255',
      'language' => 'nullable|string|max:20',
      'source_article_id' => 'nullable|string|max:255',
      'status' => 'nullable|string|max:255',
      'published_url' => 'nullable|string|max:2048',
      'faq_schema' => 'nullable',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'The article payload is invalid.',
        'errors' => $validator->errors(),
      ], 422);
    }

    $article = $validator->validated();

    try {
      $result = DB::transaction(function () use ($article) {
        $language = $this->resolveLanguage($article['language'] ?? null);
        $category = $this->resolveCategory($language, $article['category'] ?? null);
        $slug = $this->resolveSlug($article['slug'], $article['title']);
        $publishedAt = $this->resolvePublishedAt($article['published_at'] ?? null);

        $blogInformation = BlogInformation::query()
          ->where('language_id', $language->id)
          ->where('slug', $slug)
          ->first();

        if (!$blogInformation) {
          $blogInformation = BlogInformation::query()
            ->where('language_id', $language->id)
            ->where('title', $article['title'])
            ->first();
        }

        $blog = $blogInformation ? $blogInformation->blog : new Blog();
        $blog->image = $this->resolveImage($article['featured_image'] ?? null, $blog->image ?? null);
        $blog->serial_number = $blog->serial_number ?: ((int) Blog::max('serial_number') + 1);

        if (!$blog->exists) {
          $blog->created_at = $publishedAt;
        }

        $blog->updated_at = now();
        $blog->save();

        if (!$blogInformation) {
          $blogInformation = new BlogInformation();
          $blogInformation->language_id = $language->id;
          $blogInformation->blog_id = $blog->id;
        }

        $blogInformation->blog_category_id = $category->id;
        $blogInformation->title = $article['title'];
        $blogInformation->slug = $slug;
        $blogInformation->author = $article['author'] ?? 'AutoSEO';
        $blogInformation->content = Purifier::clean($article['content'], 'youtube');
        $blogInformation->meta_keywords = $this->resolveMetaKeywords($article);
        $blogInformation->meta_description = $article['meta_description'] ?? null;
        $blogInformation->save();

        return [
          'url' => $this->blogUrl($blogInformation->slug),
        ];
      });

      return response()->json([
        'url' => $result['url'],
      ], 200);
    } catch (\Throwable $exception) {
      Log::error('AutoSEO webhook failed.', [
        'message' => $exception->getMessage(),
        'delivery' => $request->header('X-AutoSEO-Delivery'),
        'event' => $request->header('X-AutoSEO-Event'),
      ]);

      return response()->json([
        'message' => 'AutoSEO webhook failed.'
      ], 500);
    }
  }

  private function normalizePayload(array $payload): array
  {
    $article = Arr::get($payload, 'article', Arr::get($payload, 'data', $payload));
    $htmlContent = Arr::get($article, 'content_html', Arr::get($article, 'contentHtml'));
    $markdownContent = Arr::get($article, 'content_markdown', Arr::get($article, 'contentMarkdown'));

    return [
      'event' => Arr::get($article, 'event'),
      'source_article_id' => Arr::get($article, 'sourceArticleId', Arr::get($article, 'id')),
      'title' => Arr::get($article, 'title', Arr::get($article, 'headline')),
      'slug' => Arr::get($article, 'slug', Arr::get($article, 'permalink')),
      'published_url' => Arr::get($article, 'published_url', Arr::get($article, 'publishedUrl')),
      'content' => $htmlContent ?: ($markdownContent ?: Arr::get($article, 'content', Arr::get($article, 'body', Arr::get($article, 'html')))),
      'category' => Arr::get($article, 'category', Arr::get($article, 'category_name')),
      'meta_description' => Arr::get($article, 'metaDescription', Arr::get($article, 'meta_description', Arr::get($article, 'seo.description'))),
      'meta_keywords' => Arr::get($article, 'metaKeywords', Arr::get($article, 'meta_keywords', Arr::get($article, 'keywords', Arr::get($article, 'tags')))),
      'featured_image' => Arr::get($article, 'heroImageUrl', Arr::get($article, 'featured_image', Arr::get($article, 'image', Arr::get($article, 'image_url')))),
      'image_alt' => Arr::get($article, 'heroImageAlt'),
      'infographic_image' => Arr::get($article, 'infographicImageUrl'),
      'faq_schema' => Arr::get($article, 'faqSchema'),
      'published_at' => Arr::get($article, 'publishedAt', Arr::get($article, 'published_at', Arr::get($article, 'publish_date'))),
      'author' => Arr::get($article, 'author', Arr::get($article, 'author_name')),
      'language' => Arr::get($article, 'languageCode', Arr::get($article, 'language', Arr::get($article, 'locale'))),
      'status' => Arr::get($article, 'status'),
    ];
  }

  private function hasValidSignature(Request $request, string $configuredSecret): bool
  {
    $signature = (string) $request->header('X-AutoSEO-Signature');

    if ($signature === '') {
      return true;
    }

    $expectedSignature = hash_hmac('sha256', $request->getContent(), $configuredSecret);

    return hash_equals(strtolower($expectedSignature), strtolower($signature));
  }

  private function resolveLanguage(?string $code): Language
  {
    if ($code) {
      $language = Language::where('code', $code)->first();

      if ($language) {
        return $language;
      }
    }

    return Language::where('is_default', 1)->firstOrFail();
  }

  private function resolveCategory(Language $language, ?string $categoryName): BlogCategory
  {
    $name = trim((string) $categoryName);
    $name = $name === '' || strcasecmp($name, 'AutoSEO') === 0 ? 'Safety Guides' : $name;
    $slug = createSlug($name);

    $category = BlogCategory::query()
      ->where('language_id', $language->id)
      ->where(function ($query) use ($name, $slug) {
        $query->where('slug', $slug)->orWhere('name', $name);
      })
      ->first();

    if ($category) {
      return $category;
    }

    $category = BlogCategory::query()
      ->where(function ($query) use ($name, $slug) {
        $query->where('slug', $slug)->orWhere('name', $name);
      })
      ->first();

    if ($category) {
      return $category;
    }

    return BlogCategory::create([
      'language_id' => $language->id,
      'name' => $name,
      'slug' => $slug,
      'status' => 1,
      'serial_number' => ((int) BlogCategory::where('language_id', $language->id)->max('serial_number') + 1),
    ]);
  }

  private function resolveSlug(?string $slug, string $title): string
  {
    $baseSlug = createSlug($slug ?: $title);

    return $baseSlug ?: createSlug($title);
  }

  private function resolveImage(?string $featuredImage, ?string $currentImage): ?string
  {
    $featuredImage = trim((string) $featuredImage);

    if ($featuredImage === '') {
      return $currentImage;
    }

    return $featuredImage;
  }

  private function resolveMetaKeywords(array $article): ?string
  {
    $keywords = $article['meta_keywords'] ?? null;

    if (is_array($keywords)) {
      return implode(',', array_filter($keywords, 'is_scalar'));
    }

    if (is_string($keywords) && trim($keywords) !== '') {
      return $keywords;
    }

    return null;
  }

  private function resolvePublishedAt(?string $publishedAt): Carbon
  {
    if ($publishedAt) {
      return Carbon::parse($publishedAt);
    }

    return now();
  }

  private function blogUrl(string $slug): string
  {
    return rtrim((string) config('app.url'), '/') . '/blog/' . $slug;
  }
}
