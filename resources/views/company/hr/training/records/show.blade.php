@extends('company.layouts.app')

@section('title', 'Training Record')

@section('content')

<style>
    .training-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .training-detail-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .training-detail-header p {
        margin: 0;
        color: #aab4ca;
    }

    .training-detail-back {
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

    .training-detail-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }

    .training-detail-panel {
        padding: 22px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .training-detail-panel h2 {
        margin: 0 0 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        font-size: 19px;
    }

    .training-detail-row {
        display: grid;
        grid-template-columns: 190px 1fr;
        gap: 15px;
        padding: 11px 0;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .training-detail-label {
        color: #aab4ca;
    }

    .training-status-badge {
        display: inline-flex;
        padding: 5px 10px;
        border-radius: 14px;
        background: rgba(41,120,220,0.18);
        color: #75aff7;
    }

    .training-complete-form {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .training-complete-form label {
        font-weight: 600;
    }

    .training-complete-form input,
    .training-complete-form textarea {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
    }

    .training-complete-form textarea {
        min-height: 95px;
        resize: vertical;
    }

    .training-complete-button,
    .training-download-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 16px;
        border: 0;
        border-radius: 7px;
        color: #ffffff;
        text-decoration: none;
        cursor: pointer;
    }

    .training-complete-button {
        background: #28965a;
    }

    .training-download-button {
        width: 100%;
        background: #2878dc;
    }

    .training-alert {
        padding: 13px 16px;
        margin-bottom: 18px;
        border-radius: 7px;
    }

    .training-alert-success {
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .training-alert-error {
        background: rgba(255,93,103,0.16);
        color: #ff8a92;
    }

    @media (max-width: 850px) {
        .training-detail-header {
            flex-direction: column;
        }

        .training-detail-grid {
            grid-template-columns: 1fr;
        }

        .training-detail-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="training-detail-header">

    <div>
        <h1>Training Record</h1>

        <p>
            {{ optional($record->employee)->first_name }}
            {{ optional($record->employee)->last_name }}
        </p>
    </div>

    <a href="{{ route(
            'company.hr.training.records.index'
        ) }}"
       class="training-detail-back">
        <i class="fas fa-arrow-left"></i>
        Employee Training
    </a>

</div>

@if (session('success'))
    <div class="
        training-alert
        training-alert-success
    ">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="
        training-alert
        training-alert-error
    ">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="training-detail-grid">

    <div class="training-detail-panel">

        <h2>Training Details</h2>

        <div class="training-detail-row">
            <div class="training-detail-label">
                Employee
            </div>

            <div>
                {{ optional($record->employee)->first_name }}
                {{ optional($record->employee)->last_name }}
                —
                {{ optional(
                    $record->employee
                )->employee_number }}
            </div>
        </div>

        <div class="training-detail-row">
            <div class="training-detail-label">
                Course
            </div>

            <div>
                {{ optional(
                    $record->trainingCourse
                )->name ?? 'Unknown course' }}
            </div>
        </div>

        <div class="training-detail-row">
            <div class="training-detail-label">
                Provider
            </div>

            <div>
                {{ $record->provider_name ?: '—' }}
            </div>
        </div>

        <div class="training-detail-row">
            <div class="training-detail-label">
                Scheduled Date
            </div>

            <div>
                {{ optional(
                    $record->scheduled_date
                )->format('d M Y') ?? '—' }}
            </div>
        </div>

        <div class="training-detail-row">
            <div class="training-detail-label">
                Status
            </div>

            <div>
                <span class="training-status-badge">
                    {{ $record->status_name }}
                </span>
            </div>
        </div>

        <div class="training-detail-row">
            <div class="training-detail-label">
                Completion Date
            </div>

            <div>
                {{ optional(
                    $record->completed_at
                )->format('d M Y') ?? '—' }}
            </div>
        </div>

        <div class="training-detail-row">
            <div class="training-detail-label">
                Expiry Date
            </div>

            <div>
                {{ optional(
                    $record->expiry_date
                )->format('d M Y') ?? '—' }}
            </div>
        </div>

        <div class="training-detail-row">
            <div class="training-detail-label">
                Certificate Number
            </div>

            <div>
                {{ $record->certificate_number ?: '—' }}
            </div>
        </div>

        <div class="training-detail-row">
            <div class="training-detail-label">
                Cost
            </div>

            <div>
                {{ $record->cost !== null
                    ? '£' . number_format(
                        $record->cost,
                        2
                    )
                    : '—'
                }}
            </div>
        </div>

        <div class="training-detail-row">
            <div class="training-detail-label">
                Notes
            </div>

            <div style="white-space:pre-line;">
                {{ $record->notes ?: '—' }}
            </div>
        </div>

    </div>

    <div class="training-detail-panel">

        @if (
            ! in_array(
                $record->status,
                ['completed', 'expired', 'cancelled'],
                true
            ) &&
            auth('company')->user()
                ->hasPermission('training.complete')
        )
            @if (
                ! in_array(
                    $record->status,
                    ['completed', 'expired', 'cancelled'],
                    true
                ) &&
                auth('company')->user()
                    ->hasPermission('training.edit')
            )
                <a href="{{ route(
                        'company.hr.training.records.edit',
                        $record
                    ) }}"
                style="
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    min-height:42px;
                    margin-bottom:16px;
                    border-radius:7px;
                    background:#2878dc;
                    color:#ffffff;
                    text-decoration:none;
                ">
                    Edit Training
                </a>
            @endif

            <h2>Mark Completed</h2>

            <form method="POST"
                  enctype="multipart/form-data"
                  action="{{ route(
                      'company.hr.training.records.complete',
                      $record
                  ) }}"
                  class="training-complete-form">

                @csrf

                <div>
                    <label for="completed_date">
                        Completion Date
                    </label>

                    <input type="date"
                           name="completed_date"
                           id="completed_date"
                           value="{{ old(
                               'completed_date',
                               today()->toDateString()
                           ) }}"
                           max="{{ today()->toDateString() }}"
                           required>
                </div>

                <div>
                    <label for="certificate_number">
                        Certificate Number
                    </label>

                    <input type="text"
                           name="certificate_number"
                           id="certificate_number"
                           maxlength="150"
                           value="{{ old(
                               'certificate_number'
                           ) }}">
                </div>

                <div>
                    <label for="certificate">
                        Certificate
                    </label>

                    <input type="file"
                           name="certificate"
                           id="certificate"
                           accept=".pdf,.jpg,.jpeg,.png"
                           {{ optional(
                               $record->trainingCourse
                           )->requires_certificate
                               ? 'required'
                               : ''
                           }}>

                    <small style="color:#aab4ca;">
                        PDF, JPG or PNG. Maximum 5 MB.
                    </small>
                </div>

                <div>
                    <label for="completion_notes">
                        Completion Notes
                    </label>

                    <textarea name="completion_notes"
                              id="completion_notes">{{ old(
                                  'completion_notes'
                              ) }}</textarea>
                </div>

                <button type="submit"
                        class="training-complete-button"
                        onclick="return confirm(
                            'Mark this training as completed?'
                        )">
                    Mark as Completed
                </button>

            </form>
     
                    @if (
            in_array(
                $record->status,
                ['not_started', 'scheduled', 'in_progress'],
                true
            ) &&
            auth('company')->user()
                ->hasPermission('training.edit')
        )
            <hr style="
                margin:22px 0;
                border:0;
                border-top:1px solid rgba(255,255,255,0.08);
            ">

            <h2>Cancel Training</h2>

            <form method="POST"
                action="{{ route(
                    'company.hr.training.records.cancel',
                    $record
                ) }}">

                @csrf

                <textarea
                    name="cancellation_reason"
                    required
                    maxlength="2000"
                    placeholder="Enter cancellation reason"
                    style="
                        width:100%;
                        min-height:90px;
                        padding:11px 12px;
                        margin-bottom:12px;
                        border:1px solid rgba(255,255,255,0.12);
                        border-radius:7px;
                        background:#151f35;
                        color:#ffffff;
                    "
                ></textarea>

                <button type="submit"
                        onclick="return confirm(
                            'Cancel this training record?'
                        )"
                        style="
                            width:100%;
                            min-height:42px;
                            border:0;
                            border-radius:7px;
                            background:#b54a52;
                            color:#ffffff;
                            cursor:pointer;
                        ">
                    Cancel Training
                </button>

            </form>
        @endif

      @else


            <h2>Certificate</h2>

            @if (
                $record->certificate_path &&
                auth('company')->user()
                    ->hasPermission(
                        'training-certificates.download'
                    )
            )
                <a href="{{ route(
                        'company.hr.training.records.certificate.download',
                        $record
                    ) }}"
                   class="training-download-button">
                    <i class="fas fa-download"></i>
                    Download Certificate
                </a>
            @elseif ($record->certificate_path)
                <p style="color:#aab4ca;">
                    Certificate uploaded. You do not have
                    download permission.
                </p>
            @else
                <p style="color:#aab4ca;">
                    No certificate has been uploaded.
                </p>
            @endif

        @endif

    </div>

</div>

@endsection