@extends('company.layouts.app')

@section('title', 'Record Absence')

@section('content')

<h1>Record Employee Absence</h1>

<form
    method="POST"
    action="{{ route(
        'company.hr.absences.store'
    ) }}"
    enctype="multipart/form-data"
>
    @csrf

    <div>
        <label>Employee</label>

        <select
            name="employee_id"
            required
        >
            <option value="">
                Select employee
            </option>

            @foreach ($employees as $employee)
                <option
                    value="{{ $employee->id }}"
                >
                    {{ $employee->full_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Absence Type</label>

        <select
            name="absence_type"
            required
        >
            <option value="sickness">
                Sickness
            </option>

            <option value="unpaid_leave">
                Unpaid Leave
            </option>

            <option value="compassionate_leave">
                Compassionate Leave
            </option>

            <option value="authorised_absence">
                Authorised Absence
            </option>

            <option value="unauthorised_absence">
                Unauthorised Absence
            </option>

            <option value="other">
                Other
            </option>
        </select>
    </div>

    <div>
        <label>Start Date</label>

        <input
            type="date"
            name="start_date"
            required
        >
    </div>

    <div>
        <label>End Date</label>

        <input
            type="date"
            name="end_date"
        >
    </div>

    <div>
        <label>Days Absent</label>

        <input
            type="number"
            step="0.5"
            min="0"
            name="days_absent"
        >
    </div>

    <div>
        <label>Reason / Notes</label>

        <textarea
            name="reason"
            rows="5"
        ></textarea>
    </div>

    <div>
        <label>
            Evidence / Fit Note
        </label>

        <input
            type="file"
            name="evidence"
            accept=".pdf,.jpg,.jpeg,.png"
        >
    </div>

    <button type="submit">
        Save Absence
    </button>

</form>

@endsection