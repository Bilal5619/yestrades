@extends('company.layouts.app')

@section('title', 'Employee Absences')

@section('content')

<style>
    .absence-page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .absence-page-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
        font-weight: 600;
    }

    .absence-page-header p {
        margin: 0;
        color: #aab4ca;
        font-size: 14px;
    }

    .absence-primary-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 42px;
        padding: 0 17px;
        border: 0;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
    }

    .absence-primary-button:hover {
        color: #ffffff;
        text-decoration: none;
        background: #246dca;
    }

    .absence-table-panel {
        background: #1e2a44;
        border-radius: 10px;
        padding: 20px;
        overflow-x: auto;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .absence-table {
        width: 100%;
        min-width: 950px;
        border-collapse: collapse;
    }

    .absence-table th,
    .absence-table td {
        padding: 14px 12px;
        text-align: left;
        border-bottom: 1px solid rgba(255, 255, 255, .07);
        vertical-align: middle;
    }

    .absence-table th {
        color: #aab4ca;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .absence-table td {
        color: #ffffff;
        font-size: 14px;
    }

    .absence-table tbody tr:last-child td {
        border-bottom: none;
    }

    .absence-employee-name {
        font-weight: 600;
    }

    .absence-type {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 14px;
        background: rgba(40, 120, 220, .16);
        color: #82b7ff;
        font-size: 12px;
    }

    .absence-status {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 14px;
        font-size: 12px;
        font-weight: 600;
    }

    .absence-status-open {
        background: rgba(243, 166, 58, .18);
        color: #ffc66d;
    }

    .absence-status-closed {
        background: rgba(46, 191, 114, .18);
        color: #6fe6a4;
    }

    .return-form {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .return-form input {
        min-height: 36px;
        padding: 7px 9px;
        border-radius: 6px;
        border: 1px solid rgba(255, 255, 255, .12);
        background: #162138;
        color: #ffffff;
        font-size: 13px;
        outline: none;
    }

    .return-form input:focus {
        border-color: #2878dc;
        box-shadow: 0 0 0 2px rgba(40, 120, 220, .15);
    }

    .return-form input[type="date"] {
        width: 145px;
    }

    .return-form input[type="text"] {
        width: 180px;
    }

    .return-form button {
        min-height: 36px;
        padding: 0 13px;
        border: 0;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
    }

    .return-form button:hover {
        background: #246dca;
    }

    .absence-completed {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #6fe6a4;
        font-weight: 600;
        font-size: 13px;
    }

    .absence-pending {
        color: #ffc66d;
        font-size: 13px;
    }

    .absence-empty {
        padding: 30px 15px;
        text-align: center;
        color: #aab4ca;
    }

    .absence-pagination {
        margin-top: 20px;
    }

    @media (max-width: 750px) {
        .absence-page-header {
            flex-direction: column;
        }

        .absence-primary-button {
            width: 100%;
        }

        .return-form {
            min-width: 300px;
        }
    }
</style>

<div class="absence-page-header">

    <div>
        <h1>Employee Absence / Sickness</h1>

        <p>
            Manage sickness and other non-holiday absences.
        </p>
    </div>

    @if (
        auth('company')
            ->user()
            ->hasPermission('absences.manage')
    )
        <a
            href="{{ route(
                'company.hr.absences.create'
            ) }}"
            class="absence-primary-button"
        >
            <i class="fas fa-plus"></i>
            Record Absence
        </a>
    @endif

</div>

<div class="absence-table-panel">

    <table class="absence-table">

        <thead>
            <tr>
                <th>Employee</th>
                <th>Type</th>
                <th>Dates</th>
                <th>Days</th>
                <th>Status</th>
                <th>Return to Work</th>
            </tr>
        </thead>

        <tbody>

            @forelse ($absences as $absence)

                <tr>

                    <td>
                        <div class="absence-employee-name">
                            {{ $absence->employee->full_name }}
                        </div>
                    </td>

                    <td>
                        <span class="absence-type">
                            {{
                                ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $absence->absence_type
                                    )
                                )
                            }}
                        </span>
                    </td>

                    <td>
                        {{ $absence->start_date->format('d M Y') }}

                        @if ($absence->end_date)
                            -
                            {{ $absence->end_date->format('d M Y') }}
                        @endif
                    </td>

                    <td>
                        {{ $absence->days_absent ?? '—' }}
                    </td>

                    <td>
                        <span class="
                            absence-status
                            {{ $absence->status === 'closed'
                                ? 'absence-status-closed'
                                : 'absence-status-open' }}
                        ">
                            {{ ucfirst($absence->status) }}
                        </span>
                    </td>

                    <td>

                        @if (
                            $absence->return_to_work_completed
                        )

                            <span class="absence-completed">
                                <i class="fas fa-check-circle"></i>
                                Completed
                            </span>

                        @elseif (
                            auth('company')
                                ->user()
                                ->hasPermission(
                                    'absences.manage'
                                )
                        )

                            <form
                                method="POST"
                                action="{{ route(
                                    'company.hr.absences.return-to-work',
                                    $absence
                                ) }}"
                                class="return-form"
                            >
                                @csrf
                                @method('PUT')

                                <input
                                    type="date"
                                    name="return_to_work_date"
                                    required
                                >

                                <input
                                    type="text"
                                    name="return_to_work_notes"
                                    placeholder="Return-to-work notes"
                                >

                                <button type="submit">
                                    <i class="fas fa-check"></i>
                                    Complete
                                </button>

                            </form>

                        @else

                            <span class="absence-pending">
                                Pending
                            </span>

                        @endif

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6">
                        <div class="absence-empty">
                            <i
                                class="fas fa-user-clock"
                                style="
                                    display:block;
                                    margin-bottom:10px;
                                    font-size:26px;
                                "
                            ></i>

                            No absence records found.
                        </div>
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

</div>

@if ($absences->hasPages())
    <div class="absence-pagination">
        {{ $absences->links() }}
    </div>
@endif

@endsection