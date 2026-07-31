@extends('company.layouts.app')

@section('title', 'Attendance')

@section('content')

@include('company.hr.attendance.partials.styles')

<div class="attendance-header">
    <div>
        <h1>Attendance</h1>
        <p>Clock In / Clock Out records captured from the employee app, including location evidence.</p>
    </div>

    <a href="{{ route('company.hr.attendance.create') }}" class="attendance-add">
        <i class="fas fa-plus"></i>
        Add Manual Record
    </a>
</div>

<form method="GET" action="{{ route('company.hr.attendance.index') }}" class="attendance-filter-bar">
    <div class="attendance-filter-field">
        <label>From</label>
        <input type="date" name="date_from" value="{{ $dateFrom->toDateString() }}">
    </div>
    <div class="attendance-filter-field">
        <label>To</label>
        <input type="date" name="date_to" value="{{ $dateTo->toDateString() }}">
    </div>
    <div class="attendance-filter-field">
        <label>Employee</label>
        <select name="employee_id">
            <option value="">All employees</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}" @selected(request('employee_id') == $employee->id)>{{ $employee->full_name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="attendance-filter-submit">Filter</button>
</form>

<div class="attendance-card">
    <div class="attendance-table-wrapper">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Action</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Geofence</th>
                    <th>Source</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $record)
                    <tr>
                        <td>{{ optional($record->employee)->full_name ?? 'Unknown' }}</td>
                        <td>
                            <span class="attendance-badge attendance-badge-{{ $record->clock_type }}">
                                {{ $record->clock_type === 'in' ? 'Clock In' : 'Clock Out' }}
                            </span>
                        </td>
                        <td>{{ $record->server_timestamp->format('d M Y H:i') }}</td>
                        <td>
                            @if ($record->latitude && $record->longitude)
                                {{ $record->latitude }}, {{ $record->longitude }}
                                @if ($record->accuracy_meters) (±{{ $record->accuracy_meters }}m) @endif
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if (!is_null($record->within_geofence))
                                {{ $record->within_geofence ? 'Inside' : 'Outside' }}
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if ($record->is_manual_entry)
                                <span class="attendance-badge attendance-badge-manual">Manual · {{ optional($record->recordedBy)->full_name ?? 'Admin' }}</span>
                                @if ($record->override_reason)
                                    <div style="color:#aab4ca; font-size:12px; margin-top:4px;">{{ $record->override_reason }}</div>
                                @endif
                            @else
                                App
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="attendance-empty">No attendance records for this period.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $records->links() }}
</div>

@endsection
