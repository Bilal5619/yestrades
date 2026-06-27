@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Vendor Details') }}</h4>
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
        <a href="{{ route('admin.vendor_management.registered_vendor') }}">{{ __('Registered Vendors') }}</a>
      </li>
            <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ $vendor->username }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Vendor Details') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-4">
          <div class="card">
            <div class="card-header">
              <div class="h4 card-title">{{ __('Vendor Information') }}</div>
              <h2 class="text-center">
                @if ($vendor->photo != null)
                  <img class="admin-vendor-photo" src="{{ asset('assets/admin/img/vendor-photo/' . $vendor->photo) }}"
                    alt="..." class="uploaded-img">
                @else
                  <img class="admin-vendor-photo" src="{{ asset('assets/img/blank-user.jpg') }}" alt="..."
                    class="uploaded-img">
                @endif

              </h2>
            </div>

            <div class="card-body">
              <div class="payment-information">

                @php
                  $currPackage = \App\Http\Helpers\VendorPermissionHelper::currPackageOrPending($vendor->id);
                  $currMemb = \App\Http\Helpers\VendorPermissionHelper::currMembOrPending($vendor->id);
                @endphp
                <div class="row mb-3">
                  <div class="col-lg-6">
                    <strong>{{ __('Current Package:') }}</strong>
                  </div>
                  <div class="col-lg-6">
                    @if ($currPackage)
                      <a target="_blank"
                        href="{{ route('admin.package.edit', $currPackage->id) }}">{{ $currPackage->title }}</a>
                      <span class="badge badge-secondary badge-xs mr-2">{{ $currPackage->term }}</span>
                      <button type="submit" class="btn btn-xs btn-warning" data-toggle="modal"
                        data-target="#editCurrentPackage"><i class="far fa-edit"></i></button>
                      <form action="{{ route('vendor.currPackage.remove') }}" class="d-inline-block deleteForm"
                        method="POST">
                        @csrf
                        <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">
                        <button type="submit" class="btn btn-xs btn-danger deleteBtn"><i
                            class="fas fa-trash"></i></button>
                      </form>

                      <p class="mb-0">
                        @if ($currMemb->is_trial == 1)
                          ({{ __('Expire Date') . ':' }}
                          {{ Carbon\Carbon::parse($currMemb->expire_date)->format('M-d-Y') }})
                          <span class="badge badge-primary">{{ __('Trial') }}</span>
                        @else
                          ({{ __('Expire Date') . ':' }}
                          {{ $currPackage->term === 'lifetime' ? 'Lifetime' : Carbon\Carbon::parse($currMemb->expire_date)->format('M-d-Y') }})
                        @endif
                        @if ($currMemb->status == 0)
                          <form id="statusForm{{ $currMemb->id }}" class="d-inline-block"
                            action="{{ route('admin.payment-log.update') }}" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{ $currMemb->id }}">
                            <select class="form-control form-control-sm bg-warning" name="status"
                              onchange="document.getElementById('statusForm{{ $currMemb->id }}').submit();">
                              <option value=0 selected>{{ __('Pending') }}</option>
                              <option value=1>{{ __('Success') }}</option>
                              <option value=2>{{ __('Rejected') }}</option>
                            </select>
                          </form>
                        @endif
                      </p>
                    @else
                      <a data-target="#addCurrentPackage" data-toggle="modal" class="btn btn-xs btn-primary text-white"><i
                          class="fas fa-plus"></i> {{ __('Add Package') }}</a>
                    @endif
                  </div>
                </div>

                @php
                  $nextPackage = \App\Http\Helpers\VendorPermissionHelper::nextPackage($vendor->id);
                  $nextMemb = \App\Http\Helpers\VendorPermissionHelper::nextMembership($vendor->id);
                @endphp
                <div class="row mb-3">
                  <div class="col-lg-6">
                    <strong>{{ __('Next Package:') }}</strong>
                  </div>
                  <div class="col-lg-6">
                    @if ($nextPackage)
                      <a target="_blank"
                        href="{{ route('admin.package.edit', $nextPackage->id) }}">{{ $nextPackage->title }}</a>
                      <span class="badge badge-secondary badge-xs mr-2">{{ $nextPackage->term }}</span>
                      <button type="button" class="btn btn-xs btn-warning" data-toggle="modal"
                        data-target="#editNextPackage"><i class="far fa-edit"></i></button>
                      <form action="{{ route('vendor.nextPackage.remove') }}" class="d-inline-block deleteForm"
                        method="POST">
                        @csrf
                        <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">
                        <button type="submit" class="btn btn-xs btn-danger deleteBtn"><i
                            class="fas fa-trash"></i></button>
                      </form>

                      <p class="mb-0">
                        @if ($currPackage->term != 'lifetime' && $nextMemb->is_trial != 1)
                          (
                          Activation Date:
                          {{ Carbon\Carbon::parse($nextMemb->start_date)->format('M-d-Y') }},
                          Expire Date:
                          {{ $nextPackage->term === 'lifetime' ? 'Lifetime' : Carbon\Carbon::parse($nextMemb->expire_date)->format('M-d-Y') }})
                        @endif
                        @if ($nextMemb->status == 0)
                          <form id="statusForm{{ $nextMemb->id }}" class="d-inline-block"
                            action="{{ route('admin.payment-log.update') }}" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{ $nextMemb->id }}">
                            <select class="form-control form-control-sm bg-warning" name="status"
                              onchange="document.getElementById('statusForm{{ $nextMemb->id }}').submit();">
                              <option value=0 selected>{{ __('Pending') }}</option>
                              <option value=1>{{ __('Success') }}</option>
                              <option value=2>{{ __('Rejected') }}</option>
                            </select>
                          </form>
                        @endif
                      </p>
                    @else
                      @if (!empty($currPackage))
                        <a class="btn btn-xs btn-primary text-white" data-toggle="modal"
                          data-target="#addNextPackage"><i class="fas fa-plus"></i> {{ __('Add  Package') }}</a>
                      @else
                        -
                      @endif
                    @endif
                  </div>
                </div>

                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Name') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ @$vendor->vendor_info->name }}
                  </div>
                </div>

                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Username') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ $vendor->username }}
                  </div>
                </div>

                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Email') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ $vendor->email }}
                  </div>
                </div>

                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Phone') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ $vendor->phone }}
                  </div>
                </div>

                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Admin Commission') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ is_null($vendor->fixed_commission_percent) ? __('Not Set') : number_format($vendor->fixed_commission_percent, 2) . '%' }}
                  </div>
                </div>

                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Business Type') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    @if ($vendor->business_type === 'sole_trader')
                      {{ __('Sole Trader') }}
                    @elseif ($vendor->business_type === 'limited_company')
                      {{ __('Limited Company') }}
                    @else
                      {{ __('Not Provided') }}
                    @endif
                  </div>
                </div>

                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Service Types') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    @if ($vendor->serviceCategories->count() > 0)
                      {{ $vendor->serviceCategories->pluck('name')->join(', ') }}
                    @else
                      {{ __('Not Provided') }}
                    @endif
                  </div>
                </div>

                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Country') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ @$vendor->vendor_info->country }}
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('City') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ @$vendor->vendor_info->city }}
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('State') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ @$vendor->vendor_info->state }}
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Postcode') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ @$vendor->vendor_info->postcode ?: @$vendor->vendor_info->zip_code }}
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Address') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ @$vendor->vendor_info->address }}
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-lg-4">
                    <strong>{{ __('Details') . ' :' }}</strong>
                  </div>
                  <div class="col-lg-8">
                    {{ @$vendor->vendor_info->details }}
                  </div>
                </div>

                @if ($registrationNumbers->count() > 0)
                  <hr>
                  <div class="row mb-2">
                    <div class="col-12">
                      <strong>{{ __('Registration Numbers') }}</strong>
                    </div>
                  </div>
                  @foreach ($registrationNumbers as $regNum)
                    <div class="row mb-1">
                      <div class="col-lg-6">
                        <span class="text-muted">{{ $regNum->title }}</span>
                      </div>
                      <div class="col-lg-6">
                        <strong>{{ $regNum->registration_number ?: $regNum->number }}</strong>
                        @if ($regNum->registration_authority)
                          <div class="text-muted"><small>{{ __('Authority') }}: {{ $regNum->registration_authority }}</small></div>
                        @endif
                        @if ($regNum->expiry_date)
                          <div class="text-muted"><small>{{ __('Expires') }}: {{ $regNum->expiry_date->format('d M Y') }}</small></div>
                        @endif
                      </div>
                    </div>
                  @endforeach
                @else
                  <hr>
                  <div class="row mb-2">
                    <div class="col-12">
                      <strong>{{ __('Registration Numbers') }}</strong>
                      <span class="text-muted ml-2">{{ __('None provided yet.') }}</span>
                    </div>
                  </div>
                @endif

                <hr>
                @php
                  $verifBadge = [
                      'not_submitted' => ['secondary', 'Not Submitted'],
                      'submitted' => ['info', 'Submitted (Under Review)'],
                      'approved' => ['success', 'Approved'],
                      'rejected' => ['danger', 'Rejected'],
                  ];
                  [$vClass, $vLabel] = $verifBadge[$vendor->verification_status] ?? ['secondary', ucfirst($vendor->verification_status)];
                  $docBadge = [
                      'under_review' => ['info', 'Under Review'],
                      'approved' => ['success', 'Approved'],
                      'rejected' => ['danger', 'Rejected'],
                      'pending' => ['warning', 'Pending'],
                  ];
                  $docMeta = [
                      'insurance' => 'Public Liability Insurance',
                      'id_document' => 'Driving Licence / Passport',
                      'right_to_work' => 'Right to Work if not British',
                  ];
                @endphp
                <div class="row mb-2">
                  <div class="col-12">
                    <strong>{{ __('Verification & Documents') }}</strong>
                    <span class="badge badge-{{ $vClass }} ml-2">{{ __($vLabel) }}</span>
                  </div>
                </div>

                @if ($vendor->verification_status === 'rejected' && !empty($vendor->verification_rejection_reason))
                  <div class="alert alert-danger py-2 px-2">
                    <small><strong>{{ __('Rejection reason') }}:</strong> {{ $vendor->verification_rejection_reason }}</small>
                  </div>
                @endif

                @foreach ($docMeta as $type => $label)
                  @php $doc = $documents->get($type); @endphp
                  <div class="border rounded p-2 mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                      <strong>{{ __($label) }}</strong>
                      @if ($type === 'right_to_work' && $vendor->right_to_work_is_british)
                        <span class="badge badge-success">{{ __('Not Required') }}</span>
                      @elseif ($doc)
                        @php [$dClass, $dLabel] = $docBadge[$doc->normalized_status] ?? ['secondary', ucfirst($doc->status)]; @endphp
                        <span class="badge badge-{{ $dClass }}">{{ __($dLabel) }}</span>
                      @else
                        <span class="badge badge-secondary">{{ __('Not Uploaded') }}</span>
                      @endif
                    </div>

                    @if ($type === 'right_to_work' && $vendor->right_to_work_is_british)
                      <div class="mt-1 text-success"><small>{{ __('Vendor confirmed they are British, so Right to Work upload is not required.') }}</small></div>
                    @elseif ($doc)
                      <div class="mt-1">
                        <a href="{{ $doc->url }}" target="_blank" rel="noopener noreferrer">
                          <i class="fas fa-file mr-1"></i>{{ __('View / Download') }}
                        </a>
                        @if ($type === 'id_document' && $doc->id_type)
                          <span class="text-muted ml-2">({{ $doc->id_type === 'passport' ? __('Passport') : __('Driving Licence') }})</span>
                        @endif
                        @if ($doc->expiry_date)
                          <div class="text-muted"><small>{{ __('Expires') }}: {{ $doc->expiry_date->format('d M Y') }}</small></div>
                        @endif
                        @if ($type === 'right_to_work' && $doc->date_of_birth)
                          <div class="text-muted"><small>{{ __('Date of Birth') }}: {{ $doc->date_of_birth->format('d M Y') }}</small></div>
                        @endif
                        @if ($type === 'insurance')
                          @if ($doc->policy_number)
                            <div class="text-muted"><small>{{ __('Policy') }}: {{ $doc->policy_number }}</small></div>
                          @endif
                        @endif
                      </div>

                      @if ($doc->is_rejected && $doc->rejection_reason)
                        <div class="text-danger mt-1"><small>{{ __('Reason') }}: {{ $doc->rejection_reason }}</small></div>
                      @endif

                      @if ($doc->is_pending_review)
                        <div class="mt-2 d-flex flex-wrap" style="gap:6px;">
                          <form action="{{ route('admin.vendor_management.document.approve', ['id' => $vendor->id, 'documentId' => $doc->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-xs">{{ __('Approve') }}</button>
                          </form>
                          <form action="{{ route('admin.vendor_management.document.reject', ['id' => $vendor->id, 'documentId' => $doc->id]) }}" method="POST" class="form-inline">
                            @csrf
                            <input type="text" name="rejection_reason" class="form-control form-control-sm mr-1"
                              placeholder="{{ __('Reason') }}" required style="max-width:160px;">
                            <button type="submit" class="btn btn-danger btn-xs">{{ __('Reject') }}</button>
                          </form>
                        </div>
                      @elseif ($doc->is_approved)
                        <div class="mt-2">
                          <span class="badge badge-success">{{ __('Approved') }}</span>
                        </div>
                      @elseif ($doc->is_rejected)
                        <div class="mt-2">
                          <span class="badge badge-danger">{{ __('Rejected') }}</span>
                        </div>
                      @endif
                    @endif
                  </div>
                @endforeach

                <div class="row mb-2 mt-2">
                  <div class="col-12 d-flex flex-wrap align-items-start" style="gap:8px;">
                    @if ($vendor->verification_status !== 'approved' || (int) $vendor->status !== 1)
                      <form action="{{ route('admin.vendor_management.verification.approve', ['id' => $vendor->id]) }}" method="POST"
                        onsubmit="return confirm('{{ __('Approve this vendor account?') }}')">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">{{ __('Approve Account') }}</button>
                      </form>
                    @endif
                    @if ($vendor->verification_status !== 'rejected')
                      <form action="{{ route('admin.vendor_management.verification.reject', ['id' => $vendor->id]) }}" method="POST" class="form-inline">
                        @csrf
                        <input type="text" name="verification_rejection_reason" class="form-control form-control-sm mr-1"
                          placeholder="{{ __('Rejection reason') }}" required style="max-width:200px;">
                        <button type="submit" class="btn btn-danger btn-sm">{{ __('Reject Verification') }}</button>
                      </form>
                    @endif
                  </div>
                </div>

                <hr>
                <div class="row mb-2">
                  <div class="col-12">
                    <strong>{{ __('Bank Accounts') }}</strong>
                    <a href="{{ route('admin.vendor_management.vendor_bank_accounts', ['id' => $vendor->id]) }}"
                      class="btn btn-xs btn-primary float-right">{{ __('View All') }}</a>
                  </div>
                </div>
                @if ($bankAccounts->count() > 0)
                  @foreach ($bankAccounts as $account)
                    <div class="row mb-1">
                      <div class="col-lg-6">
                        <span class="text-muted">{{ $account->bank_name }}</span>
                      </div>
                      <div class="col-lg-6">
                        <strong>{{ $account->account_number }}</strong>
                        @if ($account->is_active)
                          <span class="badge badge-success">{{ __('Active') }}</span>
                        @endif
                      </div>
                    </div>
                  @endforeach
                @else
                  <div class="row mb-2">
                    <div class="col-12">
                      <span class="text-muted">{{ __('No bank account provided yet.') }}</span>
                    </div>
                  </div>
                @endif

              </div>
            </div>
          </div>
        </div>
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <div class="row">
                <div class="col-lg-4">
                  <div class="card-title d-inline-block">{{ __('All Services') }}</div>
                </div>

                <div class="col-lg-3">
                  @includeIf('admin.partials.languages')
                </div>

                <div class="col-lg-4 offset-lg-1 mt-2 mt-lg-0">

                  <button class="btn btn-danger btn-sm float-right mr-2 d-none bulk-delete"
                    data-href="{{ route('admin.service_managment.bulk_delete') }}">
                    <i class="flaticon-interface-5"></i> {{ __('Delete') }}
                  </button>
                </div>
              </div>
            </div>

            <div class="card-body">
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
                          <th scope="col">{{ __('Name') }}</th>
                          <th scope="col">{{ __('Status') }}</th>
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
                              @if ($service->content->isNotEmpty())
                                {{ strlen($service->content->first()->name) > 40 ? mb_substr($service->content->first()->name, 0, 40, 'utf-8') . '...' : $service->content->first()->name }}
                              @else
                                {{ '-' }}
                              @endif
                            </td>
                            <td>
                              <form id="serviceStatus{{ $service->id }}" class="d-inline-block"
                                action="{{ route('admin.service.status.change') }}" method="post">
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
                            </td>
                            {{-- admin.service_managment.edit --}}
                            <td>
                              <a class="btn btn-secondary btn-sm mr-1 mb-1"
                                href="{{ route('admin.service_managment.edit', $service->id) }}">
                                <span class="btn-label">
                                  <i class="fas fa-edit" class="mar--3"></i>
                                </span>
                              </a>

                              <form class="deleteForm d-inline-block"
                                action="{{ route('admin.service_managment.delete', ['id' => $service->id]) }}"
                                method="post">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm deleteBtn  mb-1">
                                  <span class="btn-label">
                                    <i class="fas fa-trash" class="mar--3"></i>
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
        </div>
      </div>
    </div>
  </div>
    @includeIf('admin.end-user.vendor.edit-current-package')
    @includeIf('admin.end-user.vendor.add-current-package')
    @includeIf('admin.end-user.vendor.edit-next-package')
    @includeIf('admin.end-user.vendor.add-next-package')
  @endsection
