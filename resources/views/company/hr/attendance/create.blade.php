@extends('company.layouts.app')

@section('title', 'Add Manual Attendance Record')

@section('content')

@include('company.hr.attendance.partials.styles')

<div class="attendance-header">
    <div>
        <h1>Add Manual Attendance Record</h1>
        <p>Use this only to correct a missed Clock In/Out — for example, an employee forgot to clock out.</p>
    </div>

    <a href="{{ route('company.hr.attendance.index') }}" class="attendance-back">
        <i class="fas fa-arrow-left"></i>
        Attendance
    </a>
</div>

<div class="attendance-form-card">
    <p class="attendance-help">
        Manual entries do not include GPS location evidence and are always logged with your name and the reason given, for audit purposes.
    </p>

    <form method="POST" action="{{ route('company.hr.attendance.store') }}">
        @csrf

        <div class="attendance-field">
            <label>Employee</label>
            <select name="employee_id" required>
                <option value="">Select employee</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>{{ $employee->full_name }}</option>
                @endforeach
            </select>
            @error('employee_id')<div class="attendance-error">{{ $message }}</div>@enderror
        </div>

        <div class="attendance-field">
            <label>Action</label>
            <select name="clock_type" required>
                <option value="in" @selected(old('clock_type') === 'in')>Clock In</option>
                <option value="out" @selected(old('clock_type') === 'out')>Clock Out</option>
            </select>
            @error('clock_type')<div class="attendance-error">{{ $message }}</div>@enderror
        </div>

        <div class="attendance-field">
            <label>Date &amp; Time</label>
            <input type="datetime-local" name="server_timestamp" value="{{ old('server_timestamp') }}" required>
            @error('server_timestamp')<div class="attendance-error">{{ $message }}</div>@enderror
        </div>

        <div class="attendance-field">
            <label>Reason (required)</label>
            <textarea name="override_reason" required>{{ old('override_reason') }}</textarea>
            @error('override_reason')<div class="attendance-error">{{ $message }}</div>@enderror
        </div>

        <div class="attendance-form-actions">
            <button type="submit" class="attendance-save">Add Record</button>
        </div>
    </form>
</div>

@endsection
