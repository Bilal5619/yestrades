@extends('admin.layout')

@section('content')
  <div class="mt-2 mb-4">
    <h2 class="pb-2">{{ __('Welcome back,') }} {{ $authAdmin->first_name . ' ' . $authAdmin->last_name . '!' }}</h2>
  </div>

  {{-- dashboard information start --}}
  @php
    if (!is_null($roleInfo)) {
        $rolePermissions = json_decode($roleInfo->permissions);
    }
  @endphp

  <div class="row dashboard-items" data-admin-refresh-panel="dashboard-items">
    @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Total Profit', $rolePermissions)))
      <div class="col-sm-6 col-md-3" data-toggle="tooltip" data-placement="right" data-html="true"
        title="Service Booking, Product Purchase, Featured Service, Membership Buy, Withdraw" (click)="nodeion(node)">
        <div class="card card-stats card-earning card-round">
          <div class="card-body">
            <div class="row">
              <div class="col-5">
                <div class="icon-big text-center">
                  <i class="fas fa-money-check-alt"></i>
                </div>
              </div>

              <div class="col-7 col-stats">
                <div class="numbers">
                  <p class="card-category">{{ __('Total Sales') }}</p>
                  <h4 class="card-title">{{ symbolPrice($totalProfitAmount) }}</h4>
                  @if (!empty($salesResetAt))
                    <small class="d-block text-white-50">
                      {{ __('Sales counted from:') }} {{ $salesResetAt->format('d M Y H:i') }}
                    </small>
                  @endif
                </div>
              </div>
            </div>
            <div class="mt-3 d-flex flex-wrap align-items-center">
              <a href="{{ route('admin.monthly_profit') }}" class="btn btn-light btn-sm mr-2 mb-1">
                {{ __('View Sales') }}
              </a>
              <form action="{{ route('admin.dashboard.reset_total_sales') }}" method="POST" class="mb-1"
                onsubmit="return confirm('{{ __('Are you sure you want to reset total sales? This should only be used before going live.') }}');">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">
                  {{ __('Reset Total Sales') }}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    @endif
    @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Service Managment', $rolePermissions)))
      <div class="col-sm-6 col-md-3">
        <a href="{{ route('admin.service_managment', ['language' => $defaultLang->code]) }}">
          <div class="card card-stats card-success card-round">
            <div class="card-body">
              <div class="row">
                <div class="col-5">
                  <div class="icon-big text-center">
                    <i class="fas fa-wrench"></i>
                  </div>
                </div>

                <div class="col-7 col-stats">
                  <div class="numbers">
                    <p class="card-category">{{ __('Services') }}</p>
                    <h4 class="card-title">{{ $totalService }}</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
    @endif

    @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Transactions', $rolePermissions)))
      <div class="col-sm-6 col-md-3">
        <a href="{{ route('admin.transaction') }}">
          <div class="card card-stats card-secondary card-round">
            <div class="card-body">
              <div class="row">
                <div class="col-5">
                  <div class="icon-big text-center">
                    <i class="fal fa-exchange-alt"></i>
                  </div>
                </div>

                <div class="col-7 col-stats">
                  <div class="numbers">
                    <p class="card-category">{{ __('Transaction') }}</p>
                    <h4 class="card-title">{{ $totalTransaction }}</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
    @endif

    @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Shop Management', $rolePermissions)))
      <div class="col-sm-6 col-md-3">
        <a href="{{ route('admin.shop_management.products', ['language' => $defaultLang->code]) }}">
          <div class="card card-stats card-primary card-round">
            <div class="card-body">
              <div class="row">
                <div class="col-5">
                  <div class="icon-big text-center">
                    <i class="fal fa-box-alt"></i>
                  </div>
                </div>

                <div class="col-7 col-stats">
                  <div class="numbers">
                    <p class="card-category">{{ __('Products') }}</p>
                    <h4 class="card-title">{{ $totalProduct }}</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
    @endif

    @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Shop Management', $rolePermissions)))
      <div class="col-sm-6 col-md-3">
        <a href="{{ route('admin.shop_management.orders') }}">
          <div class="card card-stats card-warning card-round">
            <div class="card-body">
              <div class="row">
                <div class="col-5">
                  <div class="icon-big text-center">
                    <i class="fal fa-shopping-cart"></i>
                  </div>
                </div>

                <div class="col-7 col-stats">
                  <div class="numbers">
                    <p class="card-category">{{ __('Orders') }}</p>
                    <h4 class="card-title">{{ $totalOrder }}</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
    @endif

    @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Appointments', $rolePermissions)))
      <div class="col-sm-6 col-md-3">
        <a href="{{ route('admin.all_appointment', ['language' => $defaultLang->code]) }}">
          <div class="card card-stats card-danger card-round">
            <div class="card-body">
              <div class="row">
                <div class="col-5">
                  <div class="icon-big text-center">
                    <i class="far fa-calendar-check"></i>
                  </div>
                </div>

                <div class="col-7 col-stats">
                  <div class="numbers">
                    <p class="card-category">{{ __('Service Jobs') }}</p>
                    <h4 class="card-title">{{ $totalServiceJobs }}</h4>
                    @if ($newServiceJobs > 0)
                      <span class="badge badge-warning">{{ $newServiceJobs }} {{ __('New') }}</span>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
    @endif

    @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Appointments', $rolePermissions)))
      <div class="col-sm-6 col-md-3">
        <a href="{{ route('admin.availability_compliance', ['language' => $defaultLang->code]) }}">
          <div class="card card-stats card-warning card-round">
            <div class="card-body">
              <div class="row">
                <div class="col-5">
                  <div class="icon-big text-center">
                    <i class="fas fa-exclamation-triangle"></i>
                  </div>
                </div>

                <div class="col-7 col-stats">
                  <div class="numbers">
                    <p class="card-category">{{ __('Compliance Tasks') }}</p>
                    <h4 class="card-title">{{ $complianceTaskCount ?? 0 }}</h4>
                    @if (!empty($complianceTaskCount) && $complianceTaskCount > 0)
                      <span class="badge badge-danger">{{ __('Needs review') }}</span>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
    @endif

    @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Blog Management', $rolePermissions)))
      <div class="col-sm-6 col-md-3">
        <a href="{{ route('admin.blog_management.blogs', ['language' => $defaultLang->code]) }}">
          <div class="card card-stats card-info card-round">
            <div class="card-body">
              <div class="row">
                <div class="col-5">
                  <div class="icon-big text-center">
                    <i class="fal fa-blog"></i>
                  </div>
                </div>

                <div class="col-7 col-stats">
                  <div class="numbers">
                    <p class="card-category">{{ __('Blog') }}</p>
                    <h4 class="card-title">{{ $totalBlog }}</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
    @endif

    @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('Vendors Management', $rolePermissions)))
      <div class="col-sm-6 col-md-3">
        <a href="{{ route('admin.vendor_management.registered_vendor') }}">
          <div class="card card-stats card-secondary card-round">
            <div class="card-body">
              <div class="row">
                <div class="col-5">
                  <div class="icon-big text-center">
                    <i class="fas fa-users"></i>
                  </div>
                </div>
                <div class="col-7 col-stats">
                  <div class="numbers">
                    <p class="card-category">{{ __('Vendors') }}</p>
                    <h4 class="card-title">
                      {{ $vendors }}
                    </h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
    @endif

    @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('User Management', $rolePermissions)))
      <div class="col-sm-6 col-md-3">
        <a href="{{ route('admin.user_management.registered_users') }}">
          <div class="card card-stats card-orchid card-round">
            <div class="card-body">
              <div class="row">
                <div class="col-5">
                  <div class="icon-big text-center">
                    <i class="la flaticon-users"></i>
                  </div>
                </div>

                <div class="col-7 col-stats">
                  <div class="numbers">
                    <p class="card-category">{{ __('Users') }}</p>
                    <h4 class="card-title">{{ $totalUser }}</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
    @endif

    @if (is_null($roleInfo) || (!empty($rolePermissions) && in_array('User Management', $rolePermissions)))
      <div class="col-sm-6 col-md-3">
        <a href="{{ route('admin.user_management.subscribers') }}">
          <div class="card card-stats card-dark card-round">
            <div class="card-body">
              <div class="row">
                <div class="col-5">
                  <div class="icon-big text-center">
                    <i class="fal fa-bell"></i>
                  </div>
                </div>

                <div class="col-7 col-stats">
                  <div class="numbers">
                    <p class="card-category">{{ __('Subscribers') }}</p>
                    <h4 class="card-title">{{ $totalSubscriber }}</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
    @endif
  </div>
  <div data-admin-refresh-panel="recent-appointments">
    @if (!empty($recentAppointments) && $recentAppointments->count() > 0)
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header">
              <div class="card-title">{{ __('Recent Appointments / Jobs') }}</div>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>{{ __('Booking ID') }}</th>
                      <th>{{ __('Service') }}</th>
                      <th>{{ __('Customer') }}</th>
                      <th>{{ __('Location') }}</th>
                      <th>{{ __('Vendor') }}</th>
                      <th>{{ __('Appointment') }}</th>
                      <th>{{ __('Payment') }}</th>
                      <th>{{ __('Order') }}</th>
                      <th>{{ __('Action') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($recentAppointments as $appointment)
                      <tr>
                        <td>{{ '#' . $appointment->order_number }}</td>
                        <td>
                          @if ($appointment->serviceContent->isNotEmpty())
                            <a href="{{ route('admin.appointment.details', ['id' => $appointment->id]) }}">
                              {{ truncateString($appointment->serviceContent->first()->name, 35) }}
                            </a>
                          @else
                            {{ '-' }}
                          @endif
                        </td>
                        <td>{{ $appointment->customer_email ?: ($appointment->customer_name ?: '-') }}</td>
                        <td>{{ truncateString((!empty($appointment->site_address) ? $appointment->site_address : $appointment->customer_address) ?: '-', 35) }}</td>
                        <td>
                          @if ($appointment->vendor_id == 0)
                            <span class="badge badge-success">{{ __('Admin') }}</span>
                          @else
                            {{ optional($appointment->vendor)->username ?? '-' }}
                          @endif
                        </td>
                        <td>
                          {{ \Carbon\Carbon::parse($appointment->booking_date)->format('M d, Y') }}
                          <br>
                          <small>{{ $appointment->start_date }} - {{ $appointment->end_date }}</small>
                        </td>
                        <td><span class="badge badge-{{ $appointment->payment_status == 'completed' ? 'success' : ($appointment->payment_status == 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($appointment->payment_status) }}</span></td>
                        <td><span class="badge badge-{{ in_array($appointment->order_status, ['accepted', 'completed']) ? 'success' : ($appointment->order_status == 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($appointment->order_status) }}</span></td>
                        <td>
                          <a href="{{ route('admin.appointment.details', ['id' => $appointment->id]) }}" class="btn btn-primary btn-sm">
                            {{ __('Details') }}
                          </a>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
  </div>
  <div class="row">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <div class="card-title">{{ __('Monthly Income') }} ({{ date('Y') }})</div>
        </div>

        <div class="card-body">
          <div class="chart-container">
            <canvas id="incomeChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <div class="card-title">{{ __('Monthly wise appointment') }} ({{ date('Y') }})</div>
        </div>

        <div class="card-body">
          <div class="chart-container">
            <canvas id="montlyAppointment"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
  {{-- dashboard information end --}}
@endsection

@section('script')
  {{-- chart js --}}
  <script type="text/javascript" src="{{ asset('assets/js/chart.min.js') }}"></script>

  <script>
    "use strict";
    const monthArr = @php echo json_encode($monthArr) @endphp;
    const monthlyIncome = @php echo json_encode($monthlyIncome) @endphp;
    const monthlyAppointment = @php echo json_encode($monthlyAppoinment) @endphp;
    $(document).ready(function() {
      $('[data-toggle="tooltip"]').tooltip();
    });
  </script>

  <script type="text/javascript" src="{{ asset('assets/js/chart-init.js') }}"></script>
@endsection
