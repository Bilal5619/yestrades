@php
  $version = $basicInfo->theme_version;
@endphp
@extends('frontend.layout')

@section('pageHeading')
  {{ $title }}
@endsection

@section('metaKeywords')
  {{ $meta_keywords }}
@endsection

@section('metaDescription')
  {{ $meta_description }}
@endsection

@section('content')
  <style>
    @include('frontend.partials.legal-page-styles')
  </style>

  <section class="lg-hero">
    <div class="lg-hero-dots"></div>
    <div class="container">
      <nav aria-label="breadcrumb" class="lg-breadcrumb">
        <a href="{{ route('index') }}">{{ __('Home') }}</a>
        <i class="fal fa-angle-right"></i>
        <span>{{ $title }}</span>
      </nav>
      <h1>{{ $title }}</h1>
    </div>
  </section>

  <section class="lg-content-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-9">
          <article class="legal-page-content tinymce-content" data-aos="fade-up">
            {!! $pageInfo->content !!}
          </article>
          @if (!empty(showAd(3)))
            <div class="text-center mt-5">
              {!! showAd(3) !!}
            </div>
          @endif
        </div>
      </div>
    </div>
  </section>
@endsection
