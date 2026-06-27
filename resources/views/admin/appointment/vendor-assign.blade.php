@php
  $busyVendorIds = isset($vendorBusyMap) ? ($vendorBusyMap[$item->id] ?? []) : [];
  $eligibleVendorIds = isset($vendorEligibleMap) ? ($vendorEligibleMap[$item->id] ?? []) : null;
  if (is_array($eligibleVendorIds) && $item->vendor_id && !in_array($item->vendor_id, $eligibleVendorIds)) {
      $eligibleVendorIds[] = $item->vendor_id; // keep the currently-assigned vendor visible
  }
  $assignVendors = is_array($eligibleVendorIds)
      ? $vendors->whereIn('id', $eligibleVendorIds)->values()
      : $vendors;
@endphp
<div class="modal fade" id="vendorAssignModal_{{ $item->id }}" tabindex="-1" role="dialog"
  aria-labelledby="vendorAssignModalTitle_{{ $item->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="vendorAssignModalTitle_{{ $item->id }}">{{ __('Assign Vendor') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="vendorAssignForm_{{ $item->id }}" class="vendor-assign-form"
          action="{{ route('admin.appointment.vendor_assign') }}" method="post">
          @csrf
          <input type="hidden" name="appointment_id" value="{{ $item->id }}">
          <input type="hidden" name="force" class="vendor-assign-force" value="0">

          <div class="form-group">
            <label>{{ __('Vendor') . ' *' }}</label>
            <select name="vendor_id" class="form-control">
              <option value="0" {{ $item->vendor_id == 0 ? 'selected' : '' }}>{{ __('None (Admin)') }}</option>
              @foreach ($assignVendors as $vendor)
                <option value="{{ $vendor->id }}" data-busy="{{ in_array($vendor->id, $busyVendorIds) ? 1 : 0 }}"
                  {{ $item->vendor_id == $vendor->id ? 'selected' : '' }}>
                  {{ in_array($vendor->id, $busyVendorIds) ? '⚠ ' : '' }}{{ $vendor->vendor_infos->first() ? $vendor->vendor_infos->first()->name : $vendor->username }}
                  ({{ $vendor->email }}){{ in_array($vendor->id, $busyVendorIds) ? ' — ' . __('busy') : '' }}
                </option>
              @endforeach
              @if ($assignVendors->isEmpty())
                <option value="" disabled>{{ __('No matching vendors for this service category') }}</option>
              @endif
            </select>
            <p class="mt-1 mb-0 text-danger em vendor-assign-error"></p>
            <small class="text-muted">{{ __('⚠ marks vendors who are busy or unavailable for this appointment slot.') }}</small>
          </div>

          <p class="text-muted small">{{ __('Note: Changing the vendor will also clear the assigned staff.') }}</p>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('Close') }}</button>
        <button type="button" class="btn btn-warning btn-sm vendor-assign-force-btn" style="display:none;"
          data-form="#vendorAssignForm_{{ $item->id }}">{{ __('Assign anyway') }}</button>
        <button type="button" class="btn btn-primary btn-sm vendor-assign-submit"
          data-form="#vendorAssignForm_{{ $item->id }}">{{ __('Save') }}</button>
      </div>
    </div>
  </div>
</div>

@once
  @section('script')
    @parent
    <script>
      (function () {
        function submitVendorAssign(button, force) {
          var form = $(button.data('form'));
          var error = form.find('.vendor-assign-error');
          var forceBtn = form.closest('.modal-content').find('.vendor-assign-force-btn');
          var saveBtn = form.closest('.modal-content').find('.vendor-assign-submit');
          var alertBox = $('.vendor-conflict-alert');

          form.find('.vendor-assign-force').val(force ? '1' : '0');
          saveBtn.attr('disabled', true);
          forceBtn.attr('disabled', true);
          error.removeClass('text-warning').addClass('text-danger').text('');

          $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function (response) {
              if (response.status === 'success') {
                location.reload();
              }
            },
            error: function (xhr) {
              saveBtn.attr('disabled', false);
              forceBtn.attr('disabled', false);
              var res = xhr.responseJSON || {};

              if (xhr.status === 409 && res.status === 'conflict') {
                error.removeClass('text-danger').addClass('text-warning').text(res.message);
                forceBtn.show();
                if (alertBox.length) {
                  alertBox.removeClass('d-none').html(
                    '<i class="fas fa-exclamation-triangle"></i> ' + $('<div>').text(res.message).html()
                  );
                }
                return;
              }

              var errors = res.errors ? res.errors : {};
              error.text(errors.vendor_id ? errors.vendor_id[0] : @json(__('Something went wrong.')));
            }
          });
        }

        $(document).on('click', '.vendor-assign-submit', function () {
          submitVendorAssign($(this), false);
        });

        $(document).on('click', '.vendor-assign-force-btn', function () {
          submitVendorAssign($(this), true);
        });

        // Reset the override state whenever the vendor selection changes.
        $(document).on('change', '.vendor-assign-form select[name="vendor_id"]', function () {
          var content = $(this).closest('.modal-content');
          content.find('.vendor-assign-force-btn').hide();
          content.find('.vendor-assign-force').val('0');
          content.find('.vendor-assign-error').text('');
        });
      })();
    </script>
  @endsection
@endonce
