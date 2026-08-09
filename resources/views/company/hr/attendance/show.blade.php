@extends('company.layouts.app')

@section('title', 'Attendance Details')

@section('content')

@include('company.hr.attendance.partials.styles')


<div class="attendance-header">

    <div>

        <h1>
            {{ $employee->full_name }}
        </h1>

        <p>
            Attendance details for
            {{ $date->format('d F Y') }}
        </p>

    </div>


    <a
        href="{{ route('company.hr.attendance.index') }}"
        class="attendance-back"
    >
        <i class="fas fa-arrow-left"></i>
        Attendance
    </a>

</div>


{{-- SUMMARY --}}
<div class="attendance-summary-grid">

    <div class="attendance-summary-card">

        <span>First Clock In</span>

        <strong>
            @if($firstIn)
                {{ $firstIn->format('H:i') }}
            @else
                —
            @endif
        </strong>

    </div>


    <div class="attendance-summary-card">

        <span>Last Clock Out</span>

        <strong>
            @if($lastOut)
                {{ $lastOut->format('H:i') }}
            @else
                —
            @endif
        </strong>

    </div>


    <div class="attendance-summary-card">

        <span>Total Worked</span>

        <strong>
            @php
                $hours = intdiv(
                    $workedMinutes,
                    60
                );

                $minutes =
                    $workedMinutes % 60;
            @endphp

            {{ $hours }}h {{ $minutes }}m
        </strong>

    </div>


    <div class="attendance-summary-card">

        <span>Attendance Events</span>

        <strong>
            {{ $records->count() }}
        </strong>

    </div>

</div>


{{-- ISSUES --}}
@if(count($issues))

    <div
        class="
            attendance-alert
            attendance-alert-warning
        "
    >

        <strong>
            <i class="fas fa-triangle-exclamation"></i>
            Attendance needs attention
        </strong>

        <ul>
            @foreach($issues as $issue)
                <li>{{ $issue }}</li>
            @endforeach
        </ul>

    </div>

@endif


{{-- ACTIVITY --}}
<div class="attendance-card">

    <div class="attendance-section-heading">

        <div>

            <h3>Activity</h3>

            <p>
                Clock activity for this employee
                on this day.
            </p>

        </div>

    </div>


    <div class="attendance-timeline">

        @foreach($records as $record)

            @php
                $localTime =
                    $record
                        ->server_timestamp
                        ->copy()
                        ->timezone(
                            $timezone
                        );
            @endphp


            <div class="attendance-timeline-item">

                <div class="attendance-timeline-time">

                    {{ $localTime->format('H:i') }}

                </div>


                <div
                    class="
                        attendance-timeline-marker
                        {{
                            $record->clock_type === 'in'
                                ? 'in'
                                : 'out'
                        }}
                    "
                ></div>


                <div class="attendance-timeline-content">

                    <div class="attendance-timeline-title">

                        @if($record->clock_type === 'in')
                            Clock In
                        @else
                            Clock Out
                        @endif

                    </div>


                    <div class="attendance-timeline-meta">

                        {{-- FIELD JOB --}}
                        @if($record->job)

                            <div>

                                <strong>
                                    Field Job:
                                </strong>

                                <a
                                    href="{{
                                        route(
                                            'company.hr.jobs.show',
                                            $record->job
                                        )
                                    }}"
                                >

                                    {{
                                        $record
                                            ->job
                                            ->job_reference
                                            ?: 'Job #'
                                                . $record
                                                    ->job
                                                    ->id
                                    }}

                                    —

                                    {{
                                        $record
                                            ->job
                                            ->title
                                    }}

                                </a>

                            </div>


                        {{-- STAFF SHIFT --}}
                        @elseif($record->shift)

                            <div>

                                <strong>
                                    Source:
                                </strong>

                                Staff Shift

                            </div>


                        {{-- MANUAL --}}
                        @elseif($record->is_manual_entry)

                            <div>

                                <strong>
                                    Source:
                                </strong>

                                Manual Record

                            </div>


                        {{-- APP --}}
                        @else

                            <div>

                                <strong>
                                    Source:
                                </strong>

                                Employee App

                            </div>

                        @endif


                        @if($record->is_manual_entry)

                            <div>

                                Added by:

                                {{
                                    optional(
                                        $record->recordedBy
                                    )
                                    ->full_name
                                    ?? 'Admin'
                                }}

                            </div>

                        @endif


                        @if($record->override_reason)

                            <div>

                                Reason:
                                {{ $record->override_reason }}

                            </div>

                        @endif

                    </div>

                </div>

            </div>

        @endforeach

    </div>

</div>


{{-- LOCATION EVIDENCE --}}
<div class="attendance-card">

    <div class="attendance-section-heading">

        <div>

            <h3>Location Evidence</h3>

            <p>
                GPS information captured
                during clock activity.
            </p>

        </div>

    </div>


    <div class="attendance-table-wrapper">

        <table class="attendance-table">

            <thead>

                <tr>
                    <th>Time</th>
                    <th>Action</th>
                    <th>Coordinates</th>
                    <th>Accuracy</th>
                    <th>Geofence</th>
                    <th>Device</th>
                </tr>

            </thead>


            <tbody>

                @foreach($records as $record)

                    <tr>

                        <td>

                            {{
                                $record
                                    ->server_timestamp
                                    ->copy()
                                    ->timezone(
                                        $timezone
                                    )
                                    ->format(
                                        'H:i'
                                    )
                            }}

                        </td>


                        <td>

                            <span
                                class="
                                    attendance-badge
                                    attendance-badge-{{
                                        $record
                                            ->clock_type
                                    }}
                                "
                            >

                                {{
                                    $record
                                        ->clock_type
                                        === 'in'
                                        ? 'Clock In'
                                        : 'Clock Out'
                                }}

                            </span>

                        </td>


                        <td>

                            @if(
                                $record->latitude
                                &&
                                $record->longitude
                            )

                                {{ $record->latitude }},
                                {{ $record->longitude }}

                            @else

                                —

                            @endif

                        </td>


                        <td>

                            @if(
                                $record->accuracy_meters
                                !== null
                            )

                                ±{{
                                    number_format(
                                        $record
                                            ->accuracy_meters,
                                        2
                                    )
                                }}m

                            @else

                                —

                            @endif

                        </td>


                        <td>

                            @if(
                                $record->within_geofence
                                === true
                            )

                                <span
                                    class="
                                        attendance-status
                                        attendance-status-complete
                                    "
                                >
                                    Inside
                                </span>

                            @elseif(
                                $record->within_geofence
                                === false
                            )

                                <span
                                    class="
                                        attendance-status
                                        attendance-status-issue
                                    "
                                >
                                    Outside
                                </span>

                            @else

                                —

                            @endif

                        </td>


                        <td>

                            {{
                                $record
                                    ->device_platform
                                    ?: '—'
                            }}

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>


@endsection