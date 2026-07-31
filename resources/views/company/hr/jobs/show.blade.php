@extends('company.layouts.app')

@section('title', $job->title)

@section('content')

@include('company.hr.jobs.partials.styles')

<div class="jobsheet-header">
    <div>
        <h1>{{ $job->title }}</h1>
        <p>
            <span class="jobsheet-badge jobsheet-badge-{{ $job->priority }}">{{ ucfirst($job->priority) }}</span>
            <span class="jobsheet-badge jobsheet-status-{{ $job->status }}">{{ ucwords(str_replace('_', ' ', $job->status)) }}</span>
        </p>
    </div>

    <div style="display:flex; gap:10px;">
        @if (auth('company')->user()->hasPermission('jobs.manage'))
            <a href="{{ route('company.hr.jobs.edit', $job) }}" class="jobsheet-back">Edit</a>
        @endif
        <a href="{{ route('company.hr.jobs.index') }}" class="jobsheet-back">
            <i class="fas fa-arrow-left"></i>
            Job Sheet
        </a>
    </div>
</div>

<div class="jobsheet-form-card">
    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">Assigned To</div>
        <div class="jobsheet-detail-value">{{ optional($job->employee)->full_name ?? 'Unassigned' }}</div>
    </div>
    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">Scheduled</div>
        <div class="jobsheet-detail-value">{{ $job->scheduled_date->format('d M Y') }} {{ $job->scheduled_start_time }}</div>
    </div>
    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">Expected Duration</div>
        <div class="jobsheet-detail-value">{{ $job->expected_duration_minutes ? $job->expected_duration_minutes . ' minutes' : '—' }}</div>
    </div>
    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">Customer</div>
        <div class="jobsheet-detail-value">{{ $job->customer_name ?? '—' }} {{ $job->customer_phone ? '(' . $job->customer_phone . ')' : '' }}</div>
    </div>
    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">Site Address</div>
        <div class="jobsheet-detail-value">{{ $job->site_address ?? '—' }} {{ $job->site_postcode }}</div>
    </div>
    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">Description</div>
        <div class="jobsheet-detail-value" style="white-space: pre-line;">{{ $job->description ?? '—' }}</div>
    </div>
    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">Internal Notes</div>
        <div class="jobsheet-detail-value" style="white-space: pre-line;">{{ $job->internal_notes ?? '—' }}</div>
    </div>
    <div class="jobsheet-detail-row">
        <div class="jobsheet-detail-label">Created By</div>
        <div class="jobsheet-detail-value">{{ optional($job->createdBy)->full_name ?? '—' }}</div>
    </div>
</div>

@endsection
