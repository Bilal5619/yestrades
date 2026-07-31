@extends('company.layouts.app')

@section('title', 'Rota and Shifts')

@section('content')

@include('company.hr.shifts.partials.styles')

<div class="shift-header">
    <div>
        <h1>Rota and Shifts</h1>
        <p>Weekly shift schedule. Employees see their own shifts in the app and clock in/out against them.</p>
    </div>

    @if (auth('company')->user()->hasPermission('rota.manage'))
        <a href="{{ route('company.hr.shifts.create') }}" class="shift-add">
            <i class="fas fa-plus"></i>
            New Shift
        </a>
    @endif
</div>

<div class="shift-week-nav">
    <a href="{{ route('company.hr.shifts.index', ['week' => $weekStart->copy()->subWeek()->toDateString()]) }}">
        <i class="fas fa-chevron-left"></i> Previous week
    </a>
    <span class="shift-week-label">{{ $weekStart->format('d M') }} – {{ $weekEnd->format('d M Y') }}</span>
    <a href="{{ route('company.hr.shifts.index', ['week' => $weekStart->copy()->addWeek()->toDateString()]) }}">
        Next week <i class="fas fa-chevron-right"></i>
    </a>
    <a href="{{ route('company.hr.shifts.index') }}">This week</a>
</div>

@foreach (range(0, 6) as $offset)
    @php($day = $weekStart->copy()->addDays($offset))
    @php($dayShifts = $shifts->get($day->toDateString(), collect()))

    <div class="shift-day-card">
        <div class="shift-day-title">{{ $day->format('l, d M Y') }}</div>

        @forelse ($dayShifts as $shift)
            <div class="shift-row">
                <div class="shift-main">
                    <a href="{{ route('company.hr.shifts.edit', $shift) }}">{{ optional($shift->employee)->full_name ?? 'Unknown employee' }}</a>
                    <div class="shift-meta">
                        {{ $shift->start_time }} – {{ $shift->end_time }}
                        @if ($shift->break_minutes) &middot; {{ $shift->break_minutes }} min break @endif
                        @if ($shift->work_location) &middot; {{ $shift->work_location }} @endif
                    </div>
                </div>
                <span class="shift-badge shift-status-{{ $shift->status }}">{{ ucfirst($shift->status) }}</span>
            </div>
        @empty
            <div class="shift-meta">No shifts scheduled.</div>
        @endforelse
    </div>
@endforeach

@endsection
