@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Edit Service Package') }}</h4>
  </div>
  <div class="card">
    <div class="card-header"><div class="card-title">{{ __('Package Details') }}</div></div>
    <div class="card-body">
      <form action="{{ route('admin.service_packages.update', $package->id) }}" method="POST" enctype="multipart/form-data">
        @include('admin.service-packages.partials.form')
      </form>
    </div>
  </div>
@endsection
