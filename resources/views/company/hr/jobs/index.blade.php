@extends('company.layouts.app')

@section('title', 'Jobs')

@section('content')

@include('company.hr.jobs.partials.styles')


<div class="jobsheet-header">

    <div>
        <h1>Jobs</h1>

        <p>
            Manage scheduled and
            unscheduled company jobs.
        </p>
    </div>


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
                    'company.hr.jobs.create'
                )
            }}"
            class="jobsheet-add"
        >
            <i class="fas fa-plus"></i>

            New Job
        </a>

    @endif

</div>


{{-- UNSCHEDULED JOBS --}}
@if(
    isset($unscheduledJobs)
    &&
    $unscheduledJobs->count()
)

    <div
        class="jobsheet-day-card"
        style="
            border:
                1px solid
                rgba(
                    255,
                    179,
                    71,
                    0.30
                );
        "
    >

        <div
            class="jobsheet-day-title"
            style="
                color:#ffc46f;
            "
        >
            <i class="fas fa-clock"></i>

            Unscheduled /
            Date Not Confirmed

            ({{ $unscheduledJobs->count() }})
        </div>


        @foreach(
            $unscheduledJobs
            as $job
        )

            <div
                class="jobsheet-job-row"
            >

                <div
                    class="jobsheet-job-main"
                >

                    <a
                        href="{{
                            route(
                                'company.hr.jobs.show',
                                $job
                            )
                        }}"
                    >
                        {{
                            $job->job_reference
                                ?: 'Job #'
                                    .$job->id
                        }}

                        —

                        {{ $job->title }}
                    </a>


                    <div
                        class="jobsheet-job-meta"
                    >

                        @if($job->client)

                            Client:
                            {{ $job->client->name }}

                            &middot;

                        @endif


                        {{
                            ucwords(
                                str_replace(
                                    '_',
                                    ' ',
                                    $job
                                        ->appointment_status
                                        ?? 'not_confirmed'
                                )
                            )
                        }}


                        @if(
                            $job->site_postcode
                        )

                            &middot;

                            {{
                                $job
                                    ->site_postcode
                            }}

                        @endif


                        @if($job->employee)

                            &middot;

                            Engineer:
                            {{
                                $job
                                    ->employee
                                    ->full_name
                            }}

                        @else

                            &middot;
                            Unassigned

                        @endif

                    </div>

                </div>


                <div
                    style="
                        display:flex;
                        gap:8px;
                        align-items:center;
                    "
                >

                    <span
                        class="
                            jobsheet-badge
                            jobsheet-badge-{{
                                $job->priority
                            }}
                        "
                    >
                        {{
                            ucfirst(
                                $job->priority
                            )
                        }}
                    </span>


                    <span
                        class="
                            jobsheet-badge
                            jobsheet-status-awaiting_review
                        "
                    >
                        Action Required
                    </span>

                </div>

            </div>

        @endforeach

    </div>

@endif


{{-- WEEKLY SCHEDULE --}}
@foreach(
    range(0, 6)
    as $offset
)

    @php(
        $day =
            $weekStart
                ->copy()
                ->addDays(
                    $offset
                )
    )

    @php(
        $dayJobs =
            $jobs->get(
                $day
                    ->toDateString(),
                collect()
            )
    )


    <div class="jobsheet-day-card">

        <div class="jobsheet-day-title">

            {{
                $day->format(
                    'l, d M Y'
                )
            }}

        </div>


        @forelse(
            $dayJobs
            as $job
        )

            <div
                class="jobsheet-job-row"
            >

                <div
                    class="jobsheet-job-main"
                >

                    <a
                        href="{{
                            route(
                                'company.hr.jobs.show',
                                $job
                            )
                        }}"
                    >
                        @if(
                            $job->job_reference
                        )

                            {{
                                $job
                                    ->job_reference
                            }}
                            —

                        @endif

                        {{ $job->title }}
                    </a>


                    <div
                        class="jobsheet-job-meta"
                    >

                        {{
                            $job
                                ->scheduled_start_time
                                ?? 'No time set'
                        }}

                        &middot;

                        {{
                            optional(
                                $job->employee
                            )
                            ->full_name
                            ?? 'Unassigned'
                        }}


                        @if($job->client)

                            &middot;

                            {{
                                $job
                                    ->client
                                    ->name
                            }}

                        @endif


                        @if(
                            $job->customer_name
                        )

                            &middot;

                            {{
                                $job
                                    ->customer_name
                            }}

                        @endif


                        @if(
                            $job
                                ->actualDurationMinutes()
                            !== null
                        )

                            &middot;

                            {{
                                $job
                                    ->actualDurationMinutes()
                            }}m actual


                            @php(
                                $diff =
                                    $job
                                        ->durationDifferenceMinutes()
                            )


                            @if(
                                $diff !== null
                                &&
                                $diff !== 0
                            )

                                <span
                                    class="{{
                                        $diff > 0
                                            ? 'jobsheet-duration-over'
                                            : 'jobsheet-duration-under'
                                    }}"
                                >

                                    (
                                    {{
                                        $diff > 0
                                            ? '+'
                                            : ''
                                    }}
                                    {{
                                        $diff
                                    }}m
                                    )

                                </span>

                            @endif

                        @endif

                    </div>

                </div>


                <div
                    style="
                        display:flex;
                        gap:8px;
                        align-items:center;
                    "
                >

                    <span
                        class="
                            jobsheet-badge
                            jobsheet-badge-{{
                                $job->priority
                            }}
                        "
                    >
                        {{
                            ucfirst(
                                $job->priority
                            )
                        }}
                    </span>


                    <span
                        class="
                            jobsheet-badge
                            jobsheet-status-{{
                                $job->status
                            }}
                        "
                    >
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

                </div>

            </div>

        @empty

            <div
                class="jobsheet-job-meta"
            >
                No jobs scheduled.
            </div>

        @endforelse

    </div>

@endforeach


@endsection