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

        <div>
            {{
                ucwords(
                    str_replace(
                        '_',
                        ' ',
                        $job->appointment_status
                            ?? 'not_confirmed'
                    )
                )
            }}
        </div>

        @if($job->scheduled_date)

            <div style="margin-top:5px;">

                {{ $job->scheduled_date->format('d M Y') }}

                @if($job->scheduled_start_time)
                    at {{ $job->scheduled_start_time }}
                @endif

            </div>

        @else

            <div
                style="
                    margin-top:5px;
                    color:#ffc46f;
                "
            >
                Date not confirmed
            </div>

        @endif

    </div>
</div>

    <div class="jobsheet-detail-row">
    <div class="jobsheet-detail-label">
        Started
    </div>

    <div class="jobsheet-detail-value">

        @if($job->started_at)

            {{
                $job->started_at
                    ->copy()
                    ->timezone('Europe/London')
                    ->format('d M Y H:i')
            }}

        @else

            Not started

        @endif

    </div>
</div>


    <div class="jobsheet-detail-row">
    <div class="jobsheet-detail-label">
        Finished
    </div>

    <div class="jobsheet-detail-value">

        @if($job->finished_at)

            {{
                $job->finished_at
                    ->copy()
                    ->timezone('Europe/London')
                    ->format('d M Y H:i')
            }}

        @else

            Not finished

        @endif

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


<div style="
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
    flex-wrap:wrap;
">

    <h3>
        Visits / Work Days
    </h3>


    @if(
        auth('company')
            ->user()
            ->hasPermission(
                'jobs.manage'
            )
    )

        <a
            href="{{
                route(
                    'company.hr.jobs.visits.create',
                    $job
                )
            }}"
            class="jobsheet-save"
            style="
                text-decoration:none;
            "
        >
            + Add Visit
        </a>

    @endif

</div>


@if($job->visits->count())

    @foreach(
        $job->visits as $visit
    )

        <div class="
            jobsheet-detail-row
        ">

            <div class="
                jobsheet-detail-label
            ">

                {{
                    $visit
                        ->visit_date
                        ->format(
                            'd M Y'
                        )
                }}

            </div>


            <div class="
                jobsheet-detail-value
            ">

                <strong>

                    {{
                        $visit
                            ->start_time
                            ?: 'No time set'
                    }}

                </strong>


                &middot;

                {{
                    optional(
                        $visit->employee
                    )
                    ->full_name
                    ?? 'Unassigned'
                }}


                &middot;

                {{
                    ucwords(
                        str_replace(
                            '_',
                            ' ',
                            $visit->status
                        )
                    )
                }}


                @if(
                    $visit
                        ->expected_duration_minutes
                )

                    &middot;

                    {{
                        $visit
                            ->expected_duration_minutes
                    }}
                    minutes

                @endif


                @if(
                    auth('company')
                        ->user()
                        ->hasPermission(
                            'jobs.manage'
                        )
                )

                    &middot;

                    <a
                        href="{{
                            route(
                                'company.hr.jobs.visits.edit',
                                [
                                    $job,
                                    $visit
                                ]
                            )
                        }}"
                    >
                        Edit
                    </a>

                @endif


                @if($visit->notes)

                    <div
                        style="
                            margin-top:6px;
                            color:#aab4ca;
                        "
                    >

                        {{
                            $visit
                                ->notes
                        }}

                    </div>

                @endif

            </div>

        </div>

    @endforeach

@else

    <div
        style="
            color:#aab4ca;
            margin-bottom:20px;
        "
    >
        No visits scheduled yet.
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