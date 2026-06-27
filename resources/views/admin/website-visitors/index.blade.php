@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Website Visitors') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a>
      </li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item"><a href="#">{{ __('Website Visitors') }}</a></li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <form action="{{ route('admin.website_visitors.index') }}" method="GET">
            <div class="row align-items-end">
              <div class="col-lg-4">
                <div class="form-group">
                  <label>{{ __('Search') }}</label>
                  <input type="text" name="term" class="form-control" value="{{ request('term') }}"
                    placeholder="{{ __('UUID, IP, city, country, user') }}">
                </div>
              </div>
              <div class="col-lg-3">
                <button type="submit" class="btn btn-primary btn-sm">{{ __('Search') }}</button>
                <a href="{{ route('admin.website_visitors.index') }}" class="btn btn-secondary btn-sm">{{ __('Reset') }}</a>
              </div>
            </div>
          </form>
        </div>

        <div class="card-body">
          @if ($visitors->count() === 0)
            <h3 class="text-center mt-3">{{ __('No visitors found') }}</h3>
          @else
            <div class="table-responsive">
              <table class="table table-striped mt-2">
                <thead>
                  <tr>
                    <th>{{ __('Guest/User') }}</th>
                    <th>{{ __('IP Address') }}</th>
                    <th>{{ __('Country/City') }}</th>
                    <th>{{ __('Device') }}</th>
                    <th>{{ __('Browser') }}</th>
                    <th>{{ __('Last Visited Page') }}</th>
                    <th>{{ __('Referrer') }}</th>
                    <th>{{ __('First Visit') }}</th>
                    <th>{{ __('Last Activity') }}</th>
                    <th>{{ __('Actions') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($visitors as $visitor)
                    @php
                      $userLabel = $visitor->user
                        ? trim(($visitor->user->first_name ?? '') . ' ' . ($visitor->user->last_name ?? '')) ?: ($visitor->user->username ?? $visitor->user->email)
                        : __('Guest');
                    @endphp
                    <tr>
                      <td>
                        <strong>{{ $userLabel }}</strong>
                        @if ($visitor->user)
                          <div class="small text-muted">#{{ $visitor->user_id }}</div>
                        @endif
                      </td>
                      <td>{{ $visitor->ip_address ?? '-' }}</td>
                      <td>{{ collect([$visitor->country, $visitor->city])->filter()->join(' / ') ?: '-' }}</td>
                      <td>{{ $visitor->device_type ?? '-' }}<div class="small text-muted">{{ $visitor->os ?? '' }}</div></td>
                      <td>{{ $visitor->browser ?? '-' }}</td>
                      <td style="max-width: 260px;">
                        @if ($visitor->latestPageView)
                          <div class="text-truncate" title="{{ $visitor->latestPageView->page_url }}">
                            {{ $visitor->latestPageView->page_title ?: $visitor->latestPageView->page_url }}
                          </div>
                          <div class="small text-muted">{{ $visitor->page_views_count }} {{ __('views') }}</div>
                        @else
                          -
                        @endif
                      </td>
                      <td style="max-width: 220px;">
                        <div class="text-truncate" title="{{ $visitor->referrer }}">{{ $visitor->referrer ?: '-' }}</div>
                      </td>
                      <td>{{ optional($visitor->first_visited_at)->format('d M Y H:i') ?: '-' }}</td>
                      <td>{{ optional($visitor->last_activity_at)->format('d M Y H:i') ?: '-' }}</td>
                      <td>
                        <a href="{{ route('admin.website_visitors.show', $visitor->id) }}" class="btn btn-info btn-sm">
                          {{ __('View Details') }}
                        </a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="mt-3">
              {{ $visitors->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection
