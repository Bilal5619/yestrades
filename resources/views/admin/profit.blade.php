@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Monthly Sales Report') }}</h4>
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
        <a href="#">{{ __('Monthly Sales Report') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col-lg-4">
              <div class="card-title">{{ __('Monthly Sales Report') }}</div>
            </div>
            <div class="col-lg-8">
              <form action="{{ route('admin.monthly_profit') }}" method="get" class="d-flex justify-content-end" id="filterForm">
                <select class="form-control mr-2" style="width:auto;" name="month" onchange="document.getElementById('filterForm').submit()">
                  @foreach (['1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'] as $num => $name)
                    <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ __($name) }}</option>
                  @endforeach
                </select>
                <select class="form-control" style="width:auto;" name="year" onchange="document.getElementById('filterForm').submit()">
                  @for ($y = 2023; $y <= date('Y'); $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                  @endfor
                </select>
              </form>
            </div>
          </div>
        </div>

        <div class="card-body">

          {{-- Summary Bar --}}
          <div class="row mb-4">
            <div class="col-12">
              <div class="card bg-dark border-0 mb-0">
                <div class="card-body py-3">
                  <div class="row align-items-center text-center">
                    <div class="col-md-4 border-right">
                      <p class="text-muted mb-1 small text-uppercase">{{ __('Total Sales') }}</p>
                      <h4 class="mb-0 font-weight-bold text-success">
                        {{ $settings->base_currency_symbol_position == 'left' ? $settings->base_currency_symbol : '' }}{{ number_format($totalSales, 2) }}{{ $settings->base_currency_symbol_position == 'right' ? $settings->base_currency_symbol : '' }}
                      </h4>
                      @if (!empty($salesResetAt))
                        <small class="text-muted">{{ __('Sales counted from:') }} {{ $salesResetAt->format('d M Y H:i') }}</small>
                      @endif
                    </div>
                    <div class="col-md-4 border-right">
                      <p class="text-muted mb-1 small text-uppercase">{{ __('Expenses') }}</p>
                      <div class="input-group input-group-sm justify-content-center" style="max-width:180px;margin:0 auto;">
                        @if($settings->base_currency_symbol_position == 'left')
                          <div class="input-group-prepend">
                            <span class="input-group-text">{{ $settings->base_currency_symbol }}</span>
                          </div>
                        @endif
                        <input type="number" id="expensesInput" class="form-control text-center" placeholder="0.00" min="0" step="0.01" value="0">
                        @if($settings->base_currency_symbol_position == 'right')
                          <div class="input-group-append">
                            <span class="input-group-text">{{ $settings->base_currency_symbol }}</span>
                          </div>
                        @endif
                      </div>
                    </div>
                    <div class="col-md-4">
                      <p class="text-muted mb-1 small text-uppercase">{{ __('Profit') }}</p>
                      <h4 class="mb-0 font-weight-bold" id="profitDisplay">
                        {{ $settings->base_currency_symbol_position == 'left' ? $settings->base_currency_symbol : '' }}<span id="profitValue">{{ number_format($totalSales, 2) }}</span>{{ $settings->base_currency_symbol_position == 'right' ? $settings->base_currency_symbol : '' }}
                      </h4>
                      <small class="text-muted" id="profitPercent">0%</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- Sales List --}}
          <h5 class="mb-3">{{ __('All Sales for') }} {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}</h5>

          @if ($sales->isEmpty())
            <div class="text-center py-5 text-muted">
              <i class="fas fa-receipt fa-3x mb-3"></i>
              <p>{{ __('No sales found for this period.') }}</p>
            </div>
          @else
            <div class="table-responsive">
              <table class="table table-striped mt-2">
                <thead>
                  <tr>
                    <th>{{ __('Transaction ID') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Vendor') }}</th>
                    <th>{{ __('Payment Method') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Date') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($sales as $sale)
                    <tr>
                      <td><span class="badge badge-light">#{{ $sale->transaction_id }}</span></td>
                      <td>
                        @if ($sale->transaction_type == 'service_booking')
                          <span class="badge badge-primary">{{ __('Service Sale') }}</span>
                        @elseif ($sale->transaction_type == 'product_purchase')
                          <span class="badge badge-info">{{ __('Product Sale') }}</span>
                        @endif
                      </td>
                      <td>
                        @if ($sale->vendor)
                          {{ $sale->vendor->username }}
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                      <td>{{ $sale->payment_method ?? '-' }}</td>
                      <td class="text-success font-weight-bold">
                        {{ $sale->currency_symbol_position == 'left' ? $sale->currency_symbol : '' }}{{ number_format($sale->actual_total, 2) }}{{ $sale->currency_symbol_position == 'right' ? $sale->currency_symbol : '' }}
                      </td>
                      <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('d M Y, h:i A') }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif

        </div>
        <div class="card-footer"></div>
      </div>
    </div>
  </div>
  <script>
    (function () {
      var totalSales = {{ $totalSales }};
      var expensesInput = document.getElementById('expensesInput');
      var profitValue = document.getElementById('profitValue');
      var profitDisplay = document.getElementById('profitDisplay');
      var profitPercent = document.getElementById('profitPercent');

      function updateProfit() {
        var expenses = parseFloat(expensesInput.value) || 0;
        var profit = totalSales - expenses;
        profitValue.textContent = profit.toFixed(2);
        var pct = totalSales > 0 ? ((profit / totalSales) * 100).toFixed(1) : '0.0';
        profitPercent.textContent = pct + '%';
        if (profit < 0) {
          profitDisplay.classList.remove('text-success');
          profitDisplay.classList.add('text-danger');
          profitPercent.classList.remove('text-success');
          profitPercent.classList.add('text-danger');
        } else {
          profitDisplay.classList.remove('text-danger');
          profitDisplay.classList.add('text-success');
          profitPercent.classList.remove('text-danger');
          profitPercent.classList.add('text-success');
        }
      }

      expensesInput.addEventListener('input', updateProfit);
      updateProfit();
    })();
  </script>
@endsection
