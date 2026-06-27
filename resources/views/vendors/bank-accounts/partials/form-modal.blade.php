<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">{{ $title }}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="{{ $action }}" method="post">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="form-group">
                <label>{{ __('Account Holder Name') . ' *' }}</label>
                <input type="text" class="form-control" name="account_holder_name" value="{{ old('account_holder_name', optional($account)->account_holder_name) }}">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group">
                <label>{{ __('Bank Name') . ' *' }}</label>
                <input type="text" class="form-control" name="bank_name" value="{{ old('bank_name', optional($account)->bank_name) }}">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group">
                <label>{{ __('Account Number') . ' *' }}</label>
                <input type="text" class="form-control" name="account_number" value="{{ old('account_number', optional($account)->account_number) }}">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group">
                <label>{{ __('IBAN') }}</label>
                <input type="text" class="form-control" name="iban" value="{{ old('iban', optional($account)->iban) }}">
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label>{{ __('Branch Name') }}</label>
                <input type="text" class="form-control" name="branch_name" value="{{ old('branch_name', optional($account)->branch_name) }}">
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label>{{ __('Swift Code') }}</label>
                <input type="text" class="form-control" name="swift_code" value="{{ old('swift_code', optional($account)->swift_code) }}">
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group">
                <label>{{ __('Routing Number') }}</label>
                <input type="text" class="form-control" name="routing_number" value="{{ old('routing_number', optional($account)->routing_number) }}">
              </div>
            </div>
            <div class="col-lg-12">
              <div class="form-group">
                <label>{{ __('Notes') }}</label>
                <textarea class="form-control" name="notes" rows="3">{{ old('notes', optional($account)->notes) }}</textarea>
              </div>
            </div>
            @if ($showActive)
              <div class="col-lg-12">
                <div class="form-group">
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="form-check-input" type="checkbox" name="is_active" value="1">
                      <span class="form-check-sign">{{ __('Make this account active') }}</span>
                    </label>
                  </div>
                </div>
              </div>
            @endif
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-primary btn-sm">{{ __('Save') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
