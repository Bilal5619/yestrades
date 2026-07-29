@extends('company.layouts.app')

@section('title', 'HR Overview')

@section('content')

<style>
    .hr-header {
        margin-bottom: 25px;
    }

    .hr-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .hr-header p {
        margin: 0;
        color: #aab4ca;
    }

    .hr-stat-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .hr-stat-card {
        padding: 22px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .hr-stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .hr-stat-label {
        margin-bottom: 12px;
        color: #aab4ca;
        font-size: 14px;
    }

    .hr-stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #ffffff;
    }

    .hr-stat-icon {
        display: flex;
        width: 48px;
        height: 48px;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: rgba(40, 120, 220, 0.16);
        color: #57a1ff;
        font-size: 20px;
    }

    .hr-panel {
        padding: 22px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .hr-panel h2 {
        margin: 0 0 18px;
        font-size: 20px;
    }

    .hr-table {
        width: 100%;
        border-collapse: collapse;
    }

    .hr-table th,
    .hr-table td {
        padding: 13px 12px;
        text-align: left;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }

    .hr-table th {
        color: #aab4ca;
        font-size: 13px;
    }

    .hr-status {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 14px;
        background: rgba(46, 191, 114, 0.16);
        color: #59dc96;
        font-size: 12px;
    }
    
    .hr-stat-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    width: 100%;
    }
    
    .hr-stat-icon {
        flex-shrink: 0;
    }

    @media (max-width: 1000px) {
        .hr-stat-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 650px) {
        .hr-stat-grid {
            grid-template-columns: 1fr;
        }

        .hr-panel {
            overflow-x: auto;
        }
    }
</style>

<div class="hr-header">
    <h1>HR Overview</h1>

    <p>
        View employee, document and onboarding information.
    </p>
</div>

<div class="hr-stat-grid">
<a href="{{ route(
        'company.hr.employees.index'
    ) }}"
   style="text-decoration:none; color:inherit;">
    <div class="hr-stat-card">
        <div class="hr-stat-top">
            <div>
                <div class="hr-stat-label">Total Employees</div>
                <div class="hr-stat-value">
                    {{ $stats['total_employees'] }}
                </div>
            </div>

            <div class="hr-stat-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

</a>
<a href="{{ route(
        'company.hr.employees.missing-documents'
    ) }}"
   style="text-decoration:none; color:inherit;">

    <div class="hr-stat-card">
        <div class="hr-stat-top">
            <div>
                <div class="hr-stat-label">Missing Documents</div>
                <div class="hr-stat-value">
                    {{ $stats['missing_documents'] }}
                </div>
            </div>

            <div class="hr-stat-icon">
                <i class="fas fa-folder-open"></i>
            </div>
        </div>
    </div>
</a>

    <div class="hr-stat-card">
        <div class="hr-stat-top">
            <div>
                <div class="hr-stat-label">Employees on Holiday</div>
                <div class="hr-stat-value">
                    {{ $stats['employees_on_holiday'] }}
                </div>
            </div>

            <div class="hr-stat-icon">
                <i class="fas fa-plane"></i>
            </div>
        </div>
    </div>

    <div class="hr-stat-card">
        <div class="hr-stat-top">
            <div>
                <div class="hr-stat-label">New Starters</div>
                <div class="hr-stat-value">
                    {{ $stats['new_starters'] }}
                </div>
            </div>

            <div class="hr-stat-icon">
                <i class="fas fa-user-plus"></i>
            </div>
        </div>
    </div>

<a href="{{ route(
        'company.hr.employees.expiring-documents'
    ) }}"
   style="text-decoration:none; color:inherit;">
    <div class="hr-stat-card">
        <div class="hr-stat-top">
            <div>
                <div class="hr-stat-label">
                Expiring Documents
            </div>

            <div class="hr-stat-value">
                {{ $stats['expiring_documents'] }}
            </div>
            </div>

            <div class="hr-stat-icon">
                <i class="fas fa-id-card"></i>
            </div>
        </div>
    </div>
</a>


@if (
    auth('company')->user()
        ->hasPermission('training.view')
)
    <a href="{{ route(
            'company.hr.training.expiring'
        ) }}"
      style="
    display:block;
    height:100%;
    text-decoration:none;
    color:inherit;
">

        <div class="hr-stat-card">

            <div class="hr-stat-content">

                <div>
                    <div class="hr-stat-label">
                        Training Due
                    </div>

                    <div class="hr-stat-value">
                        {{ $stats['training_due'] }}
                    </div>
                </div>

                <div class="hr-stat-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>

            </div>

        </div>

    </a>

@else
    <div class="hr-stat-card">

        <div class="hr-stat-content">

            <div>
                <div class="hr-stat-label">
                    Training Due
                </div>

                <div class="hr-stat-value">
                    {{ $stats['training_due'] }}
                </div>
            </div>

            <div class="hr-stat-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>

        </div>

    </div>
@endif
    <div class="hr-stat-card">
        <div class="hr-stat-top">
            <div>
                <div class="hr-stat-label">Pending Onboarding</div>

                <div class="hr-stat-value">
                    {{ $stats['pending_onboarding'] }}
                </div>
            </div>

            <div class="hr-stat-icon">
                <i class="fas fa-clipboard-check"></i>
            </div>
        </div>
    </div>

</div>

<div class="hr-panel">

    <h2>Recent Company Users</h2>

    <table class="hr-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($recentCompanyUsers as $user)
                <tr>
                    <td>{{ $user->full_name }}</td>

                    <td>{{ $user->email }}</td>

                    <td>
                        {{ optional($user->companyRole)->name ?? 'No role' }}
                    </td>

                    <td>
                        @if ($user->status)
                            <span class="hr-status">Active</span>
                        @else
                            Disabled
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        No company users found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

@endsection