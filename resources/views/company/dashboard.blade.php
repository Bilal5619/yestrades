@extends('company.layouts.app')

@section('title', 'Dashboard')

@section('content')

<style>
    .dashboard-welcome {
        margin-bottom: 26px;
    }

    .dashboard-welcome h1 {
        margin: 0 0 7px;
        font-size: 27px;
        font-weight: 600;
    }

    .dashboard-welcome p {
        margin: 0;
        color: #aab4ca;
        font-size: 14px;
    }

    .company-dashboard-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }

    .company-dashboard-card {
        min-height: 135px;
        padding: 23px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #22304d;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.14);
        transition: transform 0.2s ease;
    }

    .company-dashboard-card:hover {
        transform: translateY(-3px);
    }

    .company-dashboard-card-content h3 {
        margin: 0 0 10px;
        color: rgba(255, 255, 255, 0.85);
        font-size: 15px;
        font-weight: 500;
    }

    .company-dashboard-card-content strong {
        display: block;
        font-size: 29px;
        line-height: 1;
    }

    .company-dashboard-card-content small {
        display: block;
        margin-top: 10px;
        color: rgba(255, 255, 255, 0.7);
        font-size: 12px;
    }

    .company-dashboard-card-icon {
        width: 58px;
        height: 58px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.14);
    }

    .company-dashboard-card-icon i {
        font-size: 27px;
    }

    .card-right-work {
        background: #2878dc;
    }

    .card-job-documents {
        background: #685bd4;
    }

    .card-certificates {
        background: #f3a63a;
    }

    .card-pending-jobs {
        background: #ec5762;
    }

    .card-holiday {
        background: #2ebf72;
    }

    .card-starters {
        background: #c85fc8;
    }

    .dashboard-section-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }

    .dashboard-panel {
        background: #1e2a44;
        border-radius: 9px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .dashboard-panel-header {
        padding: 18px 21px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .dashboard-panel-header h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .dashboard-panel-header a {
        color: #77aaff;
        font-size: 13px;
        text-decoration: none;
    }

    .dashboard-panel-body {
        padding: 20px;
    }

    .dashboard-alert-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    }

    .dashboard-alert-item:last-child {
        border-bottom: none;
    }

    .dashboard-alert-icon {
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.1);
    }

    .dashboard-alert-details {
        flex: 1;
    }

    .dashboard-alert-details strong {
        display: block;
        margin-bottom: 4px;
        font-size: 14px;
    }

    .dashboard-alert-details span {
        color: #aab4ca;
        font-size: 12px;
    }

    .dashboard-alert-count {
        min-width: 32px;
        height: 26px;
        padding: 0 9px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ec5762;
        font-size: 12px;
        font-weight: 600;
    }

    .dashboard-placeholder-chart {
        min-height: 260px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed rgba(255, 255, 255, 0.15);
        border-radius: 7px;
        color: #7886a3;
        text-align: center;
    }

    @media (max-width: 1200px) {
        .company-dashboard-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 950px) {
        .dashboard-section-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .company-dashboard-grid {
            grid-template-columns: 1fr;
        }
    }
    .company-dashboard-card {
    color: #ffffff;
    text-decoration: none;
}

.company-dashboard-card:visited,
.company-dashboard-card:hover,
.company-dashboard-card:focus {
    color: #ffffff;
    text-decoration: none;
}

.dashboard-alert-link {
    display: block;
    color: inherit;
    text-decoration: none;
}

.dashboard-alert-link:hover {
    color: inherit;
    text-decoration: none;
}

.dashboard-alert-link:hover .dashboard-alert-item {
    background: rgba(255, 255, 255, 0.04);
}
</style>

<div class="dashboard-welcome">
    <h1>Welcome back, {{ auth('company')->user()->full_name }}!</h1>

    <p>
        Here is the latest overview of Yes Trades company operations.
    </p>
</div>

{{-- Notebook dashboard cards --}}
<div class="company-dashboard-grid">

 <a href="{{ route('company.hr.right-to-work') }}"
   class="company-dashboard-card card-right-work">
        <div class="company-dashboard-card-content">
            <h3>Right to Work Documents</h3>
           <strong>{{ $stats['right_to_work'] }}</strong>
<small>Pending or expiring checks</small>
        </div>

        <div class="company-dashboard-card-icon">
            <i class="fas fa-id-card"></i>
        </div>
    </a>

   <a href="{{ route('company.hr.employees.missing-documents') }}"
   class="company-dashboard-card card-job-documents">
        <div class="company-dashboard-card-content">
            <h3>Employee Job Documents</h3>
           <strong>{{ $stats['employee_documents'] }}</strong>
            <small>Missing or awaiting review</small>
        </div>

        <div class="company-dashboard-card-icon">
            <i class="fas fa-folder-open"></i>
        </div>
    </a>

   <a href="{{ route('company.hr.training.expiring') }}"
   class="company-dashboard-card card-certificates">
        <div class="company-dashboard-card-content">
            <h3>Missing Certificates</h3>
            <strong>{{ $stats['missing_certificates'] }}</strong>
            <small>Required certificates missing</small>
        </div>

        <div class="company-dashboard-card-icon">
            <i class="fas fa-certificate"></i>
        </div>
    </a>

    <a href="{{ route('company.hr.jobs.index') }}"
   class="company-dashboard-card card-pending-jobs">
        <div class="company-dashboard-card-content">
            <h3>Pending Jobs</h3>
           <strong>{{ $stats['pending_jobs'] }}</strong>
            <small>Jobs needing action</small>
        </div>

        <div class="company-dashboard-card-icon">
            <i class="fas fa-briefcase"></i>
        </div>
    </a>

   <a href="{{ route('company.hr.holidays.index') }}"
   class="company-dashboard-card card-holiday">
        <div class="company-dashboard-card-content">
            <h3>Employees on Holiday</h3>
            <strong>{{ $stats['employees_on_holiday'] }}</strong>
            <small>Currently away</small>
        </div>

        <div class="company-dashboard-card-icon">
            <i class="fas fa-plane-departure"></i>
        </div>
    </a>

   <a href="{{ route('company.hr.onboarding.index') }}"
   class="company-dashboard-card card-starters">
        <div class="company-dashboard-card-content">
            <h3>New Starters</h3>
            <strong>{{ $stats['new_starters'] }}</strong>
            <small>Employees being onboarded</small>
        </div>

        <div class="company-dashboard-card-icon">
            <i class="fas fa-user-plus"></i>
        </div>
    </a>

</div>

<div class="dashboard-section-grid">

    {{-- HR employee graph --}}
    <section class="dashboard-panel">
        <div class="dashboard-panel-header">
            <h2>Employee Overview</h2>

            <a href="{{ route('company.hr.overview') }}">
    View HR overview
</a>
        </div>

        <div style="
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 15px;
">
    <div class="dashboard-placeholder-chart" style="min-height: 105px;">
        <div>
            <strong style="font-size: 26px;">
                {{ $employeeOverview['total'] }}
            </strong>
            <div>Total Employees</div>
        </div>
    </div>

    <div class="dashboard-placeholder-chart" style="min-height: 105px;">
        <div>
            <strong style="font-size: 26px;">
                {{ $employeeOverview['active'] }}
            </strong>
            <div>Active</div>
        </div>
    </div>

    <div class="dashboard-placeholder-chart" style="min-height: 105px;">
        <div>
            <strong style="font-size: 26px;">
                {{ $employeeOverview['onboarding'] }}
            </strong>
            <div>Onboarding</div>
        </div>
    </div>

    <div class="dashboard-placeholder-chart" style="min-height: 105px;">
        <div>
            <strong style="font-size: 26px;">
                {{ $employeeOverview['on_leave'] }}
            </strong>
            <div>On Leave</div>
        </div>
    </div>

    <div class="dashboard-placeholder-chart" style="min-height: 105px;">
        <div>
            <strong style="font-size: 26px;">
                {{ $employeeOverview['notice_period'] }}
            </strong>
            <div>Notice Period</div>
        </div>
    </div>

    <div class="dashboard-placeholder-chart" style="min-height: 105px;">
        <div>
            <strong style="font-size: 26px;">
                {{ $employeeOverview['suspended'] }}
            </strong>
            <div>Suspended</div>
        </div>
    </div>
</div>
        </div>
    </section>

    {{-- Reminders --}}
    <section class="dashboard-panel">
        <div class="dashboard-panel-header">
            <h2>Action Required</h2>

            <a href="{{ route('company.hr.overview') }}">
    View all
</a>
        </div>

        <div class="dashboard-panel-body">

            <a href="{{ route('company.hr.right-to-work') }}"
   class="dashboard-alert-link">

    <div class="dashboard-alert-item">
        <div class="dashboard-alert-icon">
            <i class="fas fa-id-card"></i>
        </div>

        <div class="dashboard-alert-details">
            <strong>Right to Work</strong>
            <span>Documents needing review</span>
        </div>

        <div class="dashboard-alert-count">
            {{ $stats['right_to_work'] }}
        </div>
    </div>

</a>

           <a href="{{ route('company.hr.employees.missing-documents') }}"
   class="dashboard-alert-link">

    <div class="dashboard-alert-item">
        <div class="dashboard-alert-icon">
            <i class="fas fa-file-circle-exclamation"></i>
        </div>

        <div class="dashboard-alert-details">
            <strong>Missing Documents</strong>
            <span>Employee records incomplete</span>
        </div>

        <div class="dashboard-alert-count">
            {{ $stats['employee_documents'] }}
        </div>
    </div>

</a>

            <a href="{{ route('company.hr.training.expiring') }}"
   class="dashboard-alert-link">

    <div class="dashboard-alert-item">
        <div class="dashboard-alert-icon">
            <i class="fas fa-graduation-cap"></i>
        </div>

        <div class="dashboard-alert-details">
            <strong>Training Due</strong>
            <span>Training or certificates due</span>
        </div>

        <div class="dashboard-alert-count">
            {{ $stats['training_due'] }}
        </div>
    </div>

</a>

           <a href="{{ route('company.hr.onboarding.index') }}"
   class="dashboard-alert-link">

    <div class="dashboard-alert-item">
        <div class="dashboard-alert-icon">
            <i class="fas fa-user-clock"></i>
        </div>

        <div class="dashboard-alert-details">
            <strong>New Starter Checklists</strong>
            <span>Onboarding actions incomplete</span>
        </div>

        <div class="dashboard-alert-count">
            {{ $stats['pending_onboarding'] }}
        </div>
    </div>

</a>

        </div>
    </section>

</div>

@endsection