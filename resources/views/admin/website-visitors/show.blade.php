@extends('admin.layout')

@section('content')
  @php
    $userLabel = $visitor->user
      ? trim(($visitor->user->first_name ?? '') . ' ' . ($visitor->user->last_name ?? '')) ?: ($visitor->user->username ?? $visitor->user->email)
      : __('Guest');
  @endphp

  <div class="page-header">
    <h4 class="page-title">{{ __('Visitor Details') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a>
      </li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item"><a href="{{ route('admin.website_visitors.index') }}">{{ __('Website Visitors') }}</a></li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item"><a href="#">{{ __('Details') }}</a></li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div class="card-title">{{ $userLabel }}</div>
          <a href="{{ route('admin.website_visitors.index') }}" class="btn btn-secondary btn-sm">{{ __('Back') }}</a>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4 mb-3"><strong>{{ __('Visitor UUID') }}:</strong><br>{{ $visitor->visitor_uuid }}</div>
            <div class="col-md-4 mb-3"><strong>{{ __('IP Address') }}:</strong><br>{{ $visitor->ip_address ?? '-' }}</div>
            <div class="col-md-4 mb-3"><strong>{{ __('Location') }}:</strong><br>{{ collect([$visitor->country, $visitor->city])->filter()->join(' / ') ?: '-' }}</div>
            <div class="col-md-4 mb-3"><strong>{{ __('Device') }}:</strong><br>{{ $visitor->device_type ?? '-' }}</div>
            <div class="col-md-4 mb-3"><strong>{{ __('Browser') }}:</strong><br>{{ $visitor->browser ?? '-' }}</div>
            <div class="col-md-4 mb-3"><strong>{{ __('OS') }}:</strong><br>{{ $visitor->os ?? '-' }}</div>
            <div class="col-md-4 mb-3"><strong>{{ __('First Visit') }}:</strong><br>{{ optional($visitor->first_visited_at)->format('d M Y H:i') ?: '-' }}</div>
            <div class="col-md-4 mb-3"><strong>{{ __('Last Activity') }}:</strong><br>{{ optional($visitor->last_activity_at)->format('d M Y H:i') ?: '-' }}</div>
            <div class="col-md-4 mb-3"><strong>{{ __('UTM') }}:</strong><br>{{ collect([$visitor->utm_source, $visitor->utm_medium, $visitor->utm_campaign])->filter()->join(' / ') ?: '-' }}</div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="card-title">{{ __('Pages Visited') }}</div>
        </div>
        <div class="card-body">
          @if ($pageViews->count() === 0)
            <h3 class="text-center mt-3">{{ __('No page views found') }}</h3>
          @else
            <div class="table-responsive">
              <table class="table table-striped mt-2">
                <thead>
                  <tr>
                    <th>{{ __('Page') }}</th>
                    <th>{{ __('URL') }}</th>
                    <th>{{ __('Referrer') }}</th>
                    <th>{{ __('Visited At') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($pageViews as $pageView)
                    <tr>
                      <td>{{ $pageView->page_title ?: '-' }}</td>
                      <td style="max-width: 420px;">
                        <a href="{{ $pageView->page_url }}" target="_blank" rel="noopener" class="text-truncate d-block">
                          {{ $pageView->page_url }}
                        </a>
                      </td>
                      <td style="max-width: 260px;">
                        <div class="text-truncate" title="{{ $pageView->referrer }}">{{ $pageView->referrer ?: '-' }}</div>
                      </td>
                      <td>{{ optional($pageView->visited_at)->format('d M Y H:i:s') ?: '-' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="mt-3">
              {{ $pageViews->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection
