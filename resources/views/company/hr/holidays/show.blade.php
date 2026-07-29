@extends('company.layouts.app')

@section('title', 'Holiday Request')

@section('content')

<style>
    .holiday-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .holiday-detail-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .holiday-detail-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 15px;
        border-radius: 7px;
        background: #3a4967;
        color: #ffffff;
        text-decoration: none;
    }

    .holiday-detail-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }

    .holiday-detail-panel {
        padding: 22px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .holiday-detail-panel h2 {
        margin: 0 0 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        font-size: 19px;
    }

    .holiday-detail-row {
        display: grid;
        grid-template-columns: 180px 1fr;
        gap: 15px;
        padding: 11px 0;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .holiday-detail-label {
        color: #aab4ca;
    }

    .holiday-action-form {
        margin-bottom: 18px;
    }

    .holiday-action-form textarea {
        width: 100%;
        min-height: 85px;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 6px;
        background: #151f35;
        color: #ffffff;
    }

    .holiday-action-button {
        width: 100%;
        min-height: 41px;
        border: 0;
        border-radius: 6px;
        color: #ffffff;
        cursor: pointer;
    }

    .holiday-approve {
        background: #28965a;
    }

    .holiday-reject {
        background: #b54a52;
    }

    .holiday-cancel {
        background: #6c7485;
    }

    .holiday-alert {
        padding: 13px 16px;
        margin-bottom: 18px;
        border-radius: 7px;
    }

    .holiday-alert-success {
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .holiday-alert-error {
        background: rgba(255,93,103,0.16);
        color: #ff8a92;
    }

    @media (max-width: 850px) {
        .holiday-detail-header {
            flex-direction: column;
        }

        .holiday-detail-grid {
            grid-template-columns: 1fr;
        }

        .holiday-detail-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="holiday-detail-header">

    <div>
        <h1>
            Holiday Request
        </h1>

        <p>
            {{ optional($holiday->employee)->first_name }}
            {{ optional($holiday->employee)->last_name }}
        </p>
    </div>

    <a href="{{ route(
            'company.hr.holidays.requests'
        ) }}"
       class="holiday-detail-back">
        <i class="fas fa-arrow-left"></i>
        Holiday Requests
    </a>

</div>

@if (session('success'))
    <div class="holiday-alert holiday-alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="holiday-alert holiday-alert-error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="holiday-detail-grid">

    <div class="holiday-detail-panel">

        <h2>Request Details</h2>

        <div class="holiday-detail-row">
            <div class="holiday-detail-label">Employee</div>

            <div>
                {{ optional($holiday->employee)->first_name }}
                {{ optional($holiday->employee)->last_name }}
                —
                {{ optional(
                    $holiday->employee
                )->employee_number }}
            </div>
        </div>

        <div class="holiday-detail-row">
            <div class="holiday-detail-label">Holiday Type</div>
            <div>{{ $holiday->holiday_type_name }}</div>
        </div>

        <div class="holiday-detail-row">
            <div class="holiday-detail-label">Dates</div>

            <div>
                {{ $holiday->start_date->format('d M Y') }}
                –
                {{ $holiday->end_date->format('d M Y') }}
            </div>
        </div>

        <div class="holiday-detail-row">
            <div class="holiday-detail-label">Total Days</div>

            <div>
                {{ number_format($holiday->total_days, 2) }}
            </div>
        </div>

        <div class="holiday-detail-row">
            <div class="holiday-detail-label">Status</div>
            <div>{{ $holiday->status_name }}</div>
        </div>

        <div class="holiday-detail-row">
            <div class="holiday-detail-label">Reason</div>
            <div>{{ $holiday->reason ?: '—' }}</div>
        </div>

        <div class="holiday-detail-row">
            <div class="holiday-detail-label">Internal Notes</div>
            <div>{{ $holiday->notes ?: '—' }}</div>
        </div>

        @if ($allowance)
            <div class="holiday-detail-row">
                <div class="holiday-detail-label">
                    Remaining Allowance
                </div>

                <div>
                    {{ number_format(
                        $allowance->remaining_allowance,
                        2
                    ) }}
                    days
                </div>
            </div>
        @endif

        @if ($holiday->rejection_reason)
            <div class="holiday-detail-row">
                <div class="holiday-detail-label">
                    Rejection Reason
                </div>

                <div>{{ $holiday->rejection_reason }}</div>
            </div>
        @endif

        @if ($holiday->cancellation_reason)
            <div class="holiday-detail-row">
                <div class="holiday-detail-label">
                    Cancellation Reason
                </div>

                <div>{{ $holiday->cancellation_reason }}</div>
            </div>
        @endif

    </div>

    <div class="holiday-detail-panel">

        <h2>Actions</h2>

        @if (
            $holiday->status === 'pending' &&
            auth('company')->user()
                ->hasPermission('holidays.approve')
        )
            <form method="POST"
                  action="{{ route(
                      'company.hr.holidays.approve',
                      $holiday
                  ) }}"
                  class="holiday-action-form">

                @csrf

                <button type="submit"
                        class="
                            holiday-action-button
                            holiday-approve
                        "
                        onclick="return confirm(
                            'Approve this holiday request?'
                        )">
                    Approve Request
                </button>

            </form>

            <form method="POST"
                  action="{{ route(
                      'company.hr.holidays.reject',
                      $holiday
                  ) }}"
                  class="holiday-action-form">

                @csrf

                <textarea
                    name="rejection_reason"
                    placeholder="Enter rejection reason"
                    required
                ></textarea>

                <button type="submit"
                        class="
                            holiday-action-button
                            holiday-reject
                        ">
                    Reject Request
                </button>

            </form>
        @endif

        @if (
            in_array(
                $holiday->status,
                ['pending', 'approved'],
                true
            ) &&
            auth('company')->user()
                ->hasPermission('holidays.cancel')
        )
            <form method="POST"
                  action="{{ route(
                      'company.hr.holidays.cancel',
                      $holiday
                  ) }}"
                  class="holiday-action-form">

                @csrf

                <textarea
                    name="cancellation_reason"
                    placeholder="Enter cancellation reason"
                    required
                ></textarea>

                <button type="submit"
                        class="
                            holiday-action-button
                            holiday-cancel
                        ">
                    Cancel Holiday
                </button>

            </form>
        @endif

        @if (
            ! in_array(
                $holiday->status,
                ['pending', 'approved'],
                true
            )
        )
            <p style="color:#aab4ca;">
                No further actions are available for this request.
            </p>
        @endif

    </div>

</div>

@endsection