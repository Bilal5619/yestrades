@extends('company.layouts.app')

@section('title', 'Attendance')

@section('content')

@include(
    'company.hr.attendance.partials.styles'
)


<div class="attendance-header">

    <div>

        <h1>Attendance</h1>

        <p>
            Daily attendance summary.
            Click any employee row to
            view clock activity,
            jobs, shifts and location evidence.
        </p>

    </div>


    <a
        href="{{
            route(
                'company.hr.attendance.create'
            )
        }}"
        class="attendance-add"
    >
        <i class="fas fa-plus"></i>

        Add Manual Record
    </a>

</div>


{{-- QUICK DATE FILTERS --}}
<div class="attendance-quick-filters">

    <a
        href="{{
            route(
                'company.hr.attendance.index',
                [
                    'period' => 'today'
                ]
            )
        }}"
        class="
            attendance-period-button
            {{
                request('period') === 'today'
                    ? 'active'
                    : ''
            }}
        "
    >
        Today
    </a>


    <a
        href="{{
            route(
                'company.hr.attendance.index',
                [
                    'period' => 'week'
                ]
            )
        }}"
        class="
            attendance-period-button
            {{
                (
                    request('period', 'week')
                    === 'week'
                    &&
                    !request('date_from')
                )
                    ? 'active'
                    : ''
            }}
        "
    >
        This Week
    </a>


    <a
        href="{{
            route(
                'company.hr.attendance.index',
                [
                    'period' => 'month'
                ]
            )
        }}"
        class="
            attendance-period-button
            {{
                request('period') === 'month'
                    ? 'active'
                    : ''
            }}
        "
    >
        This Month
    </a>

</div>


{{-- CUSTOM FILTER --}}
<form
    method="GET"
    action="{{
        route(
            'company.hr.attendance.index'
        )
    }}"
    class="attendance-filter-bar"
>

    <div class="attendance-filter-field">

        <label>From</label>

        <input
            type="date"
            name="date_from"
            value="{{
                $dateFrom->toDateString()
            }}"
        >

    </div>


    <div class="attendance-filter-field">

        <label>To</label>

        <input
            type="date"
            name="date_to"
            value="{{
                $dateTo->toDateString()
            }}"
        >

    </div>


    <div class="attendance-filter-field">

        <label>Employee</label>

        <select name="employee_id">

            <option value="">
                All employees
            </option>

            @foreach(
                $employees as $employee
            )

                <option
                    value="{{ $employee->id }}"
                    @selected(
                        request('employee_id')
                        == $employee->id
                    )
                >
                    {{
                        $employee->full_name
                    }}
                </option>

            @endforeach

        </select>

    </div>


    <button
        type="submit"
        class="attendance-filter-submit"
    >
        Filter
    </button>

</form>


{{-- SUMMARY CARDS --}}
<div class="attendance-summary-grid">

    <div class="attendance-summary-card">

        <span>
            Attendance Days
        </span>

        <strong>
            {{
                $summaryStats[
                    'employee_days'
                ]
            }}
        </strong>

    </div>


    <div class="attendance-summary-card">

        <span>
            Complete
        </span>

        <strong class="attendance-green">
            {{
                $summaryStats[
                    'complete'
                ]
            }}
        </strong>

    </div>


    <div class="attendance-summary-card">

        <span>
            Issues
        </span>

        <strong class="
            {{
                $summaryStats['issues'] > 0
                    ? 'attendance-red'
                    : 'attendance-green'
            }}
        ">
            {{
                $summaryStats[
                    'issues'
                ]
            }}
        </strong>

    </div>


    <div class="attendance-summary-card">

        <span>
            Total Recorded Time
        </span>

        <strong>
            @php
                $totalMinutes =
                    $summaryStats[
                        'total_minutes'
                    ];

                $hours =
                    intdiv(
                        $totalMinutes,
                        60
                    );

                $minutes =
                    $totalMinutes % 60;
            @endphp

            {{ $hours }}h
            {{ $minutes }}m
        </strong>

    </div>

</div>


{{-- DAILY SUMMARY --}}
<div class="attendance-card">

    <div class="attendance-section-heading">

        <div>

            <h3>
                Daily Summary
            </h3>

            <p>
                One row per employee
                per day.
            </p>

        </div>

    </div>


    <div class="attendance-table-wrapper">

        <table class="
            attendance-table
            attendance-summary-table
        ">

            <thead>

                <tr>

                    <th>Date</th>

                    <th>Employee</th>

                    <th>First In</th>

                    <th>Last Out</th>

                    <th>Worked</th>

                    <th>Source</th>

                    <th>Status</th>

                    <th></th>

                </tr>

            </thead>


            <tbody>

                @forelse(
                    $summaries
                    as $summary
                )

                    <tr
                        class="attendance-clickable-row"
                        onclick="
                            window.location={{
                                \Illuminate\Support\Js::from(
                                    route(
                                        'company.hr.attendance.show-day',
                                        [
                                            $summary[
                                                'employee_id'
                                            ],
                                            $summary[
                                                'date'
                                            ]
                                        ]
                                    )
                                )
                            }};
                        "
                    >

                        <td>

                            {{
                                \Illuminate\Support\Carbon::parse(
                                    $summary['date']
                                )
                                ->format(
                                    'd M Y'
                                )
                            }}

                        </td>


                        <td>

                            <strong>
                                {{
                                    optional(
                                        $summary[
                                            'employee'
                                        ]
                                    )
                                    ->full_name
                                    ?? 'Unknown'
                                }}
                            </strong>

                        </td>


                        <td>

                            @if(
                                $summary[
                                    'first_in'
                                ]
                            )

                                {{
                                    $summary[
                                        'first_in'
                                    ]
                                    ->format(
                                        'H:i'
                                    )
                                }}

                            @else

                                —

                            @endif

                        </td>


                        <td>

                            @if(
                                $summary[
                                    'last_out'
                                ]
                            )

                                {{
                                    $summary[
                                        'last_out'
                                    ]
                                    ->format(
                                        'H:i'
                                    )
                                }}

                            @else

                                <span class="
                                    attendance-warning-text
                                ">
                                    —
                                </span>

                            @endif

                        </td>


                        <td>

                            @php
                                $worked =
                                    $summary[
                                        'worked_minutes'
                                    ];

                                $workedHours =
                                    intdiv(
                                        $worked,
                                        60
                                    );

                                $workedMins =
                                    $worked % 60;
                            @endphp


                            @if($workedHours)

                                {{ $workedHours }}h

                            @endif

                            {{ $workedMins }}m

                        </td>


                        <td>

                            <span class="
                                attendance-source-badge
                            ">
                                {{
                                    $summary[
                                        'source'
                                    ]
                                }}
                            </span>

                        </td>


                        <td>

                            @if(
                                $summary[
                                    'status'
                                ]
                                === 'complete'
                            )

                                <span class="
                                    attendance-status
                                    attendance-status-complete
                                ">
                                    Complete
                                </span>

                            @else

                                <span class="
                                    attendance-status
                                    attendance-status-issue
                                ">
                                    Action Required
                                </span>


                                @if(
                                    count(
                                        $summary[
                                            'issues'
                                        ]
                                    )
                                )

                                    <div class="
                                        attendance-issue-text
                                    ">

                                        {{
                                            implode(
                                                ', ',
                                                $summary[
                                                    'issues'
                                                ]
                                            )
                                        }}

                                    </div>

                                @endif

                            @endif

                        </td>


                        <td class="
                            attendance-row-arrow
                        ">

                            <i class="
                                fas
                                fa-chevron-right
                            "></i>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="attendance-empty"
                        >
                            No attendance
                            records for
                            this period.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>


@endsection