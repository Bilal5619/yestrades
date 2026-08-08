<style>
    .company-logo {
        padding: 22px;
        text-align: center;
        background: #111a2d;
    }

    .company-logo strong {
        display: block;
        font-size: 18px;
        letter-spacing: 1px;
    }

    .company-logo span {
        display: block;
        margin-top: 5px;
        font-size: 11px;
        color: #aab4ca;
    }

    .company-user {
        padding: 24px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .company-user-name {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .company-user-role {
        color: #aab4ca;
        font-size: 13px;
    }

    .company-menu {
        padding: 18px 12px;
    }

    .company-menu-title {
        padding: 10px 14px;
        color: #7886a3;
        font-size: 12px;
        text-transform: uppercase;
    }

    .company-menu-item {
        margin-bottom: 5px;
    }

    .company-menu-link,
    .company-submenu-link {
        display: flex;
        align-items: center;
        gap: 13px;
        width: 100%;
        padding: 14px 16px;
        border: 0;
        border-radius: 6px;
        background: transparent;
        color: #c5cddd;
        text-decoration: none;
        font-size: 15px;
        text-align: left;
        cursor: pointer;
        transition: 0.2s;
    }

    .company-menu-link:hover,
    .company-menu-link.active,
    .company-submenu-link:hover,
    .company-submenu-link.active {
        background: #ffffff;
        color: #172036;
    }

    .company-menu-link i,
    .company-submenu-link i {
        width: 22px;
        text-align: center;
    }

    .company-menu-arrow {
        margin-left: auto;
        width: auto !important;
        transition: transform 0.2s ease;
    }

    .company-menu-link.open .company-menu-arrow {
        transform: rotate(90deg);
    }

    .company-submenu {
        display: none;
        padding: 5px 0 5px 18px;
    }

    .company-submenu.open {
        display: block;
    }

    .company-submenu-link {
        padding: 11px 14px;
        font-size: 14px;
    }

    .company-submenu-link::before {
        content: "";
        width: 6px;
        height: 6px;
        flex: 0 0 6px;
        border-radius: 50%;
        background: #7886a3;
    }

    .company-submenu-link:hover::before,
    .company-submenu-link.active::before {
        background: #172036;
    }
</style>

<aside class="company-sidebar">

    <div class="company-logo">
        <strong>YES TRADES</strong>
        <span>COMPANY PORTAL</span>
    </div>

    <div class="company-user">
        <div class="company-user-name">
    <h3>
    {{ auth('company')->user()->full_name }}
</h3>

<p>
    {{ optional(auth('company')->user()->companyRole)->name ?? 'Company User' }}
</p>
</div>

    <div class="company-user-role">
        {{ ucwords(str_replace('_', ' ', auth('company')->user()->role)) }}
    </div>
    </div>
@php
    $companyUser = auth('company')->user();

    $canSeeHrMenu =
        $companyUser->hasPermission('hr.overview.view') ||
        $companyUser->hasPermission('employees.view') ||
        $companyUser->hasPermission('employees.create') ||
        $companyUser->hasPermission('employees.edit') ||
        $companyUser->hasPermission('employees.delete') ||
        $companyUser->hasPermission('holidays.view')    || 
        $companyUser->hasPermission('training.view')|| 
        $companyUser->hasPermission('recruitment.manage') ||
        $companyUser->hasPermission('onboarding.manage');

    $canSeeStaffMenu =
        $companyUser->hasPermission('attendance.manage') ||
        $companyUser->hasPermission('rota.manage') ||
        $companyUser->hasPermission('jobs.view') ||
        $companyUser->hasPermission('jobs.manage') ||
        $companyUser->hasPermission('expenses.manage') ||
        $companyUser->hasPermission('training.manage') ||
        $companyUser->hasPermission('equipment-vehicles.manage');

    $canSeeAnnouncements =
        $companyUser->hasPermission('announcements.view') ||
        $companyUser->hasPermission('announcements.manage');

    $canSeeReports =
        $companyUser->hasPermission('reports.missing-documents') ||
        $companyUser->hasPermission('reports.holiday') ||
        $companyUser->hasPermission('reports.attendance') ||
        $companyUser->hasPermission('reports.overtime') ||
        $companyUser->hasPermission('reports.expense') ||
        $companyUser->hasPermission('reports.training-due');
@endphp

<nav class="company-menu">

    {{-- Dashboard --}}
    @if ($companyUser->hasPermission('dashboard.view'))
        <div class="company-menu-item">
            <a href="{{ route('company.dashboard') }}"
               class="company-menu-link
               {{ request()->routeIs('company.dashboard') ? 'active' : '' }}">

                <i class="fas fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>
        </div>
    @endif


    {{-- HR --}}
    @if ($canSeeHrMenu)
        <div class="company-menu-item">

            <button type="button"
                    class="company-menu-link company-dropdown-toggle"
                    data-target="hr-submenu">

                <i class="fas fa-user-tie"></i>
                <span>HR</span>
                <i class="fas fa-chevron-right company-menu-arrow"></i>
            </button>

            <div class="company-submenu" id="hr-submenu">

                @if ($companyUser->hasPermission('hr.overview.view'))
            <a href="{{ route('company.hr.overview') }}"
            class="company-submenu-link
            {{ request()->routeIs('company.hr.overview') ? 'active' : '' }}">
                HR Overview
            </a>
            @endif

                @if ($companyUser->hasPermission('employees.view'))
                <a href="{{ route('company.hr.employees.index') }}"
                class="company-submenu-link
                {{ request()->routeIs('company.hr.employees.*')
                        ? 'active'
                        : ''
                }}">
                    View Employees
                </a>
            @endif
            @if ($companyUser->hasPermission('employees.create'))
                <a href="{{ route('company.hr.employees.create') }}"
                class="company-submenu-link
                {{ request()->routeIs('company.hr.employees.create')
                        ? 'active'
                        : ''
                }}">
                    Add Employee
                </a>
            @endif


                        @if (
                $companyUser->hasPermission(
                    'employee-documents.view'
                )
            )
                <a href="{{ route(
                        'company.hr.employees.missing-documents'
                    ) }}"
                class="company-submenu-link
                {{ request()->routeIs(
                        'company.hr.employees.missing-documents'
                ) ? 'active' : '' }}">
                    Missing Documents
                </a>
            @endif
                @if (
                    $companyUser->hasPermission(
                        'employee-documents.view'
                    )
                )
                    <a href="{{ route(
                            'company.hr.employees.expiring-documents'
                        ) }}"
                    class="company-submenu-link
                    {{ request()->routeIs(
                            'company.hr.employees.expiring-documents'
                    ) ? 'active' : '' }}">
                        Expiring Documents
                    </a>
                @endif

            @if ($companyUser->hasPermission('employees.view'))
                <a href="{{ route(
                        'company.hr.employees.archived'
                    ) }}"
                class="company-submenu-link
                {{ request()->routeIs(
                        'company.hr.employees.archived'
                ) ? 'active' : '' }}">
                    Archived Employees
                </a>
            @endif

            @if ($companyUser->hasPermission('holidays.view'))
                <a href="{{ route(
                        'company.hr.holidays.index'
                    ) }}"
                class="company-submenu-link
                {{ request()->routeIs(
                        'company.hr.holidays.*'
                ) ? 'active' : '' }}">
                    Holidays
                </a>
            @endif
          
            @if ($companyUser->hasPermission('training.view'))
                    <a href="{{ route(
                            'company.hr.training.index'
                        ) }}"
                    class="company-submenu-link
                    {{ request()->routeIs(
                            'company.hr.training.*'
                    ) ? 'active' : '' }}">
                        Training
                    </a>
                @endif

                @if ($companyUser->hasPermission('recruitment.manage'))
                    <a href="#"
                       class="company-submenu-link">
                        Recruitment
                    </a>
                @endif

               @if ($companyUser->hasPermission('onboarding.view'))
    <a href="{{ route('company.hr.onboarding.index') }}"
       class="company-submenu-link
           {{ request()->routeIs('company.hr.onboarding.*')
               ? 'active'
               : '' }}">
        Onboarding
    </a>
@endif
@if (
    $companyUser->hasPermission(
        'contract_templates.view'
    )
)
    <a href="{{ route(
            'company.hr.contract-templates.index'
        ) }}"
       class="company-submenu-link
           {{ request()->routeIs(
               'company.hr.contract-templates.*'
           )
               ? 'active'
               : ''
           }}">

        Contract Templates
    </a>
@endif
@if (
    $companyUser->hasPermission(
        'payslips.view'
    )
)
    <a href="{{ route(
            'company.hr.payslips.index'
        ) }}"
       class="company-submenu-link
           {{ request()->routeIs(
               'company.hr.payslips.*'
           )
               ? 'active'
               : ''
           }}">

        Payslips
    </a>
@endif
@if ($companyUser->hasPermission('sponsor-compliance.view'))
<a
    href="{{ route(
        'company.hr.sponsor-compliance.sponsored-workers.index'
    ) }}"
    class="company-submenu-link
        {{ request()->routeIs(
            'company.hr.sponsor-compliance.sponsored-workers.*'
        ) ? 'active' : '' }}"
>

    <span>Sponsored Workers</span>
</a>
@endif
            </div>
        </div>
    @endif
    



    {{-- Staff --}}
    @if ($canSeeStaffMenu)
        <div class="company-menu-item">

            <button type="button"
                    class="company-menu-link company-dropdown-toggle"
                    data-target="staff-submenu">

                <i class="fas fa-users"></i>
                <span>Staff</span>
                <i class="fas fa-chevron-right company-menu-arrow"></i>
            </button>

            <div class="company-submenu" id="staff-submenu">

                @if ($companyUser->hasPermission('attendance.manage'))
                    <a href="{{ route('company.hr.attendance.index') }}"
                       class="company-submenu-link
                       {{ request()->routeIs('company.hr.attendance.*') ? 'active' : '' }}">
                        Attendance
                    </a>
                @endif

                @if ($companyUser->hasPermission('rota.manage'))
                    <a href="{{ route('company.hr.shifts.index') }}"
                       class="company-submenu-link
                       {{ request()->routeIs('company.hr.shifts.*') ? 'active' : '' }}">
                        Rota and Shifts
                    </a>
                @endif
                @if(
    $companyUser
        ->hasPermission(
            'jobs.view'
        )
    ||
    $companyUser
        ->hasPermission(
            'jobs.manage'
        )
)

    <a
        href="{{
            route(
                'company.clients.index'
            )
        }}"
        class="
            company-submenu-link

            {{
                request()
                    ->routeIs(
                        'company.clients.*'
                    )
                    ? 'active'
                    : ''
            }}
        "
    >
        Clients / Accounts
    </a>

@endif

                @if ($companyUser->hasPermission('jobs.view') || $companyUser->hasPermission('jobs.manage'))
                    <a href="{{ route('company.hr.jobs.index') }}"
                       class="company-submenu-link
                       {{ request()->routeIs('company.hr.jobs.*') ? 'active' : '' }}">
                        Jobs
                    </a>
                @endif

                @if ($companyUser->hasPermission('expenses.manage'))
                    <a href="#"
                       class="company-submenu-link">
                        Expenses
                    </a>
                @endif

                @if ($companyUser->hasPermission('training.manage'))
                    <a href="#"
                       class="company-submenu-link">
                        Training
                    </a>
                @endif

                @if ($companyUser->hasPermission('equipment-vehicles.manage'))
                    <a href="#"
                       class="company-submenu-link">
                        Equipment and Vehicles
                    </a>
                @endif

            </div>
        </div>
    @endif


    {{-- Announcements --}}
    @if ($canSeeAnnouncements)
        <div class="company-menu-item">

            <a href="{{ route('company.hr.announcements.index') }}"
               class="company-menu-link
               {{ request()->routeIs('company.hr.announcements.*') ? 'active' : '' }}">

                <i class="fas fa-bullhorn"></i>
                <span>Announcements</span>
            </a>

        </div>
    @endif


    {{-- Reports --}}
    @if ($canSeeReports)
        <div class="company-menu-item">

            <button type="button"
                    class="company-menu-link company-dropdown-toggle"
                    data-target="reports-submenu">

                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
                <i class="fas fa-chevron-right company-menu-arrow"></i>
            </button>

            <div class="company-submenu" id="reports-submenu">

                @if ($companyUser->hasPermission('reports.missing-documents'))
                    <a href="#"
                       class="company-submenu-link">
                        Missing Documents
                    </a>
                @endif

                @if ($companyUser->hasPermission('reports.holiday'))
                    <a href="#"
                       class="company-submenu-link">
                        Holiday Report
                    </a>
                @endif

                @if ($companyUser->hasPermission('reports.attendance'))
                    <a href="{{ route('company.hr.timesheet.index') }}"
                       class="company-submenu-link
                       {{ request()->routeIs('company.hr.timesheet.*') ? 'active' : '' }}">
                        Attendance Report
                    </a>
                @endif

                @if ($companyUser->hasPermission('reports.overtime'))
                    <a href="#"
                       class="company-submenu-link">
                        Overtime Report
                    </a>
                @endif

                @if ($companyUser->hasPermission('reports.expense'))
                    <a href="#"
                       class="company-submenu-link">
                        Expense Report
                    </a>
                @endif

                @if ($companyUser->hasPermission('reports.training-due'))
                    <a href="#"
                       class="company-submenu-link">
                        Training Due
                    </a>
                @endif

            </div>
        </div>
    @endif


    {{-- Audit Logs --}}
    @if ($companyUser->hasPermission('audit-logs.view'))
        <div class="company-menu-item">

            <a href="#"
               class="company-menu-link">

                <i class="fas fa-history"></i>
                <span>Audit Logs</span>
            </a>

        </div>
    @endif


    {{-- Users and Permissions --}}
    @if ($companyUser->hasPermission('company-users.view'))
        <div class="company-menu-item">

            <a href="{{ route('company.users.index') }}"
               class="company-menu-link
               {{ request()->routeIs('company.users.*') ? 'active' : '' }}">

                <i class="fas fa-user-shield"></i>
                <span>Users and Permissions</span>
            </a>

        </div>
    @endif


    {{-- Roles --}}
    @if ($companyUser->hasPermission('roles.manage'))
        <div class="company-menu-item">

            <a href="{{ route('company.roles.index') }}"
               class="company-menu-link
               {{ request()->routeIs('company.roles.*') ? 'active' : '' }}">

                <i class="fas fa-user-tag"></i>
                <span>Roles</span>
            </a>

        </div>
    @endif

</nav>

</aside>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropdownButtons = document.querySelectorAll(
            '.company-dropdown-toggle'
        );

        dropdownButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const targetId = button.getAttribute('data-target');
                const submenu = document.getElementById(targetId);

                if (!submenu) {
                    return;
                }

                const isOpen = submenu.classList.contains('open');

                document.querySelectorAll('.company-submenu').forEach(
                    function (item) {
                        item.classList.remove('open');
                    }
                );

                document.querySelectorAll('.company-dropdown-toggle').forEach(
                    function (item) {
                        item.classList.remove('open');
                    }
                );

                if (!isOpen) {
                    submenu.classList.add('open');
                    button.classList.add('open');
                }
            });
        });
    });
</script>