@if ($doc)
  <p class="mb-2">
    <a href="{{ $doc->url }}" target="_blank" rel="noopener noreferrer">
      <i class="fas fa-file mr-1"></i>{{ __('View uploaded document') }}
    </a>
  </p>
  @if ($doc->expiry_date)
    <p class="text-muted mb-2"><small>{{ __('Expires') }}: {{ $doc->expiry_date->format('d M Y') }}</small></p>
  @endif
  @if ($doc->type === 'right_to_work' && $doc->date_of_birth)
    <p class="text-muted mb-2"><small>{{ __('Date of Birth') }}: {{ $doc->date_of_birth->format('d M Y') }}</small></p>
  @endif
  @if ($doc->is_rejected && !empty($doc->rejection_reason))
    <div class="alert alert-danger py-2 px-2">
      <small><strong>{{ __('Rejected') }}:</strong> {{ $doc->rejection_reason }}</small>
    </div>
  @elseif ($doc->is_pending_review)
    <p class="text-info mb-2"><small>{{ __('This document is under review.') }}</small></p>
  @elseif ($doc->is_approved)
    <p class="text-success mb-2"><small>{{ __('This document has been approved.') }}</small></p>
  @endif
@endif
