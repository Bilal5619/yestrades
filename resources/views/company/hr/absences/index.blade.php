@extends('company.layouts.app')

@section('title', 'Employee Absences')

@section('content')

<div style="
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:24px;
">
    <div>
        <h1 style="margin:0 0 6px;">
            Employee Absence / Sickness
        </h1>

        <p style="margin:0;color:#aab4ca;">
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
            style="
                padding:11px 16px;
                background:#2878dc;
                border-radius:6px;
                color:white;
                text-decoration:none;
            "
        >
            Record Absence
        </a>
    @endif
</div>

<div style="
    background:#1e2a44;
    padding:20px;
    border-radius:9px;
    overflow-x:auto;
">

    <table
        style="
            width:100%;
            border-collapse:collapse;
        "
    >
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
                        {{ $absence->employee->full_name }}
                    </td>

                    <td>
                        {{
                            ucwords(
                                str_replace(
                                    '_',
                                    ' ',
                                    $absence->absence_type
                                )
                            )
                        }}
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
                        {{ ucfirst($absence->status) }}
                    </td>

                    <td>
                        @if (
                            $absence->return_to_work_completed
                        )
                            Completed
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
                                    placeholder="Notes"
                                >

                                <button type="submit">
                                    Complete
                                </button>
                            </form>
                        @else
                            Pending
                        @endif
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6">
                        No absence records found.
                    </td>
                </tr>

            @endforelse

        </tbody>
    </table>

</div>

<div style="margin-top:20px;">
    {{ $absences->links() }}
</div>

@endsection