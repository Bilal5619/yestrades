@extends('company.layouts.app')

@section('title', 'Overtime Report')

@section('content')

@php
  if (!function_exists('formatMinutes')) {
    function formatMinutes($minutes) {
      if ($minutes === null) return '—';
      $sign = $minutes < 0 ? '-' : '';
      $minutes = abs($minutes);
      return $sign . intdiv($minutes, 60) . 'h ' . ($minutes % 60) . 'm';
    }
  }
@endphp

<style>
    .timesheet-header { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px; margin-bottom: 24px; }
    .timesheet-header h1 { margin: 0 0 7px; font-size: 28px; }
    .timesheet-header p { margin: 0; color: #aab4ca; }
    .timesheet-filter-bar { display: flex; align-items: flex-end; gap: 14px; flex-wrap: wrap; margin-bottom: 20px; padding: 18px; border-radius: 10px; background: #1e2a44; }
    .timesheet-filter-field { display: flex; flex-direction: column; gap: 6px; }
    .timesheet-filter-field label { color: #c9d2e5; font-size: 12px; font-weight: 600; }
    .timesheet-filter-field input, .timesheet-filter-field select { padding: 9px 11px; border: 1px solid rgba(255,255,255,0.12); border-radius: 7px; background: #151f35; color: #ffffff; }
    .timesheet-filter-submit { min-height: 40px; padding: 0 16px; border: none; border-radius: 7px; background: #2878dc; color: #ffffff; cursor: pointer; }
    .timesheet-stats { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; margin-bottom: 24px; }
    .timesheet-stat-card { padding: 18px; border-radius: 10px; background: #1e2a44; }
    .timesheet-stat-label { color: #aab4ca; font-size: 12px; margin-bottom: 8px; }
    .timesheet-stat-value { font-size: 24px; font-weight: 700; color: #ffc46f; }
    .timesheet-section { margin-bottom: 28px; }
    .timesheet-section h2 { font-size: 16px; margin-bottom: 14px; }
    .timesheet-card { padding: 20px; border-radius: 10px; background: #1e2a44; }
    .timesheet-table-wrapper { overflow-x: auto; }
    .timesheet-table { width: 100%; min-width: 900px; border-collapse: collapse; }
    .timesheet-table th, .timesheet-table td { padding: 12px 10px; border-bottom: 1px solid rgba(255,255,255,0.07); text-align: left; font-size: 13px; }
    .timesheet-table th { color: #aab4ca; font-size: 11px; text-transform: uppercase; }
    .timesheet-empty { padding: 25px; text-align: center; color: #aab4ca; }
    .timesheet-over { color: #ffc46f; font-weight: 700; }
    .btn-outline-sm { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 7px; font-size: 13px; text-decoration: none; background: transparent; color: #c9d2e5; border: 1px solid rgba(255,255,255,0.16); }
</style>

<div class="timesheet-header">
    <div>
        <h1>Overtime Report</h1>
        <p>Shifts and jobs that ran over their scheduled/estimated time.</p>
    </div>
    <a href="{{ route('company.hr.reports.overtime.export', request()->query()) }}" class="btn-outline-sm">
        <i class="fas fa-file-excel"></i> Export to Excel
    </a>
</div>

<form method="GET" action="{{ route('company.hr.reports.overtime') }}" class="timesheet-filter-bar">
    <div class="timesheet-filter-field">
        <label>From</label>
        <input type="date" name="date_from" value="{{ $dateFrom->toDateString() }}">
    </div>
    <div class="timesheet-filter-field">
        <label>To</label>
        <input type="date" name="date_to" value="{{ $dateTo->toDateString() }}">
    </div>
    <div class="timesheet-filter-field">
        <label>Employee</label>
        <select name="employee_id">
            <option value="">All employees</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}" @selected(request('employee_id') == $employee->id)>{{ $employee->full_name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="timesheet-filter-submit">Filter</button>
</form>

<div class="timesheet-stats">
    <div class="timesheet-stat-card">
        <div class="timesheet-stat-label">Office Overtime (Total)</div>
        <div class="timesheet-stat-value">{{ formatMinutes($officeOvertimeMinutes) }}</div>
    </div>
    <div class="timesheet-stat-card">
        <div class="timesheet-stat-label">Field Overtime (Total)</div>
        <div class="timesheet-stat-value">{{ formatMinutes($fieldOvertimeMinutes) }}</div>
    </div>
</div>

<div class="timesheet-section">
    <h2>Office Staff Overtime</h2>
    <div class="timesheet-card">
        <div class="timesheet-table-wrapper">
            <table class="timesheet-table">
                <thead>
                    <tr><th>Employee</th><th>Date</th><th>Clock In</th><th>Clock Out</th><th>Scheduled</th><th>Actual</th><th>Overtime</th></tr>
                </thead>
                <tbody>
                    @forelse ($officeRows as $row)
                        <tr>
                            <td>{{ optional($row['employee'])->full_name ?? 'Unknown' }}</td>
                            <td>{{ $row['shift_date']->format('d M Y') }}</td>
                            <td>{{ $row['clock_in'] ? $row['clock_in']->format('H:i') : '—' }}</td>
                            <td>{{ $row['clock_out'] ? $row['clock_out']->format('H:i') : '—' }}</td>
                            <td>{{ formatMinutes($row['worked_minutes'] - $row['overtime_minutes']) }}</td>
                            <td>{{ formatMinutes($row['worked_minutes']) }}</td>
                            <td class="timesheet-over">+{{ formatMinutes($row['overtime_minutes']) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="timesheet-empty">No office overtime in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="timesheet-section">
    <h2>Field Engineer Overtime</h2>
    <div class="timesheet-card">
        <div class="timesheet-table-wrapper">
            <table class="timesheet-table">
                <thead>
                    <tr><th>Engineer</th><th>Job Number</th><th>Job</th><th>Estimated</th><th>Actual</th><th>Overtime</th></tr>
                </thead>
                <tbody>
                    @forelse ($fieldRows as $row)
                        <tr>
                            <td>{{ optional($row['employee'])->full_name ?? 'Unknown' }}</td>
                            <td>{{ $row['job_number'] }}</td>
                            <td><a href="{{ route('company.hr.jobs.show', $row['job']) }}" style="color:#6fa8ee;">{{ $row['job']->title }}</a></td>
                            <td>{{ formatMinutes($row['estimated_minutes']) }}</td>
                            <td>{{ formatMinutes($row['actual_minutes']) }}</td>
                            <td class="timesheet-over">+{{ formatMinutes($row['difference_minutes']) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="timesheet-empty">No field overtime in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
