@extends('vendors.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Services') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('vendor.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Service Managment') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="{{ route('vendor.service_managment', ['language' => $defaultLang->code]) }}">{{ __('Services') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      @php
        $vendor_id = Auth::guard('vendor')->user()->id;
      @endphp

      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <div class="row">
                <div class="col-lg-4 col-md-6">
                  <div class="card-title d-inline-block">{{ __('Services') }}</div>
                </div>
                <div class="col-lg-4">
                  @includeIf('vendors.partials.languages')
                </div>
                <div class="col-lg-4 col-md-6 mt-2 mt-lg-0">
                  <a href="{{ route('vendor.service_managment.create') }}" class="btn btn-primary btn-sm float-right"><i
                      class="fas fa-plus"></i> {{ __('Add Service') }}</a>

                  <button class="btn btn-danger btn-sm float-right mr-2 d-none bulk-delete"
                    data-href="{{ route('vendor.service_managment.bulk_delete') }}">
                    <i class="flaticon-interface-5"></i> {{ __('Delete') }}
                  </button>
                </div>
              </div>
            </div>

            <div class="card-body">
              <div class="row">
                <div class="col-lg-12">
                  @if (count($services) == 0)
                    <h3 class="text-center mt-2">{{ __('NO SERVICE FOUND') . '!' }}</h3>
                  @else
                    <div class="table-responsive">
                      <table class="table table-striped mt-3" id="basic-datatables">
                        <thead>
                          <tr>
                            <th scope="col">
                              <input type="checkbox" class="bulk-check" data-val="all">
                            </th>
                            <th scope="col">{{ __('Service Image') }}</th>
                            <th scope="col">{{ __('Service Title') }}</th>
                            <th scope="col">{{ __('Featured Status') }}</th>
                            <th scope="col">
                              @php $currencyText = $currencyInfo->base_currency_text; @endphp
                              {{ __('Price') . ' (' . $currencyText . ')' }}
                            </th>
                            <th>{{ __('Status') }}</th>
                            <th scope="col">{{ __('Actions') }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($services as $service)
                            <tr>
                              <td>
                                <input type="checkbox" class="bulk-check" data-val="{{ $service->id }}">
                              </td>
                              <td>
                                <img src="{{ asset('assets/img/services/' . $service->service_image) }}"
                                  alt="service image" width="80">
                              </td>
                              <td>
                                @if ($service->content->isNotEmpty())
                                  @foreach ($service->content as $content)
                                    <a href="{{ route('frontend.service.details', ['slug' => $content->slug, 'id' => $service->id]) }}"
                                      target="_blank">
                                      {{ strlen($content->name) > 50 ? mb_substr($content->name, 0, 50, 'utf-8') . '...' : $content->name }}
                                    </a>
                                  @endforeach
                                @else
                                  {{ '-' }}
                                @endif
                              </td>
                              <td>
                                @php
                                  $featuredService = \App\Models\FeaturedService\ServicePromotion::where(
                                      'service_id',
                                      $service->id,
                                  )
                                      ->latest()
                                      ->first();
                                @endphp
                                @if ($featuredService)
                                  @if ($featuredService->order_status == 'pending' || $featuredService->payment_status == 'pending')
                                    <h2 class="d-inline-block"><span
                                        class="badge badge-warning">{{ __('Pending') }}</span>
                                    </h2>
                                  @elseif(
                                      $featuredService->order_status == 'approved' &&
                                          $featuredService->payment_status == 'completed' &&
                                          $featuredService->end_date >= \Carbon\Carbon::now()->format('Y-m-d'))
                                    <h2 class="d-inline-block"><span
                                        class="badge badge-success">{{ __('Active') }}</span>
                                    </h2>
                                  @elseif ($featuredService->end_date <= \Carbon\Carbon::now()->format('Y-m-d'))
                                    <a href="javascript:void()" class="featured btn btn-sm btn-primary"
                                      data-toggle="modal" data-target="#featured" data-id="{{ $service->id }}">
                                      {{ __('Pay to Feature') }}
                                    </a>
                                  @endif
                                @else
                                  <a href="javascript:void()" class="featured btn btn-sm btn-primary" data-toggle="modal"
                                    data-target="#featured" data-id="{{ $service->id }}">
                                    {{ __('Pay to Feature') }}
                                  </a>
                                @endif
                              </td>
                              <td>{{ symbolPrice($service->price) }}</td>
                              <td>
                                @php
                                  $appStatus = $service->approval_status ?? 'approved';
                                  $appBadge = ['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'];
                                @endphp
                                @if ($appStatus !== 'approved')
                                  <span class="badge badge-{{ $appBadge[$appStatus] ?? 'secondary' }} mb-1 d-inline-block">
                                    {{ $appStatus === 'pending' ? __('Awaiting Approval') : __('Rejected') }}
                                  </span>
                                  @if ($appStatus === 'rejected' && $service->service_rejection_reason)
                                    <div class="text-danger"><small>{{ $service->service_rejection_reason }}</small></div>
                                  @endif
                                @else
                                  <form id="serviceStatus{{ $service->id }}" class="d-inline-block"
                                    action="{{ route('vendor.service.status.change') }}" method="post">
                                    @csrf
                                    <select
                                      class="form-control form-control-sm {{ $service->status == 1 ? 'bg-success' : 'bg-danger' }}"
                                      name="status"
                                      onchange="document.getElementById('serviceStatus{{ $service->id }}').submit();">
                                      <option value="1" {{ $service->status == 1 ? 'selected' : '' }}>
                                        {{ __('Active') }}
                                      </option>
                                      <option value="0" {{ $service->status == 0 ? 'selected' : '' }}>
                                        {{ __('Deactive') }}
                                      </option>
                                    </select>
                                    <input type="hidden" name="service_id" value="{{ $service->id }}">
                                  </form>
                                @endif
                              </td>

                              <td>
                                <a class="btn btn-secondary mt-1 btn-sm mr-1"
                                  href="{{ route('vendor.service_managment.edit', ['id' => $service->id]) }}">
                                  <span class="btn-label">
                                    <i class="fas fa-edit"></i>
                                  </span>
                                </a>

                                <form class="deleteForm d-inline-block"
                                  action="{{ route('vendor.service_managment.delete_product', ['id' => $service->id]) }}"
                                  method="post">
                                  @csrf
                                  <button type="submit" class="btn btn-danger mt-1 btn-sm deleteBtn">
                                    <span class="btn-label">
                                      <i class="fas fa-trash"></i>
                                    </span>
                                  </button>
                                </form>
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="card-footer"></div>
          </div>
        </div>
      </div>

    </div>
  </div>
  <div id="razorPayForm"></div>
  @include('vendors.services.featured')
@endsection
@section('script')
  <script src="https://js.stripe.com/v3/"></script>
  <script src="{{ $authorizeUrl }}"></script>
  <script>
    let authorize_login_key = "{{ $authorize_login_id }}";
    let authorize_public_key = "{{ $authorize_public_key }}";
    let stripe_key = "{{ $stripe_key }}";
  </script>
  <script src="{{ asset('assets/js/service_featured.js') }}"></script>
  <script>
    @if (old('gateway') == 'stripe')
      $('#stripe-element').removeClass('d-none');
    @endif
  </script>
@endsection
