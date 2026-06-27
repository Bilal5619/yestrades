@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Transactions') }}</h4>
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
        <a href="#">{{ __('Transaction') }}</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col-lg-6">
              <div class="card-title d-inline-block">{{ __('Transactions') }}</div>
            </div>

            <div class="col-lg-3 mt-2 mt-lg-0 justify-content-end">
              <form action="{{ url()->current() }}" class="d-inline-block d-flex">
                <input class="form-control" type="text" name="transaction_id"
                  placeholder="{{ __('Enter Transaction Id') }}"
                  value="{{ request()->input('transaction_id') ? request()->input('transaction_id') : '' }}">
                <button class="dis-none" type="submit"></button>
              </form>
            </div>
            <div class="col-lg-3 mt-2 mt-lg-0 text-lg-right">
              <form action="{{ route('admin.transaction.delete_all') }}" method="POST" class="d-inline-block"
                onsubmit="return confirm('{{ __('Are you sure? This will permanently delete all transactions.') }}');">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">
                  {{ __('Delete All') }}
                </button>
              </form>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($transactions) == 0)
                <h3 class="text-center">{{ __('NO TRANSACTIONS FOUND') }}</h3>
              @else
                <form id="bulkDeleteTransactionsForm" action="{{ route('admin.transaction.bulk_delete') }}" method="POST">
                  @csrf
                  <div class="mb-3">
                    <button type="submit" class="btn btn-danger btn-sm">
                      {{ __('Delete Selected') }}
                    </button>
                  </div>
                <div class="table-responsive">
                  <table class="table table-striped mt-3">
                    <thead>
                      <tr>
                        <th scope="col">
                          <input type="checkbox" id="selectAllTransactions">
                        </th>
                        <th scope="col">{{ __('Transaction Id') }}</th>
                        <th scope="col">{{ __('Vendor') }}</th>
                        <th scope="col">{{ __('Transaction Type') }}</th>
                        <th scope="col">{{ __('Payment Method') }}</th>
                        <th scope="col">{{ __('Pre Balance') }}</th>
                        <th scope="col">{{ __('Amount') }}</th>
                        <th scope="col">{{ __('After Balance') }}</th>
                        <th scope="col">{{ __('Status') }}</th>
                        <th scope="col">{{ __('Action') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($transactions as $transaction)
                        <tr>
                          <td>
                            <input type="checkbox" class="transaction-checkbox" name="ids[]"
                              value="{{ $transaction->id }}">
                          </td>
                          <td>{{ '#' . $transaction->transaction_id }}</td>
                          <td>
                            @if ($transaction->vendor)
                              <a
                                href="{{ route('admin.vendor_management.vendor_details', ['id' => $transaction->vendor_id, 'language' => $defaultLang->code]) }}">{{ @$transaction->vendor->username }}</a>
                            @else
                              {{ '-' }}
                            @endif
                          </td>
                          <td>{{ ucwords(str_replace('_', ' ', $transaction->transaction_type)) }}</td>
                          <td>
                            @if ($transaction->transaction_type == 'withdraw' || $transaction->transaction_type =='withdraw_declined')
                              @php
                                $method = $transaction->method()->first();
                              @endphp
                              @if ($method)
                                {{ $method->name }}
                              @else
                                {{ '-' }}
                              @endif
                            @elseif ($transaction->transaction_type == 'balance_subtrac' || $transaction->transaction_type == 'balance_added')
                              {{ '-' }}
                            @else
                              {{ $transaction->payment_method }}
                            @endif
                          </td>
                          <td>
                            @if ($transaction->pre_balance == null)
                              {{ '-' }}
                            @else
                              {{ $transaction->currency_symbol_position == 'left' ? $transaction->currency_symbol : '' }}
                              {{ $transaction->pre_balance }}
                              {{ $transaction->currency_symbol_position == 'right' ? $transaction->currency_symbol : '' }}
                            @endif
                          </td>
                          <td>
                            @if (
                                $transaction->transaction_type == 'withdraw' ||
                                    $transaction->transaction_type == 'balance_subtrac' ||
                                    $transaction->transaction_type == 'featured_service_reject' ||
                                    $transaction->transaction_type == 'booking_refund')
                              <span class="text-danger">(-)</span>
                            @else
                              <span class="text-success">(+)</span>
                            @endif
                            {{ $transaction->currency_symbol_position == 'left' ? $transaction->currency_symbol : '' }}
                            @if ($transaction->transaction_type == 'booking_refund')
                              {{ $transaction->refund_amount }}
                            @endif
                            {{ $transaction->actual_total }}
                            {{ $transaction->currency_symbol_position == 'right' ? $transaction->currency_symbol : '' }}
                          </td>
                          <td>
                            @if ($transaction->after_balance == null)
                              {{ '-' }}
                            @else
                              {{ $transaction->currency_symbol_position == 'left' ? $transaction->currency_symbol : '' }}
                              {{ $transaction->after_balance }}
                              {{ $transaction->currency_symbol_position == 'right' ? $transaction->currency_symbol : '' }}
                            @endif
                          </td>
                          @if ($transaction->payment_status == 'completed')
                            <td>
                              <span class="badge badge-success">{{ __('Paid') }}</span>
                            </td>
                          @elseif($transaction->payment_status == 'rejected')
                            <td>
                              <span class="badge badge-warning">{{ __('Unpaid') }}</span>
                            </td>
                          @else
                            <td>
                              <span class="badge badge-danger">{{ __('Unpaid') }}</span>
                            </td>
                          @endif
                          <td>
                            <button type="button" class="btn btn-danger btn-sm transaction-delete-btn"
                              data-action="{{ route('admin.transaction.delete', ['id' => $transaction->id]) }}">
                              {{ __('Delete') }}
                            </button>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
                </form>
                <form id="singleDeleteTransactionForm" method="POST" class="d-none">
                  @csrf
                </form>
              @endif
            </div>
          </div>
        </div>
        <div class="card-footer">
          <div class="row">
            <div class="d-inline-block mx-auto">
              {{ $transactions->appends(['transaction_id' => request()->input('transaction_id')])->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script>
    'use strict';

    document.addEventListener('change', function (event) {
      if (event.target && event.target.id === 'selectAllTransactions') {
        document.querySelectorAll('.transaction-checkbox').forEach(function (checkbox) {
          checkbox.checked = event.target.checked;
        });
      }
    });

    document.addEventListener('submit', function (event) {
      if (event.target && event.target.id === 'bulkDeleteTransactionsForm') {
        const selected = document.querySelectorAll('.transaction-checkbox:checked');

        if (!selected.length) {
          event.preventDefault();
          alert('{{ __('Please select at least one transaction to delete.') }}');
          return;
        }

        if (!confirm('{{ __('Are you sure you want to delete selected transactions?') }}')) {
          event.preventDefault();
        }
      }
    });

    document.addEventListener('click', function (event) {
      const button = event.target.closest('.transaction-delete-btn');

      if (!button) {
        return;
      }

      const singleForm = document.getElementById('singleDeleteTransactionForm');

      if (!singleForm || !confirm('{{ __('Are you sure you want to delete this transaction?') }}')) {
        return;
      }

      singleForm.action = button.getAttribute('data-action');
      singleForm.submit();
    });
  </script>
@endsection
