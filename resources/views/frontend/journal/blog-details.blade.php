@php
  $version = $basicInfo->theme_version;
@endphp
@extends('frontend.layout')

@php
  $title = strlen($details->title) > 40 ? mb_substr($details->title, 0, 40, 'UTF-8') . '...' : $details->title;
  $detailsImage = !empty($details->image) && filter_var($details->image, FILTER_VALIDATE_URL)
    ? $details->image
    : asset('assets/img/blogs/' . $details->image);
  $detailsAuthor = in_array(strtolower(trim((string) $details->author)), ['autoseo', 'admin']) ? ($websiteInfo->website_title ?? config('app.name')) : $details->author;
  $detailsCategoryName = strcasecmp(trim((string) $details->categoryName), 'AutoSEO') === 0 ? 'Safety Guides' : $details->categoryName;
@endphp
@section('pageHeading')
  @if (!empty($title))
    {{ $title ? $title : $pageHeading->blog_page_title }}
  @endif
@endsection

@section('metaKeywords')
  {{ $details->meta_keywords }}
@endsection

@section('metaDescription')
  {{ $details->meta_description }}
@endsection

@section('content')
  <div class="bd-page">

  <!-- Hero start -->
  <section class="bd-hero">
    <div class="bd-hero-dots"></div>
    <div class="container">
      <nav aria-label="breadcrumb" class="bd-breadcrumb">
        <a href="{{ route('index') }}">{{ __('Home') }}</a>
        <i class="fal fa-angle-right"></i>
        <a href="{{ route('blog') }}">{{ !empty($pageHeading) ? $pageHeading->blog_page_title : __('Blog') }}</a>
        <i class="fal fa-angle-right"></i>
        <span>{{ !empty($title) ? $title : '' }}</span>
      </nav>
      <h1>{{ !empty($title) ? $title : '' }}</h1>
    </div>
  </section>
  <!-- Hero end -->

  <div class="blog-details-area pt-60 pb-60">
    <div class="container">
      <div class="row justify-content-center gx-xl-5">
        <div class="col-lg-8">
          <div class="blog-description mb-40">
            <article class="item-single">
              <div class="image radius-md">
                <div class="lazy-container ratio ratio-16-9">
                  <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                    data-src="{{ $detailsImage }}" alt="Blog Image">
                </div>
                <a href="javaScript:void(0)" class="btn btn-md btn-primary btn-gradient icon-start" data-bs-toggle="modal"
                  data-bs-target="#socialMediaModal"><i class="fas fa-share-alt"></i>{{ __('Share Now') }}</a>
              </div>
              <div class="content">
                <ul class="info-list">
                  <li><i class="fal fa-user"></i>{{ $detailsAuthor }}</li>
                  <li><i class="fal fa-calendar"></i> {{ \Carbon\Carbon::parse($details->created_at)->format('F d, Y') }}
                  </li>

                  <li><i class="fal fa-tag"></i>
                    <a href="{{ route('blog', ['category' => $details->categorySlug]) }}">
                      {{ $detailsCategoryName }}
                    </a>
                  </li>
                </ul>
                <h4 class="title">
                  {{ $title }}
                </h4>
                <p class="m-0">{!! replaceBaseUrl($details->content, 'summernote') !!}</p>
              </div>
            </article>
          </div>
          <div class="comments">
            <h4 class="mb-20">{{ __('Comments') }}</h4>
            @if ($disqusInfo->disqus_status == 1)
              <div id="disqus_thread"></div>
            @endif
          </div>
        </div>
        <div class="col-lg-4">
          <aside class="widget-area mb-10">
            <div class="widget widget-search mb-30 p-30 border radius-md">
              <h4 class="title mb-15">{{ __('Search Posts') }}</h4>
              <form class="search-form radius-md" action="{{ route('blog') }}" method="GET">
                <input type="search" class="search-input" placeholder="{{ __('Search By Title') }}" name="title"
                  value="{{ !empty(request()->input('title')) ? request()->input('title') : '' }}">
                @if (!empty(request()->input('category')))
                  <input type="hidden" name="category" value="{{ request()->input('category') }}">
                @endif
                <button class="btn-search" type="submit">
                  <i class="far fa-search"></i>
                </button>
              </form>
            </div>
            <div class="widget widget-blog-categories mb-30 p-30 border radius-md">
              <h4 class="title mb-15">{{ __('Categories') }}</h4>
              <ul class="list-unstyled m-0">
                @foreach ($categories as $category)
                  <li class="d-flex align-items-center justify-content-between">
                    <a href="{{ route('blog', ['category' => $category->slug]) }}" target="_self" title="Blogs"><i
                        class="fal fa-folder"></i>{{ $category->name }}</a>
                    <span class="tqy">({{ $category->blogCount }})</span>
                  </li>
                @endforeach
              </ul>
            </div>
            <div class="widget widget-post mb-30 p-30 border radius-md">
              <h4 class="title mb-15">{{ __('Recent Posts') }}</h4>
              @foreach ($recent_blogs as $blog)
                @php
                  $blogImage = !empty($blog->image) && filter_var($blog->image, FILTER_VALIDATE_URL)
                    ? $blog->image
                    : asset('assets/img/blogs/' . $blog->image);
                  $blogAuthor = in_array(strtolower(trim((string) $blog->author)), ['autoseo', 'admin']) ? ($websiteInfo->website_title ?? config('app.name')) : $blog->author;
                @endphp
                <article class="article-item mb-30">
                  <div class="image">
                    <a href="blog-details.html" class="lazy-container ratio ratio-5-4" target="_self" title="Blog">
                      <img class="lazyload" src="{{ asset('assets/frontend/images/placeholder.png') }}"
                        data-src="{{ $blogImage }}" alt="Blog Image">
                    </a>
                  </div>
                  <div class="content">
                    <h6 class="lc-2">
                      <a href="{{ route('blog_details', ['slug' => $blog->slug]) }}" target="_self" title="Blog">
                        {{ strlen($blog->title) > 40 ? mb_substr($blog->title, 0, 40, 'UTF-8') . '...' : $blog->title }}</a>
                    </h6>
                    <ul class="info-list">
                      <li><i class="fal fa-user"></i>{{ $blogAuthor }}</li>
                      <li><i class="fal fa-calendar"></i>{{ date_format($blog->created_at, 'M d, Y') }}</li>
                    </ul>
                  </div>
                </article>
              @endforeach
            </div>
            @if (!empty(showAd(1)))
              <div class="text-center mb-40">
                {!! showAd(1) !!}
              </div>
            @endif
            @if (!empty(showAd(2)))
              <div class="text-center mb-40">
                {!! showAd(2) !!}
              </div>
            @endif
          </aside>
        </div>
      </div>
    </div>
  </div>
  @php
      $display = 'none';
  @endphp
  <div class="modal social-media-modal fade" id="socialMediaModal" tabindex="-1" aria-labelledby="socialMediaModalTitle"
    style="display: {{ $display }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Share on') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="actions">
            <div class="action-btn">
              <a href="//www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" class="facebook"
                target="_blank">
                <i class="fab fa-facebook-f"></i>{{ __('Facebook') }}
              </a>
            </div>
            <div class="action-btn">
              <a href="//twitter.com/intent/tweet?text=my share text&amp;url={{ urlencode(url()->current()) }}"
                class="twitter" target="_blank">
                <i class="fab fa-twitter"></i>{{ __('Tweet') }}
              </a>
            </div>

            <div class="action-btn">
              <a href="//www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}&amp;title={{ $details->title }}"
                class="linkedin" target="_blank">
                <i class="fab fa-linkedin-in"></i>{{ __('Linkedin') }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  </div>
  <!-- /.bd-page -->

  <style>
    .bd-page {
      --bd-primary: #0e9f6e;
      --bd-primary-dark: #0a7d57;
      --bd-primary-deep: #06432f;
      --bd-primary-soft: #e5f5ee;
      --bd-ink: #101d2c;
      --bd-muted: #5e6b7e;
      background: #ffffff;
    }

    .bd-hero {
      position: relative;
      overflow: hidden;
      margin-top: 84px;
      padding: 50px 0 44px;
      background:
        radial-gradient(circle at 92% 6%, rgba(14, 159, 110, 0.5), transparent 40%),
        radial-gradient(circle at 4% 100%, rgba(245, 165, 36, 0.14), transparent 32%),
        linear-gradient(120deg, #0d1b16 0%, #06432f 100%);
      color: #ffffff;
    }

    .bd-hero-dots {
      position: absolute;
      inset: 0;
      background-image: radial-gradient(rgba(255, 255, 255, 0.07) 1.5px, transparent 1.5px);
      background-size: 22px 22px;
      mask-image: linear-gradient(120deg, rgba(0, 0, 0, 0.9), transparent 60%);
      -webkit-mask-image: linear-gradient(120deg, rgba(0, 0, 0, 0.9), transparent 60%);
      pointer-events: none;
    }

    .bd-hero .container {
      position: relative;
      z-index: 1;
    }

    .bd-breadcrumb {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 9px;
      margin-bottom: 14px;
      color: #a9c7ba;
      font-size: 12.5px;
      font-weight: 700;
    }

    .bd-breadcrumb a {
      color: #dbeee3;
      text-decoration: none;
    }

    .bd-breadcrumb i {
      font-size: 10px;
      color: #6f9a84;
    }

    .bd-breadcrumb span {
      color: #8be3bf;
    }

    .bd-hero h1 {
      margin: 0;
      max-width: 780px;
      color: #ffffff;
      font-size: clamp(24px, 3vw, 34px);
      line-height: 1.25;
      font-weight: 900;
      letter-spacing: -0.01em;
    }

    /* Reskin the existing blog-details theme components */
    .bd-page .item-single .image {
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 20px 50px rgba(16, 29, 44, 0.1);
    }

    .bd-page .item-single .content {
      margin-top: 26px;
      padding: 28px;
      background: #ffffff;
      border-radius: 18px;
      box-shadow: 0 14px 40px rgba(16, 29, 44, 0.07);
    }

    .bd-page .item-single .info-list li {
      color: var(--bd-muted);
    }

    .bd-page .item-single .info-list i {
      color: var(--bd-primary);
    }

    .bd-page .item-single .title {
      color: var(--bd-ink);
      font-weight: 900;
    }

    .bd-page .btn-primary {
      background: linear-gradient(120deg, var(--bd-primary), var(--bd-primary-dark)) !important;
      border-color: transparent !important;
    }

    .bd-page .widget-area .widget {
      border-radius: 18px !important;
      box-shadow: 0 14px 40px rgba(16, 29, 44, 0.07);
    }

    .bd-page .search-form .btn-search {
      background: var(--bd-primary);
      color: #ffffff;
    }

    .bd-page .widget-blog-categories a:hover,
    .bd-page .widget-post a:hover {
      color: var(--bd-primary-dark) !important;
    }
  </style>
@endsection
@section('script')
  @if ($disqusInfo->disqus_status == 1)
    <script>
      'use strict';
      const shortName = '{{ $disqusInfo->disqus_short_name }}';
      const slug = '{{ $details->blogSlug }}';
      const blogId = '{{ $details->id }}';
    </script>
    <!-- disqus plugin -->
    <script id="dsq-count-scr" src="//bookapp.disqus.com/count.js" async></script>
    <script src="{{ asset('assets/frontend/js/disqus.js') }}"></script>
  @endif
@endsection
