@php
  $version = $basicInfo->theme_version;
@endphp
@extends('frontend.layout')

@section('pageHeading')
  @if (!empty($pageHeading))
    {{ $pageHeading->blog_page_title }}
  @else
    {{ __('Posts') }}
  @endif
@endsection

@section('metaKeywords')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_keyword_blog }}
  @endif
@endsection

@section('metaDescription')
  @if (!empty($seoInfo))
    {{ $seoInfo->meta_description_blog }}
  @endif
@endsection

@section('content')
  <main class="bg-page">

    <section class="bg-hero">
      <div class="bg-hero-dots"></div>
      <div class="container">
        <nav aria-label="breadcrumb" class="bg-breadcrumb">
          <a href="{{ route('index') }}">{{ __('Home') }}</a>
          <i class="fal fa-angle-right"></i>
          <span>{{ !empty($pageHeading) ? $pageHeading->blog_page_title : __('Blog') }}</span>
        </nav>
        <h1>{{ !empty($pageHeading) ? $pageHeading->blog_page_title : __('From the Blog') }}</h1>
        <p>{{ __('Tips, guides and news to help you keep your home safe, compliant and running smoothly.') }}</p>
      </div>
    </section>

    <section class="bg-section">
      <div class="container">
        <div class="row gx-xl-5">
          <div class="col-lg-8">
            @if (count($blogs) > 0)
              <div class="row g-4">
                @foreach ($blogs as $blog)
                  @php
                    $blogImage = !empty($blog->image) && filter_var($blog->image, FILTER_VALIDATE_URL)
                      ? $blog->image
                      : asset('assets/img/blogs/' . $blog->image);
                    $blogAuthor = in_array(strtolower(trim((string) $blog->author)), ['autoseo', 'admin']) ? ($websiteInfo->website_title ?? config('app.name')) : $blog->author;
                    $blogCategoryName = strcasecmp(trim((string) $blog->categoryName), 'AutoSEO') === 0 ? 'Safety Guides' : $blog->categoryName;
                  @endphp
                  <div class="col-md-6" data-aos="fade-up">
                    <article class="bg-card">
                      <div class="bg-card-media">
                        <a href="{{ route('blog_details', ['slug' => $blog->slug]) }}" target="_self" title="{{ @$blog->title }}">
                          <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                            data-src="{{ $blogImage }}" alt="Blog Image">
                        </a>
                        <a href="{{ route('blog', ['category' => $blog->categorySlug]) }}" class="bg-card-tag">
                          {{ $blogCategoryName }}
                        </a>
                      </div>
                      <div class="bg-card-body">
                        <div class="bg-card-meta">
                          <span><i class="fal fa-user-circle"></i>{{ $blogAuthor }}</span>
                          <span><i class="fal fa-calendar-alt"></i>{{ \Carbon\Carbon::parse($blog->created_at)->format('d M Y') }}</span>
                        </div>
                        <h3 class="bg-card-title">
                          <a href="{{ route('blog_details', ['slug' => $blog->slug]) }}" target="_self" title="{{ @$blog->title }}">
                            {{ @$blog->title }}
                          </a>
                        </h3>
                        <a href="{{ route('blog_details', ['slug' => $blog->slug]) }}" class="bg-card-link" target="_self">
                          {{ __('Continue Reading') }} <i class="fal fa-long-arrow-right"></i>
                        </a>
                      </div>
                    </article>
                  </div>
                @endforeach
              </div>
              <div class="bg-pagination-wrap">
                {{ $blogs->links() }}
              </div>
            @else
              <div class="bg-empty" data-aos="fade-up">
                <i class="fal fa-newspaper"></i>
                <h4>{{ __('NO BLOG FOUND') }}!</h4>
              </div>
            @endif
          </div>

          <div class="col-lg-4">
            <aside class="bg-sidebar" data-aos="fade-up">
              <div class="bg-widget">
                <h4>{{ __('Search Posts') }}</h4>
                <form class="bg-search-form" action="{{ route('blog') }}" method="GET">
                  <input type="search" placeholder="{{ __('Search By Title') }}" name="title"
                    value="{{ !empty(request()->input('title')) ? request()->input('title') : '' }}">
                  @if (!empty(request()->input('category')))
                    <input type="hidden" name="category" value="{{ request()->input('category') }}">
                  @endif
                  <button type="submit"><i class="far fa-search"></i></button>
                </form>
              </div>
              <div class="bg-widget">
                <h4>{{ __('Categories') }}</h4>
                <ul class="bg-cat-list">
                  @foreach ($categories as $category)
                    <li>
                      <a href="{{ route('blog', ['category' => $category->slug]) }}" target="_self" title="Blogs">
                        <i class="fal fa-folder"></i>{{ $category->name }}
                      </a>
                      <span class="bg-cat-count">{{ $category->blogCount }}</span>
                    </li>
                  @endforeach
                </ul>
              </div>
            </aside>
          </div>
        </div>

        @if (!empty(showAd(3)))
          <div class="text-center mt-4">
            {!! showAd(3) !!}
          </div>
        @endif
      </div>
    </section>
  </main>

  <style>
    .bg-page {
      --bg-primary: #0e9f6e;
      --bg-primary-dark: #0a7d57;
      --bg-primary-deep: #06432f;
      --bg-primary-soft: #e5f5ee;
      --bg-ink: #101d2c;
      --bg-ink-2: #33415c;
      --bg-muted: #5e6b7e;
      --bg-line: #e4e9e6;
      --bg-radius: 18px;
      --bg-shadow: 0 14px 40px rgba(16, 29, 44, 0.07);
      --bg-shadow-lg: 0 28px 64px rgba(16, 29, 44, 0.13);
      background: #ffffff;
    }

    .bg-hero {
      position: relative;
      overflow: hidden;
      margin-top: 84px;
      padding: 64px 0 58px;
      background:
        radial-gradient(circle at 92% 6%, rgba(14, 159, 110, 0.5), transparent 40%),
        radial-gradient(circle at 4% 100%, rgba(245, 165, 36, 0.14), transparent 32%),
        linear-gradient(120deg, #0d1b16 0%, #06432f 100%);
      color: #ffffff;
    }

    .bg-hero-dots {
      position: absolute;
      inset: 0;
      background-image: radial-gradient(rgba(255, 255, 255, 0.07) 1.5px, transparent 1.5px);
      background-size: 22px 22px;
      mask-image: linear-gradient(120deg, rgba(0, 0, 0, 0.9), transparent 60%);
      -webkit-mask-image: linear-gradient(120deg, rgba(0, 0, 0, 0.9), transparent 60%);
      pointer-events: none;
    }

    .bg-hero .container {
      position: relative;
      z-index: 1;
    }

    .bg-breadcrumb {
      display: flex;
      align-items: center;
      gap: 9px;
      margin-bottom: 16px;
      color: #a9c7ba;
      font-size: 12.5px;
      font-weight: 700;
    }

    .bg-breadcrumb a {
      color: #dbeee3;
      text-decoration: none;
    }

    .bg-breadcrumb i {
      font-size: 10px;
      color: #6f9a84;
    }

    .bg-breadcrumb span {
      color: #8be3bf;
    }

    .bg-hero h1 {
      margin: 0 0 10px;
      color: #ffffff;
      font-size: clamp(28px, 3.4vw, 40px);
      font-weight: 900;
      letter-spacing: -0.015em;
    }

    .bg-hero p {
      max-width: 520px;
      margin: 0;
      color: #bcd8cb;
      font-size: 14.5px;
      line-height: 1.7;
    }

    .bg-section {
      padding: 60px 0 90px;
      background: #f4f7f5;
    }

    /* ── Blog cards ───────────────────────────────── */
    .bg-card {
      display: flex;
      flex-direction: column;
      height: 100%;
      background: #ffffff;
      border-radius: var(--bg-radius);
      box-shadow: var(--bg-shadow);
      overflow: hidden;
      transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .bg-card:hover {
      transform: translateY(-6px);
      box-shadow: var(--bg-shadow-lg);
    }

    .bg-card-media {
      position: relative;
      overflow: hidden;
    }

    .bg-card-media img {
      display: block;
      width: 100%;
      aspect-ratio: 16 / 10;
      object-fit: cover;
      transition: transform 0.45s ease;
    }

    .bg-card:hover .bg-card-media img {
      transform: scale(1.06);
    }

    .bg-card-tag {
      position: absolute;
      left: 12px;
      top: 12px;
      padding: 6px 13px;
      color: var(--bg-primary-deep);
      background: #ffffff;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 800;
      text-decoration: none;
    }

    .bg-card-body {
      display: flex;
      flex-direction: column;
      flex: 1;
      padding: 20px;
    }

    .bg-card-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 14px;
      margin-bottom: 10px;
      color: var(--bg-muted);
      font-size: 12px;
      font-weight: 600;
    }

    .bg-card-meta i {
      margin-right: 5px;
      color: var(--bg-primary);
    }

    .bg-card-title {
      margin: 0 0 14px;
      font-size: 17px;
      line-height: 1.4;
      font-weight: 800;
    }

    .bg-card-title a {
      color: var(--bg-ink);
      text-decoration: none;
    }

    .bg-card-title a:hover {
      color: var(--bg-primary-dark);
    }

    .bg-card-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-top: auto;
      color: var(--bg-primary-dark);
      font-size: 12.5px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.03em;
      text-decoration: none;
    }

    .bg-card-link:hover {
      color: var(--bg-primary);
      gap: 12px;
    }

    /* ── Empty state ──────────────────────────────── */
    .bg-empty {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 12px;
      padding: 70px 24px;
      text-align: center;
      background: #ffffff;
      border-radius: var(--bg-radius);
    }

    .bg-empty i {
      font-size: 32px;
      color: var(--bg-primary);
    }

    .bg-empty h4 {
      margin: 0;
      color: var(--bg-ink);
      font-weight: 800;
    }

    .bg-pagination-wrap {
      margin-top: 34px;
      display: flex;
      justify-content: center;
    }

    .bg-pagination-wrap .page-link {
      color: var(--bg-ink-2);
      border-radius: 999px !important;
      margin: 0 3px;
      border-color: var(--bg-line);
    }

    .bg-pagination-wrap .page-item.active .page-link {
      color: #ffffff;
      background: var(--bg-primary);
      border-color: var(--bg-primary);
    }

    /* ── Sidebar ──────────────────────────────────── */
    .bg-sidebar {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .bg-widget {
      padding: 24px;
      background: #ffffff;
      border-radius: var(--bg-radius);
      box-shadow: var(--bg-shadow);
    }

    .bg-widget h4 {
      margin: 0 0 16px;
      color: var(--bg-ink);
      font-size: 15px;
      font-weight: 900;
    }

    .bg-search-form {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 5px 5px 5px 16px;
      background: #f4f7f5;
      border: 1px solid var(--bg-line);
      border-radius: 999px;
    }

    .bg-search-form input {
      flex: 1;
      min-width: 0;
      height: 40px;
      background: transparent;
      border: 0;
      outline: none;
      font-size: 13.5px;
      color: var(--bg-ink);
    }

    .bg-search-form button {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 38px;
      height: 38px;
      color: #ffffff;
      background: var(--bg-primary);
      border: 0;
      border-radius: 50%;
      font-size: 13px;
    }

    .bg-cat-list {
      display: flex;
      flex-direction: column;
      gap: 4px;
      margin: 0;
      padding: 0;
      list-style: none;
    }

    .bg-cat-list li {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
    }

    .bg-cat-list a {
      display: flex;
      align-items: center;
      gap: 9px;
      padding: 9px 0;
      color: var(--bg-ink-2);
      font-size: 13.5px;
      font-weight: 700;
      text-decoration: none;
    }

    .bg-cat-list a:hover {
      color: var(--bg-primary-dark);
    }

    .bg-cat-list a i {
      color: var(--bg-primary);
    }

    .bg-cat-count {
      padding: 2px 9px;
      color: var(--bg-muted);
      background: #f4f7f5;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 800;
    }

    @media (max-width: 767.98px) {
      .bg-hero {
        padding: 48px 0 44px;
      }

      .bg-section {
        padding: 40px 0 60px;
      }
    }
  </style>
@endsection
