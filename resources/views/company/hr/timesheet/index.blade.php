@extends('company.layouts.app')

@section('title', 'Timesheet')

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
    .timesheet-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 24px;
    }

    .timesheet-header h1 { margin: 0 0 7px; font-size: 28px; }
    .timesheet-header p { margin: 0; color: #aab4ca; }
    .btn-outline-sm { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 7px; font-size: 13px; text-decoration: none; background: transparent; color: #c9d2e5; border: 1px solid rgba(255,255,255,0.16); }

    .timesheet-filter-bar {
        display: flex;
        align-items: flex-end;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 20px;
        padding: 18px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .timesheet-filter-field { display: flex; flex-direction: column; gap: 6px; }
    .timesheet-filter-field label { color: #c9d2e5; font-size: 12px; font-weight: 600; }
    .timesheet-filter-field input,
    .timesheet-filter-field select {
        padding: 9px 11px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
    }
    .timesheet-filter-submit {
        min-height: 40px;
        padding: 0 16px;
        border: none;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    .timesheet-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 16px;
    }
    .timesheet-stats-secondary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .timesheet-stat-card { padding: 18px; border-radius: 10px; background: #1e2a44; }
    .timesheet-stat-card-muted { background: #182136; }
    .timesheet-stat-label { color: #aab4ca; font-size: 12px; margin-bottom: 8px; }
    .timesheet-stat-value { font-size: 24px; font-weight: 700; }
    .timesheet-stat-value-warning { color: #ffc46f; }
    .timesheet-job-number { display: block; color: #aab4ca; font-size: 11px; margin-top: 2px; }

    .timesheet-section { margin-bottom: 28px; }
    .timesheet-section h2 { font-size: 16px; margin-bottom: 14px; }

    .timesheet-card { padding: 20px; border-radius: 10px; background: #1e2a44; }
    .timesheet-table-wrapper { overflow-x: auto; }
    .timesheet-table { width: 100%; min-width: 900px; border-collapse: collapse; }
    .timesheet-table th, .timesheet-table td {
        padding: 12px 10px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
        font-size: 13px;
    }
    .timesheet-table th { color: #aab4ca; font-size: 11px; text-transform: uppercase; }

    .timesheet-badge {
        display: inline-flex;
        padding: 4px 9px;
        border-radius: 13px;
        font-size: 11px;
    }
    .timesheet-badge-warning { background: rgba(255,179,71,0.16); color: #ffc46f; }
    .timesheet-badge-danger { background: rgba(255,99,99,0.16); color: #ff8a92; }
    .timesheet-badge-muted { background: rgba(170,180,202,0.14); color: #c4ccdc; }
    .timesheet-empty { padding: 25px; text-align: center; color: #aab4ca; }
    .timesheet-over { color: #ff8a92; font-weight: 700; }
    .timesheet-under { color: #59dc96; font-weight: 700; }
</style>

<div class="timesheet-header">
    <div>
        <h1>Timesheet</h1>
        <p>Attendance and job hours by employee, with exceptions flagged for review.</p>
    </div>
    <a href="{{ route('company.hr.timesheet.export', request()->query()) }}" class="btn-outline-sm">
        <i class="fas fa-file-excel"></i> Export to Excel
    </a>
</div>

<form method="GET" action="{{ route('company.hr.timesheet.index') }}" class="timesheet-filter-bar">
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
        <div class="timesheet-stat-label">Office Hours (Total)</div>
        <div class="timesheet-stat-value">{{ formatMinutes($officeTotalMinutes) }}</div>
    </div>
    <div class="timesheet-stat-card">
        <div class="timesheet-stat-label">Field Hours (Total)</div>
        <div class="timesheet-stat-value">{{ formatMinutes($fieldTotalMinutes) }}</div>
    </div>
    <div class="timesheet-stat-card">
        <div class="timesheet-stat-label">Missing Clock-Outs</div>
        <div class="timesheet-stat-value {{ $missingClockOuts > 0 ? 'timesheet-stat-value-warning' : '' }}">{{ $missingClockOuts }}</div>
    </div>
    <div class="timesheet-stat-card">
        <div class="timesheet-stat-label">Manual Adjustments</div>
        <div class="timesheet-stat-value {{ $manualAdjustments->count() > 0 ? 'timesheet-stat-value-warning' : '' }}">{{ $manualAdjustments->count() }}</div>
    </div>
</div>

<div class="timesheet-stats-secondary">
    <div class="timesheet-stat-card">
        <div class="timesheet-stat-label">Late Starts</div>
        <div class="timesheet-stat-value {{ $lateStarts > 0 ? 'timesheet-stat-value-warning' : '' }}">{{ $lateStarts }}</div>
    </div>
    <div class="timesheet-stat-card">
        <div class="timesheet-stat-label">Overtime (Total)</div>
        <div class="timesheet-stat-value {{ $overtimeMinutesTotal > 0 ? 'timesheet-stat-value-warning' : '' }}">{{ formatMinutes($overtimeMinutesTotal) }}</div>
    </div>
    <div class="timesheet-stat-card timesheet-stat-card-muted">
        <div class="timesheet-stat-label">Hours Today / This Week / This Month</div>
        <div class="timesheet-stat-value">{{ formatMinutes($dailyMinutes) }} <span style="color:#aab4ca; font-size:14px; font-weight:400;">/ {{ formatMinutes($weeklyMinutes) }} / {{ formatMinutes($monthlyMinutes) }}</span></div>
    </div>
</div>

<div class="timesheet-section">
    <h2>Office Staff Attendance</h2>
    <div class="timesheet-card">
        <div class="timesheet-table-wrapper">
            <table class="timesheet-table">
                <thead>
                    <tr>
                        <th>Employee</th><th>Date</th><th>Clock In</th><th>Clock Out</th>
                        <th>Break</th><th>Total Hours</th><th>Location</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($officeRows as $row)
                        <tr>
                            <td>{{ optional($row['employee'])->full_name ?? 'Unknown' }}</td>
                            <td>{{ $row['shift_date']->format('d M Y') }}</td>
                            <td>{{ $row['clock_in'] ? $row['clock_in']->format('H:i') : '—' }}</td>
                            <td>{{ $row['clock_out'] ? $row['clock_out']->format('H:i') : '—' }}</td>
                            <td>{{ $row['break_minutes'] }}m</td>
                            <td>{{ formatMinutes($row['worked_minutes']) }}</td>
                            <td>{{ $row['location'] ?? '—' }}</td>
                            <td>
                                @if ($row['missing_clock_out'])
                                    <span class="timesheet-badge timesheet-badge-danger">Missing clock-out</span>
                                @elseif ($row['is_manual'])
                                    <span class="timesheet-badge timesheet-badge-warning">Manual entry</span>
                                @else
                                    <span class="timesheet-badge timesheet-badge-muted">{{ ucfirst($row['status']) }}</span>
                                @endif
                                @if ($row['is_late'])
                                    <span class="timesheet-badge timesheet-badge-warning">Late start</span>
                                @endif
                                @if ($row['overtime_minutes'] > 0)
                                    <span class="timesheet-badge timesheet-badge-warning">+{{ formatMinutes($row['overtime_minutes']) }} OT</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="timesheet-empty">No office shifts in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="timesheet-section">
    <h2>Field Engineer Jobs</h2>
    <div class="timesheet-card">
        <div class="timesheet-table-wrapper">
            <table class="timesheet-table">
                <thead>
                    <tr>
                        <th>Engineer</th><th>Job Number</th><th>Job</th><th>Customer</th><th>Started</th><th>Finished</th>
                        <th>Estimated</th><th>Actual</th><th>Difference</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($fieldRows as $row)
                        <tr>
                            <td>{{ optional($row['employee'])->full_name ?? 'Unknown' }}</td>
                            <td>{{ $row['job_number'] }}</td>
                            <td><a href="{{ route('company.hr.jobs.show', $row['job']) }}" style="color:#6fa8ee;">{{ $row['job']->title }}</a></td>
                            <td>{{ $row['job']->customer_name ?? '—' }}</td>
                            <td>{{ $row['job']->started_at?->format('d M H:i') }}</td>
                            <td>{{ $row['job']->finished_at ? $row['job']->finished_at->format('d M H:i') : '—' }}</td>
                            <td>{{ $row['estimated_minutes'] ? formatMinutes($row['estimated_minutes']) : '—' }}</td>
                            <td>{{ formatMinutes($row['actual_minutes']) }}</td>
                            <td>
                                @if ($row['difference_minutes'] !== null)
                                    <span class="{{ $row['difference_minutes'] > 0 ? 'timesheet-over' : ($row['difference_minutes'] < 0 ? 'timesheet-under' : '') }}">
                                        {{ $row['difference_minutes'] > 0 ? '+' : '' }}{{ formatMinutes($row['difference_minutes']) }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if ($row['missing_finish'])
                                    <span class="timesheet-badge timesheet-badge-danger">Not finished</span>
                                @else
                                    <span class="timesheet-badge timesheet-badge-muted">{{ ucwords(str_replace('_', ' ', $row['job']->status)) }}</span>
                                @endif
                                @if ($row['is_late'])
                                    <span class="timesheet-badge timesheet-badge-warning">Late start</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="timesheet-empty">No field engineer jobs started in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="timesheet-section">
    <h2>Manual Adjustments (Audit)</h2>
    <div class="timesheet-card">
        <div class="timesheet-table-wrapper">
            <table class="timesheet-table">
                <thead>
                    <tr><th>Employee</th><th>Action</th><th>Time</th><th>Reason</th><th>Adjusted By</th></tr>
                </thead>
                <tbody>
                    @forelse ($manualAdjustments as $entry)
                        <tr>
                            <td>{{ optional($entry->employee)->full_name ?? 'Unknown' }}</td>
                            <td>{{ $entry->clock_type === 'in' ? 'Clock In' : 'Clock Out' }}</td>
                            <td>{{ $entry->server_timestamp->format('d M Y H:i') }}</td>
                            <td>{{ $entry->override_reason ?? '—' }}</td>
                            <td>{{ optional($entry->recordedBy)->full_name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="timesheet-empty">No manual adjustments in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
