@if (count($bankAccounts) == 0)
  <h3 class="text-center">{{ __('NO BANK ACCOUNT FOUND') }}!</h3>
@else
  <div class="table-responsive">
    <table class="table table-striped mt-3">
      <thead>
        <tr>
          @if (!isset($hideVendor) || !$hideVendor)
            <th scope="col">{{ __('Vendor') }}</th>
          @endif
          <th scope="col">{{ __('Account Holder') }}</th>
          <th scope="col">{{ __('Bank') }}</th>
          <th scope="col">{{ __('Account Number') }}</th>
          <th scope="col">{{ __('IBAN') }}</th>
          <th scope="col">{{ __('Branch') }}</th>
          <th scope="col">{{ __('Swift Code') }}</th>
          <th scope="col">{{ __('Routing Number') }}</th>
          <th scope="col">{{ __('Status') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($bankAccounts as $account)
          <tr>
            @if (!isset($hideVendor) || !$hideVendor)
              <td>
                @if ($account->vendor)
                  <a href="{{ route('admin.vendor_management.vendor_details', ['id' => $account->vendor->id, 'language' => $defaultLang->code]) }}">
                    {{ $account->vendor->username }}
                  </a>
                @else
                  {{ '-' }}
                @endif
              </td>
            @endif
            <td>{{ $account->account_holder_name }}</td>
            <td>{{ $account->bank_name }}</td>
            <td>{{ $account->account_number }}</td>
            <td>{{ $account->iban ?? '-' }}</td>
            <td>{{ $account->branch_name ?? '-' }}</td>
            <td>{{ $account->swift_code ?? '-' }}</td>
            <td>{{ $account->routing_number ?? '-' }}</td>
            <td>
              @if ($account->is_active)
                <span class="badge badge-success">{{ __('Active') }}</span>
              @else
                <span class="badge badge-secondary">{{ __('Inactive') }}</span>
              @endif
            </td>
          </tr>
          @if (!empty($account->notes))
            <tr>
              <td colspan="{{ (!isset($hideVendor) || !$hideVendor) ? 9 : 8 }}">
                <strong>{{ __('Notes') }}:</strong> {{ $account->notes }}
              </td>
            </tr>
          @endif
        @endforeach
      </tbody>
    </table>
  </div>
@endif
