@extends('company.layouts.app')

@section('title', $job->job_reference ?? $job->title)

@section('content')

@include('company.hr.jobs.partials.styles')

<div class="jobsheet-header">

    <div>
        <h1>
            {{ $job->job_reference }}
            — {{ $job->title }}
        </h1>

        <p>
            <span class="jobsheet-badge">
                {{ ucfirst($job->priority) }}
            </span>

            <span class="jobsheet-badge">
                {{
                    ucwords(
                        str_replace(
                            '_',
                            ' ',
                            $job->status
                        )
                    )
                }}
            </span>

            <span class="jobsheet-badge">
                Admin:
                {{
                    ucwords(
                        str_replace(
                            '_',
                            ' ',
                            $job->admin_status
                        )
                    )
                }}
            </span>
        </p>
    </div>


    <div style="display:flex;gap:10px;">

        @if (
            auth('company')
                ->user()
                ->hasPermission('jobs.manage')
        )
            <a
                href="{{
                    route(
                        'company.hr.jobs.edit',
                        $job
                    )
                }}"
                class="jobsheet-back"
            >
                Edit
            </a>
        @endif

        <a
            href="{{
                route(
                    'company.hr.jobs.index'
                )
            }}"
            class="jobsheet-back"
        >
            Jobs
        </a>

    </div>

</div>


<div class="jobsheet-form-card">

    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">
            Client
        </div>

        <div class="jobsheet-detail-value">
            {{ optional($job->client)->name ?? 'Private customer' }}
        </div>
    </div>


    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">
            Property
        </div>

        <div class="jobsheet-detail-value">
            {{
                optional($job->property)->full_address
                ?: ($job->site_address . ' ' . $job->site_postcode)
            }}
        </div>
    </div>


    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">
            Site Contact
        </div>

        <div class="jobsheet-detail-value">
            {{ $job->customer_name ?? '—' }}

            @if($job->customer_phone)
                — {{ $job->customer_phone }}
            @endif

            @if($job->customer_email)
                — {{ $job->customer_email }}
            @endif
        </div>
    </div>


    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">
            Field Engineer
        </div>

        <div class="jobsheet-detail-value">
            {{
                optional($job->employee)->full_name
                ?? 'Unassigned'
            }}
        </div>
    </div>


    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">
            Appointment
        </div>

        <div class="jobsheet-detail-value">
            {{ $job->scheduled_date->format('d M Y') }}

            {{ $job->scheduled_start_time }}
        </div>
    </div>


    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">
            Started
        </div>

        <div class="jobsheet-detail-value">
            {{
                $job->started_at
                    ? $job->started_at->format(
                        'd M Y H:i'
                    )
                    : 'Not started'
            }}
        </div>
    </div>


    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">
            Finished
        </div>

        <div class="jobsheet-detail-value">
            {{
                $job->finished_at
                    ? $job->finished_at->format(
                        'd M Y H:i'
                    )
                    : '—'
            }}
        </div>
    </div>


    @if($job->actualDurationMinutes() !== null)
        <div class="jobsheet-detail-row">
            <div class="jobsheet-detail-label">
                Actual Job Time
            </div>

            <div class="jobsheet-detail-value">
                {{ $job->actualDurationMinutes() }}
                minutes
            </div>
        </div>
    @endif


    <hr>


    <h3>Payment / Invoice</h3>

    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">
            Amount
        </div>

        <div class="jobsheet-detail-value">
            @if($job->agreed_amount !== null)
                £{{ number_format($job->agreed_amount, 2) }}
            @else
                —
            @endif
        </div>
    </div>


    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">
            Payment Method
        </div>

        <div class="jobsheet-detail-value">
            {{
                $job->payment_method
                    ? ucwords(
                        str_replace(
                            '_',
                            ' ',
                            $job->payment_method
                        )
                    )
                    : '—'
            }}
        </div>
    </div>


    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">
            Payment Status
        </div>

        <div class="jobsheet-detail-value">
            {{
                ucwords(
                    str_replace(
                        '_',
                        ' ',
                        $job->payment_status
                    )
                )
            }}
        </div>
    </div>


    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">
            Invoice
        </div>

        <div class="jobsheet-detail-value">
            {{ $job->invoice_number ?? '—' }}
        </div>
    </div>


    <hr>


    <h3>Certificate</h3>

    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">
            Required
        </div>

        <div class="jobsheet-detail-value">
            {{ $job->certificate_required ? 'Yes' : 'No' }}
        </div>
    </div>


    @if($job->certificate_required)

        <div class="jobsheet-detail-row">
            <div class="jobsheet-detail-label">
                Certificate
            </div>

            <div class="jobsheet-detail-value">
                {{
                    strtoupper(
                        str_replace(
                            '_',
                            ' ',
                            $job->certificate_type
                        )
                    )
                }}
            </div>
        </div>


        <div class="jobsheet-detail-row">
            <div class="jobsheet-detail-label">
                Certificate Status
            </div>

            <div class="jobsheet-detail-value">
                {{
                    ucwords(
                        str_replace(
                            '_',
                            ' ',
                            $job->certificate_status
                        )
                    )
                }}
            </div>
        </div>

    @endif


    <hr>


    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">
            Job Instructions
        </div>

        <div
            class="jobsheet-detail-value"
            style="white-space:pre-line;"
        >
            {{ $job->description ?? '—' }}
        </div>
    </div>


    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">
            Engineer Work Summary
        </div>

        <div
            class="jobsheet-detail-value"
            style="white-space:pre-line;"
        >
            {{ $job->work_summary ?? '—' }}
        </div>
    </div>


    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">
            Internal Notes
        </div>

        <div
            class="jobsheet-detail-value"
            style="white-space:pre-line;"
        >
            {{ $job->internal_notes ?? '—' }}
        </div>
    </div>


    @if (
        $job->admin_status === 'ready_to_close'
        &&
        auth('company')
            ->user()
            ->hasPermission('jobs.manage')
    )

        <form
            method="POST"
            action="{{
                route(
                    'company.hr.jobs.close',
                    $job
                )
            }}"
            style="margin-top:25px;"
        >
            @csrf

            <button
                type="submit"
                class="jobsheet-save"
            >
                Close Job
            </button>
        </form>

    @endif

</div>

@endsection