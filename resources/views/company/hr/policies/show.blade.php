@extends('company.layouts.app')

@section('title', $policy->title)

@section('content')

<style>
    .policy-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .policy-detail-header h1 {
        margin: 0 0 7px;
    }

    .policy-muted {
        color: #aab4ca;
    }

    .policy-panel {
        background: #1e2a44;
        padding: 22px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .policy-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 10px 15px;
        border: 0;
        border-radius: 7px;
        background: #2878dc;
        color: white;
        cursor: pointer;
        text-decoration: none;
    }

    .policy-secondary {
        background: #3a4967;
    }

    .policy-assign-list {
        display: grid;
        grid-template-columns:
            repeat(
                auto-fill,
                minmax(220px, 1fr)
            );
        gap: 10px;
        margin-top: 15px;
        max-height: 300px;
        overflow-y: auto;
    }

    .policy-employee-check {
        display: flex;
        gap: 9px;
        align-items: center;
        background: #162138;
        padding: 10px;
        border-radius: 6px;
    }

    .policy-table {
        width: 100%;
        border-collapse: collapse;
    }

    .policy-table th,
    .policy-table td {
        padding: 13px 11px;
        text-align: left;
        border-bottom:
            1px solid rgba(255,255,255,.07);
    }

    .policy-table th {
        color: #aab4ca;
        font-size: 12px;
        text-transform: uppercase;
    }

    .policy-pending {
        color: #ffc66d;
    }

    .policy-acknowledged {
        color: #6fe6a4;
        font-weight: 600;
    }

    @media (max-width: 700px) {
        .policy-detail-header {
            flex-direction: column;
        }
    }
</style>

<div class="policy-detail-header">

    <div>

        <h1>
            {{ $policy->title }}
        </h1>

        <div class="policy-muted">
            Version:
            {{ $policy->version ?: 'Not specified' }}
        </div>

    </div>

    <div style="display:flex;gap:10px;">

        @if ($policy->file_path)

            <a
                href="{{ route(
                    'company.hr.policies.download',
                    $policy
                ) }}"
                class="policy-button"
            >
                <i class="fas fa-download"></i>
                Download Policy
            </a>

        @endif

        <a
            href="{{ route(
                'company.hr.policies.index'
            ) }}"
            class="
                policy-button
                policy-secondary
            "
        >
            Back
        </a>

    </div>

</div>

@if (session('success'))

    <div
        style="
            padding:12px 15px;
            margin-bottom:20px;
            border-radius:7px;
            background:rgba(46,191,114,.15);
            color:#6fe6a4;
        "
    >
        {{ session('success') }}
    </div>

@endif

<div class="policy-panel">

    <h3 style="margin-top:0;">
        Policy Details
    </h3>

    <p class="policy-muted">
        {{
            $policy->description
            ?: 'No description provided.'
        }}
    </p>

</div>

@if (
    auth('company')
        ->user()
        ->hasPermission('policies.manage')
)

    <div class="policy-panel">

        <h3 style="margin-top:0;">
            Assign Policy to Employees
        </h3>

        <p class="policy-muted">
            Select the employees who are required
            to read and acknowledge this policy.
        </p>

        <form
            method="POST"
            action="{{ route(
                'company.hr.policies.assign',
                $policy
            ) }}"
        >
            @csrf

            <div class="policy-assign-list">

                @foreach ($employees as $employee)

                    @php
                        $alreadyAssigned =
                            $policy
                                ->acknowledgements
                                ->contains(
                                    'employee_id',
                                    $employee->id
                                );
                    @endphp

                    <label class="policy-employee-check">

                        <input
                            type="checkbox"
                            name="employee_ids[]"
                            value="{{ $employee->id }}"
                            {{ $alreadyAssigned
                                ? 'disabled'
                                : '' }}
                        >

                        <span>

                            {{ $employee->full_name }}

                            @if ($alreadyAssigned)
                                <small class="policy-muted">
                                    (Already assigned)
                                </small>
                            @endif

                        </span>

                    </label>

                @endforeach

            </div>

            <button
                type="submit"
                class="policy-button"
                style="margin-top:16px;"
            >
                <i class="fas fa-user-plus"></i>
                Assign Policy
            </button>

        </form>

    </div>

@endif

<div class="policy-panel">

    <h3 style="margin-top:0;">
        Employee Acknowledgements
    </h3>

    <div style="overflow-x:auto;">

        <table class="policy-table">

            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Status</th>
                    <th>Acknowledged Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse (
                    $policy->acknowledgements
                    as $acknowledgement
                )

                    <tr>

                        <td>
                            {{
                                $acknowledgement
                                    ->employee
                                    ->full_name
                            }}
                        </td>

                        <td>

                            @if (
                                $acknowledgement->status
                                === 'acknowledged'
                            )

                                <span
                                    class="
                                        policy-acknowledged
                                    "
                                >
                                    <i
                                        class="
                                            fas
                                            fa-check-circle
                                        "
                                    ></i>

                                    Acknowledged
                                </span>

                            @else

                                <span
                                    class="policy-pending"
                                >
                                    Pending
                                </span>

                            @endif

                        </td>

                        <td>

                            @if (
                                $acknowledgement
                                    ->acknowledged_at
                            )

                                {{
                                    $acknowledgement
                                        ->acknowledged_at
                                        ->format(
                                            'd M Y H:i'
                                        )
                                }}

                            @else
                                —
                            @endif

                        </td>

                        <td>

                         @if (
                $acknowledgement->status
                    === 'acknowledged'
            )

                <span class="policy-acknowledged">
                    <i class="fas fa-check-circle"></i>
                    Acknowledged
                </span>

            @else

                <span class="policy-pending">
                    Waiting for Employee
                </span>

            @endif
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td
                            colspan="4"
                            style="
                                text-align:center;
                                color:#aab4ca;
                                padding:25px;
                            "
                        >
                            This policy has not
                            been assigned to any
                            employees yet.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection