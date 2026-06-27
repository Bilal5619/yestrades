@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Service Packages') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home"><a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a></li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item"><a href="#">{{ __('Service Managment') }}</a></li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item"><a href="#">{{ __('Service Packages') }}</a></li>
    </ul>
  </div>

  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <div class="card-title">{{ __('Packages') }}</div>
      <a href="{{ route('admin.service_packages.create') }}" class="btn btn-primary btn-sm">{{ __('Add Package') }}</a>
    </div>
    <div class="card-body">
      @if ($packages->count() == 0)
        <h3 class="text-center mt-3">{{ __('No packages found.') }}</h3>
      @else
        <div class="table-responsive">
          <table class="table table-striped mt-3">
            <thead>
              <tr>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Price') }}</th>
                <th>{{ __('Services') }}</th>
                <th>{{ __('Homepage Annual Section') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($packages as $package)
                <tr>
                  <td>{{ $package->title }}</td>
                  <td>{{ $package->hasPricePlaceholder() ? $package->price_placeholder : symbolPrice($package->offer_price ?? $package->price) }}</td>
                  <td>{{ $package->services->count() }}</td>
                  <td>{{ $package->show_in_renewal_section ? __('Yes') : __('No') }}</td>
                  <td>
                    <span class="badge badge-{{ $package->is_active ? 'success' : 'secondary' }}">
                      {{ $package->is_active ? __('Active') : __('Inactive') }}
                    </span>
                  </td>
                  <td>
                    <a href="{{ route('admin.service_packages.edit', $package->id) }}" class="btn btn-secondary btn-sm">{{ __('Edit') }}</a>
                    <form action="{{ route('admin.service_packages.delete', $package->id) }}" method="POST" class="d-inline-block">
                      @csrf
                      <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('Delete this package?') }}')">{{ __('Delete') }}</button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        {{ $packages->links() }}
      @endif
    </div>
  </div>
@endsection
