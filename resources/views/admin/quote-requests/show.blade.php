@extends('admin.layout')

@section('content')

  <div class="page-header">
    <h4 class="page-title">Quote Request &mdash; {{ $quote->reference_number }}</h4>
    <div class="ml-auto">
      <a href="{{ route('admin.quote_requests.index') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>
  </div>

  @if (Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
  @endif
  @if (Session::has('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
  @endif

  <div class="row">
    <div class="col-lg-8">

      <div class="card mb-3">
        <div class="card-header"><h4 class="card-title">Customer &amp; Service</h4></div>
        <div class="card-body">
          <p><strong>Service:</strong> {{ $quote->serviceName() }}</p>
          <p><strong>Name:</strong> {{ $quote->customer_name }}</p>
          <p><strong>Email:</strong> {{ $quote->customer_email }}</p>
          <p><strong>Phone:</strong> {{ $quote->customer_phone }}</p>
          <p><strong>Postcode:</strong> {{ $quote->customer_postcode }}</p>
          <p><strong>Address:</strong> {{ $quote->customer_address }}</p>
          @if ($quote->preferred_contact_method)
            <p><strong>Preferred contact:</strong> {{ ucfirst($quote->preferred_contact_method) }}</p>
          @endif
          @if ($quote->preferred_date)
            <p><strong>Preferred date:</strong> {{ $quote->preferred_date->format('d M Y') }}</p>
          @endif
          @if ($quote->description)
            <p><strong>Additional description:</strong><br>{{ $quote->description }}</p>
          @endif
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header"><h4 class="card-title">Questions &amp; Answers</h4></div>
        <div class="card-body">
          @forelse ($quote->answers as $answer)
            <div class="mb-2">
              <strong>{{ optional($answer->question)->title ?? 'Question' }}:</strong>
              {{ $answer->answer }}
            </div>
          @empty
            <p class="text-muted">No answers recorded.</p>
          @endforelse
        </div>
      </div>

      @if ($quote->attachments->isNotEmpty())
        <div class="card mb-3">
          <div class="card-header"><h4 class="card-title">Attachments</h4></div>
          <div class="card-body">
            @foreach ($quote->attachments as $attachment)
              <div class="mb-2">
                @if ($attachment->file_type === 'image')
                  <a href="{{ $attachment->url }}" target="_blank"><img src="{{ $attachment->url }}" alt="Attachment" style="height:80px;width:80px;object-fit:cover;border-radius:6px;border:1px solid #ddd;"></a>
                @else
                  <a href="{{ $attachment->url }}" target="_blank"><i class="fas fa-file-alt"></i> {{ $attachment->original_name ?? 'Document' }}</a>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      @endif

      <div class="card mb-3">
        <div class="card-header"><h4 class="card-title">Pricing</h4></div>
        <div class="card-body">
          <form action="{{ route('admin.quote_requests.pricing', $quote->id) }}" method="POST">
            @csrf
            <div class="row">
              <div class="col-md-4 form-group">
                <label>Labour Cost</label>
                <input type="number" step="0.01" name="labour_cost" class="form-control" value="{{ old('labour_cost', $quote->labour_cost) }}">
              </div>
              <div class="col-md-4 form-group">
                <label>Material Cost</label>
                <input type="number" step="0.01" name="material_cost" class="form-control" value="{{ old('material_cost', $quote->material_cost) }}">
              </div>
              <div class="col-md-4 form-group">
                <label>Call-out Charge</label>
                <input type="number" step="0.01" name="callout_charge" class="form-control" value="{{ old('callout_charge', $quote->callout_charge) }}">
              </div>
              <div class="col-md-4 form-group">
                <label>Additional Charges</label>
                <input type="number" step="0.01" name="additional_charges" class="form-control" value="{{ old('additional_charges', $quote->additional_charges) }}">
              </div>
              <div class="col-md-4 form-group">
                <label>Discount</label>
                <input type="number" step="0.01" name="discount" class="form-control" value="{{ old('discount', $quote->discount) }}">
              </div>
              <div class="col-md-4 form-group">
                <label>VAT</label>
                <input type="number" step="0.01" name="vat_amount" class="form-control" value="{{ old('vat_amount', $quote->vat_amount) }}">
              </div>
              <div class="col-md-4 form-group">
                <label>Expiry Date</label>
                <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date', optional($quote->expiry_date)->format('Y-m-d')) }}">
              </div>
              <div class="col-md-8 form-group">
                <label>Expected Duration</label>
                <input type="text" name="expected_duration" class="form-control" placeholder="e.g. Half a day" value="{{ old('expected_duration', $quote->expected_duration) }}">
              </div>
              <div class="col-md-12 form-group">
                <label>Customer-visible Notes</label>
                <textarea name="customer_notes" class="form-control" rows="2">{{ old('customer_notes', $quote->customer_notes) }}</textarea>
              </div>
              <div class="col-md-12 form-group">
                <label>Internal Notes <small class="text-muted">(never shown to the customer)</small></label>
                <textarea name="internal_notes" class="form-control" rows="2">{{ old('internal_notes', $quote->internal_notes) }}</textarea>
              </div>
            </div>
            @if ($quote->total_price)
              <p><strong>Current total:</strong> {{ number_format((float) $quote->total_price, 2) }}</p>
            @endif
            <button type="submit" class="btn btn-primary">Save Pricing</button>
          </form>

          @if (!in_array($quote->status, ['customer_accepted', 'customer_declined', 'converted_to_booking']))
            <form action="{{ route('admin.quote_requests.send', $quote->id) }}" method="POST" class="mt-3" onsubmit="return confirm('Send this quote to the customer now?');">
              @csrf
              <button type="submit" class="btn btn-success" {{ $quote->total_price ? '' : 'disabled' }}>Send Quote to Customer</button>
              @if (!$quote->total_price)
                <small class="text-muted d-block mt-1">Save pricing first.</small>
              @endif
            </form>
          @endif
        </div>
      </div>

    </div>

    <div class="col-lg-4">

      <div class="card mb-3">
        <div class="card-header"><h4 class="card-title">Status</h4></div>
        <div class="card-body">
          <p>Current status: <span class="badge badge-info">{{ $quote->statusLabel() }}</span></p>

          @if (!in_array($quote->status, ['converted_to_booking', 'completed', 'cancelled']))
            <form action="{{ route('admin.quote_requests.status', $quote->id) }}" method="POST">
              @csrf
              <div class="form-group">
                <select name="status" class="form-control">
                  @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" {{ $quote->status == $value ? 'selected' : '' }}>{{ $label }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <textarea name="note" class="form-control" rows="2" placeholder="Optional note"></textarea>
              </div>
              <button type="submit" class="btn btn-sm btn-primary">Update Status</button>
            </form>
          @endif
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header"><h4 class="card-title">Assign Staff</h4></div>
        <div class="card-body">
          <form action="{{ route('admin.quote_requests.assign_staff', $quote->id) }}" method="POST">
            @csrf
            <div class="form-group">
              <select name="staff_id" class="form-control">
                <option value="">-- Unassigned --</option>
                @foreach ($staffMembers as $staff)
                  <option value="{{ $staff->id }}" {{ $quote->assigned_staff_id == $staff->id ? 'selected' : '' }}>{{ $staff->username }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="btn btn-sm btn-primary">Save Assignment</button>
          </form>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header"><h4 class="card-title">History</h4></div>
        <div class="card-body">
          @forelse ($quote->statusHistory as $entry)
            <div class="mb-2 pb-2 border-bottom">
              <div><strong>{{ $entry->new_status }}</strong> <small class="text-muted">{{ $entry->created_at->format('d M Y H:i') }}</small></div>
              <div class="small text-muted">by {{ $entry->changed_by_type === 'admin' ? (optional($entry->admin)->username ?? 'Admin') : ucfirst($entry->changed_by_type) }}</div>
              @if ($entry->note)
                <div class="small">{{ $entry->note }}</div>
              @endif
            </div>
          @empty
            <p class="text-muted">No history yet.</p>
          @endforelse
        </div>
      </div>

    </div>
  </div>

@endsection
