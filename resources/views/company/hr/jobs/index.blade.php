@extends('company.layouts.app')

@section('title', 'Job Sheet')

@section('content')

@include('company.hr.jobs.partials.styles')

<div class="jobsheet-header">
    <div>
        <h1>Job Sheet</h1>
        <p>Weekly rota of jobs assigned to employees. Only assigned employees see their jobs in the app.</p>
    </div>

    @if (auth('company')->user()->hasPermission('jobs.manage'))
        <a href="{{ route('company.hr.jobs.create') }}" class="jobsheet-add">
            <i class="fas fa-plus"></i>
            New Job
        </a>
    @endif
</div>

<div class="jobsheet-week-nav">
    <a href="{{ route('company.hr.jobs.index', ['week' => $weekStart->copy()->subWeek()->toDateString()]) }}">
        <i class="fas fa-chevron-left"></i> Previous week
    </a>
    <span class="jobsheet-week-label">{{ $weekStart->format('d M') }} – {{ $weekEnd->format('d M Y') }}</span>
    <a href="{{ route('company.hr.jobs.index', ['week' => $weekStart->copy()->addWeek()->toDateString()]) }}">
        Next week <i class="fas fa-chevron-right"></i>
    </a>
    <a href="{{ route('company.hr.jobs.index') }}">This week</a>
</div>

@foreach (range(0, 6) as $offset)
    @php($day = $weekStart->copy()->addDays($offset))
    @php($dayJobs = $jobs->get($day->toDateString(), collect()))

    <div class="jobsheet-day-card">
        <div class="jobsheet-day-title">{{ $day->format('l, d M Y') }}</div>

        @forelse ($dayJobs as $job)
            <div class="jobsheet-job-row">
                <div class="jobsheet-job-main">
                    <a href="{{ route('company.hr.jobs.show', $job) }}">{{ $job->title }}</a>
                    <div class="jobsheet-job-meta">
                        {{ $job->scheduled_start_time ?? 'No time set' }}
                        &middot; {{ optional($job->employee)->full_name ?? 'Unassigned' }}
                        @if ($job->customer_name) &middot; {{ $job->customer_name }} @endif
                        @if ($job->actualDurationMinutes() !== null)
                            &middot; {{ $job->actualDurationMinutes() }}m actual
                            @php($diff = $job->durationDifferenceMinutes())
                            @if ($diff !== null && $diff !== 0)
                                <span class="{{ $diff > 0 ? 'jobsheet-duration-over' : 'jobsheet-duration-under' }}">({{ $diff > 0 ? '+' : '' }}{{ $diff }}m)</span>
                            @endif
                        @endif
                    </div>
                </div>
                <div style="display:flex; gap:8px; align-items:center;">
                    <span class="jobsheet-badge jobsheet-badge-{{ $job->priority }}">{{ ucfirst($job->priority) }}</span>
                    <span class="jobsheet-badge jobsheet-status-{{ $job->status }}">{{ ucwords(str_replace('_', ' ', $job->status)) }}</span>
                </div>
            </div>
        @empty
            <div class="jobsheet-job-meta">No jobs scheduled.</div>
        @endforelse
    </div>
@endforeach

@endsection
