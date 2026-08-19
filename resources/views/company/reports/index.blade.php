@extends('company.layouts.app')

@section('title', 'Reports')

@section('content')

<style>
    .reports-header {
        margin-bottom: 25px;
    }

    .reports-header h1 {
        margin: 0 0 7px;
        font-size: 27px;
    }

    .reports-header p {
        margin: 0;
        color: #aab4ca;
        font-size: 14px;
    }

    .reports-grid {
        display: grid;
        grid-template-columns:
            repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .report-card {
        display: flex;
        flex-direction: column;
        min-height: 190px;
        padding: 22px;
        border-radius: 10px;
        background: #1e2a44;
        color: #ffffff;
        text-decoration: none;
        transition:
            transform .2s ease,
            background .2s ease;
    }

    .report-card:hover {
        transform: translateY(-3px);
        background: #253552;
        color: #ffffff;
        text-decoration: none;
    }

    .report-icon {
        width: 48px;
        height: 48px;
        margin-bottom: 18px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background:
            rgba(255,255,255,.09);
        font-size: 20px;
    }

    .report-card h3 {
        margin: 0 0 8px;
        font-size: 17px;
    }

    .report-card p {
        margin: 0;
        color: #aab4ca;
        font-size: 13px;
        line-height: 1.5;
    }

    .report-open {
        margin-top: auto;
        padding-top: 18px;
        color: #77aaff;
        font-size: 13px;
        font-weight: 600;
    }

    @media (max-width: 1000px) {
        .reports-grid {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 650px) {
        .reports-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@php
    $companyUser =
        auth('company')->user();
@endphp

<div class="reports-header">

    <h1>Reports Centre</h1>

    <p>
        Access HR, attendance, compliance and
        operational reports from one place.
    </p>

</div>

<div class="reports-grid">

    @if (
        $companyUser->hasPermission(
            'reports.missing-documents'
        )
    )
        <a
            href="{{ route(
                'company.hr.employees.missing-documents'
            ) }}"
            class="report-card"
        >

            <div class="report-icon">
                <i
                    class="fas
                           fa-file-circle-exclamation"
                ></i>
            </div>

            <h3>Missing Documents</h3>

            <p>
                Employees with required documents
                missing or awaiting completion.
            </p>

            <div class="report-open">
                Open report →
            </div>

        </a>
    @endif


    @if (
        $companyUser->hasPermission(
            'reports.holiday'
        )
    )
        <a
            href="{{ route(
                'company.hr.holidays.requests'
            ) }}"
            class="report-card"
        >

            <div class="report-icon">
                <i class="fas fa-plane"></i>
            </div>

            <h3>Holiday Report</h3>

            <p>
                Review employee holiday requests,
                dates and approval status.
            </p>

            <div class="report-open">
                Open report →
            </div>

        </a>
    @endif


    @if (
        $companyUser->hasPermission(
            'reports.attendance'
        )
    )
        <a
            href="{{ route(
                'company.hr.timesheet.index'
            ) }}"
            class="report-card"
        >

            <div class="report-icon">
                <i class="fas fa-clock"></i>
            </div>

            <h3>Attendance / Timesheet</h3>

            <p>
                Review employee working time,
                attendance and timesheet records.
            </p>

            <div class="report-open">
                Open report →
            </div>

        </a>
    @endif


    @if (
        $companyUser->hasPermission(
            'reports.overtime'
        )
    )
        <a
            href="{{ route(
                'company.hr.reports.overtime'
            ) }}"
            class="report-card"
        >

            <div class="report-icon">
                <i
                    class="fas fa-business-time"
                ></i>
            </div>

            <h3>Overtime Report</h3>

            <p>
                View recorded overtime and
                additional working hours.
            </p>

            <div class="report-open">
                Open report →
            </div>

        </a>
    @endif


    @if (
        $companyUser->hasPermission(
            'reports.expense'
        )
    )
        <a
            href="{{ route(
                'company.hr.expenses.index'
            ) }}"
            class="report-card"
        >

            <div class="report-icon">
                <i
                    class="fas fa-receipt"
                ></i>
            </div>

            <h3>Expense Report</h3>

            <p>
                Review submitted employee expenses,
                receipts and approval status.
            </p>

            <div class="report-open">
                Open report →
            </div>

        </a>
    @endif


    @if (
        $companyUser->hasPermission(
            'reports.training-due'
        )
    )
        <a
            href="{{ route(
                'company.hr.training.expiring'
            ) }}"
            class="report-card"
        >

            <div class="report-icon">
                <i
                    class="fas
                           fa-graduation-cap"
                ></i>
            </div>

            <h3>Training Due</h3>

            <p>
                Review training and certificates
                that are expired or approaching expiry.
            </p>

            <div class="report-open">
                Open report →
            </div>

        </a>
    @endif


    @if (
        $companyUser->hasPermission(
            'employee-documents.view'
        )
    )
        <a
            href="{{ route(
                'company.hr.right-to-work'
            ) }}"
            class="report-card"
        >

            <div class="report-icon">
                <i class="fas fa-id-card"></i>
            </div>

            <h3>Right to Work</h3>

            <p>
                Review missing, pending, expired
                and expiring Right to Work evidence.
            </p>

            <div class="report-open">
                Open report →
            </div>

        </a>
    @endif


    @if (
        $companyUser->hasPermission(
            'sponsor-compliance.view'
        )
    )
        <a
            href="{{ route(
                'company.hr.sponsor-compliance.sponsored-workers.index'
            ) }}"
            class="report-card"
        >

            <div class="report-icon">
                <i class="fas fa-passport"></i>
            </div>

            <h3>Sponsor Compliance</h3>

            <p>
                Review sponsored worker visa,
                CoS and compliance information.
            </p>

            <div class="report-open">
                Open report →
            </div>

        </a>
    @endif

</div>

@endsection