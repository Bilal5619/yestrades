@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Vendor Bank Accounts') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('admin.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Vendor Management') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Bank Accounts') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-6">
              <div class="card-title">{{ __('All Vendor Bank Accounts') }}</div>
            </div>
            <div class="col-lg-6">
              <form class="float-right" action="{{ route('admin.vendor_management.bank_accounts') }}" method="GET">
                <input name="info" type="text" class="form-control minw-230"
                  placeholder="{{ __('Search bank or vendor') }}"
                  value="{{ request()->input('info') }}">
              </form>
            </div>
          </div>
        </div>

        <div class="card-body">
          @include('admin.end-user.vendor-bank-accounts.partials.table', ['bankAccounts' => $bankAccounts])
        </div>

        <div class="card-footer">
          <div class="row">
            <div class="d-inline-block mx-auto">
              {{ $bankAccounts->appends(['info' => request()->input('info')])->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
