@extends('company.layouts.app')

@section('title', 'Employee Onboarding')

@section('content')

<style>
    .onboarding-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .onboarding-detail-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .onboarding-detail-header p {
        margin: 0;
        color: #aab4ca;
    }

    .onboarding-back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 15px;
        border-radius: 7px;
        background: #3a4967;
        color: #ffffff;
        text-decoration: none;
    }

    .onboarding-employee-card {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 20px;
        padding: 22px;
        margin-bottom: 22px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .onboarding-employee-name {
        margin-bottom: 6px;
        font-size: 22px;
        font-weight: 700;
    }

    .onboarding-employee-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 18px;
        color: #aab4ca;
        font-size: 14px;
    }

    .onboarding-status {
        display: inline-flex;
        align-items: center;
        height: fit-content;
        padding: 6px 11px;
        border-radius: 15px;
        font-size: 13px;
    }

    .onboarding-status-in_progress {
        background: rgba(47,128,237,0.16);
        color: #76adf2;
    }

    .onboarding-status-pending {
        background: rgba(255,179,71,0.16);
        color: #ffc46f;
    }

    .onboarding-status-completed {
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .onboarding-status-cancelled {
        background: rgba(255,93,103,0.16);
        color: #ff8a92;
    }

    .onboarding-progress-card {
        padding: 20px;
        margin-bottom: 22px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .onboarding-progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .onboarding-progress-label {
        font-weight: 600;
    }

    .onboarding-progress-value {
        color: #aab4ca;
    }

    .onboarding-progress-track {
        height: 11px;
        overflow: hidden;
        border-radius: 999px;
        background: #11192a;
    }

    .onboarding-progress-bar {
        height: 100%;
        border-radius: 999px;
        background: #2f80ed;
    }

    .onboarding-sections-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .onboarding-section-card {
        padding: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .onboarding-section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 16px;
    }

    .onboarding-section-title {
        margin: 0;
        font-size: 18px;
    }

    .onboarding-section-status {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 13px;
        font-size: 12px;
    }

    .onboarding-section-status-pending {
        background: rgba(255,179,71,0.16);
        color: #ffc46f;
    }

    .onboarding-section-status-in_progress {
        background: rgba(47,128,237,0.16);
        color: #76adf2;
    }

    .onboarding-section-status-completed {
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .onboarding-section-status-not_applicable {
        background: rgba(170,180,202,0.14);
        color: #c4ccdc;
    }

    .onboarding-section-details {
        margin-bottom: 16px;
        color: #aab4ca;
        font-size: 13px;
        line-height: 1.6;
    }

    .onboarding-section-button {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 12px;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
    }

    .onboarding-section-button-disabled {
        background: #3a4967;
        color: #c4ccdc;
        cursor: default;
    }

    @media (max-width: 850px) {
        .onboarding-sections-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .onboarding-detail-header,
        .onboarding-employee-card {
            grid-template-columns: 1fr;
            flex-direction: column;
        }
    }
</style>

<div class="onboarding-detail-header">

    <div>
        <h1>Employee Onboarding</h1>

        <p>
            Review and complete all required onboarding sections.
        </p>
    </div>

    <a href="{{ route('company.hr.onboarding.index') }}"
       class="onboarding-back-button">
        <i class="fas fa-arrow-left"></i>
        Onboarding Overview
    </a>

</div>

<div class="onboarding-employee-card">

    <div>
        <div class="onboarding-employee-name">
            {{ optional($onboarding->employee)->first_name }}
            {{ optional($onboarding->employee)->last_name }}
        </div>

        <div class="onboarding-employee-meta">

            <span>
                Employee ID:
                {{ optional($onboarding->employee)->employee_number }}
            </span>

            <span>
                Job:
                {{ optional($onboarding->employee)->job_title ?: 'Not set' }}
            </span>

            <span>
                Department:
                {{ optional($onboarding->employee)->department ?: 'Not set' }}
            </span>

            <span>
                Started:
                {{ optional($onboarding->started_at)->format('d M Y') ?? '—' }}
            </span>

        </div>
    </div>

    <span class="
        onboarding-status
        onboarding-status-{{ $onboarding->status }}
    ">
        {{ ucwords(str_replace('_', ' ', $onboarding->status)) }}
    </span>

</div>

<div class="onboarding-progress-card">

    <div class="onboarding-progress-header">

        <div class="onboarding-progress-label">
            Overall Onboarding Progress
        </div>

        <div class="onboarding-progress-value">
            {{ $onboarding->progress_percentage }}%
        </div>

    </div>

    <div class="onboarding-progress-track">
        <div class="onboarding-progress-bar"
             style="width:{{ $onboarding->progress_percentage }}%;">
        </div>
    </div>

</div>

<div class="onboarding-sections-grid">

    @foreach ($onboarding->sections as $section)

        <div class="onboarding-section-card">

            <div class="onboarding-section-header">

                <h2 class="onboarding-section-title">
                    {{ $section->title }}
                </h2>

                <span class="
                    onboarding-section-status
                    onboarding-section-status-{{ $section->status }}
                ">
                    {{ ucwords(str_replace('_', ' ', $section->status)) }}
                </span>

            </div>

            <div class="onboarding-section-details">

                @if ($section->is_required)
                    <div>Required section</div>
                @else
                    <div>Optional section</div>
                @endif

                @if ($section->completed_at)
                    <div>
                        Completed:
                        {{ $section->completed_at->format('d M Y H:i') }}
                    </div>
                @endif

                @if ($section->completedByUser)
                    <div>
                        Completed by:
                        {{ trim(
    $section->completedByUser->first_name
    . ' '
    . $section->completedByUser->last_name
) }}
                    </div>
                @endif

                @if ($section->notes)
                    <div>
                        Notes: {{ $section->notes }}
                    </div>
                @endif

            </div>
@if ($section->section_key === 'job_manager_confirmation')

    <a href="{{ route(
            'company.hr.onboarding.job-manager',
            $onboarding
        ) }}"
       class="onboarding-section-button">

        <i class="fas fa-briefcase"></i>
        Open Section
    </a>

@elseif ($section->section_key === 'personal_bank_details')

    <a href="{{ route(
            'company.hr.onboarding.personal-details',
            $onboarding
        ) }}"
       class="onboarding-section-button">

        <i class="fas fa-user-shield"></i>
        Open Section
    </a>
@elseif ($section->section_key === 'induction_meeting')

    <a href="{{ route(
            'company.hr.onboarding.induction',
            $onboarding
        ) }}"
       class="onboarding-section-button">

        <i class="fas fa-users"></i>
        Open Section
    </a>
    @elseif ($section->section_key === 'payroll_setup')

    <a href="{{ route(
            'company.hr.onboarding.payroll',
            $onboarding
        ) }}"
       class="onboarding-section-button">

        <i class="fas fa-money-check-alt"></i>
        Open Section
    </a>
    @elseif ($section->section_key === 'employee_contract')

    <a href="{{ route(
            'company.hr.onboarding.contract',
            $onboarding
        ) }}"
       class="onboarding-section-button">

        <i class="fas fa-file-contract"></i>
        Open Section
    </a>
@else

    <span class="
        onboarding-section-button
        onboarding-section-button-disabled
    ">
        Open Section
    </span>

@endif

        </div>

    @endforeach

</div>

@endsection