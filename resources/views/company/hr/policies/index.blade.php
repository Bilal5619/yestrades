@extends('company.layouts.app')

@section('title', 'Company Policies')

@section('content')

<style>
    .policy-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .policy-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .policy-header p {
        margin: 0;
        color: #aab4ca;
    }

    .policy-button {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 11px 16px;
        border-radius: 7px;
        background: #2878dc;
        color: white;
        text-decoration: none;
        border: 0;
        cursor: pointer;
    }

    .policy-grid {
        display: grid;
        grid-template-columns:
            repeat(
                auto-fill,
                minmax(300px, 1fr)
            );
        gap: 18px;
    }

    .policy-card {
        background: #1e2a44;
        border-radius: 10px;
        padding: 20px;
    }

    .policy-card h3 {
        margin: 0 0 8px;
    }

    .policy-description {
        color: #aab4ca;
        font-size: 14px;
        min-height: 40px;
    }

    .policy-meta {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 17px;
        font-size: 13px;
    }

    .policy-stat {
        padding: 6px 9px;
        border-radius: 15px;
        background: #162138;
    }

    .policy-card-actions {
        display: flex;
        gap: 10px;
        margin-top: 18px;
    }

    .policy-secondary {
        background: #3a4967;
    }

    .policy-empty {
        padding: 35px;
        background: #1e2a44;
        border-radius: 10px;
        text-align: center;
        color: #aab4ca;
    }

    @media (max-width: 700px) {
        .policy-header {
            flex-direction: column;
        }
    }
</style>

<div class="policy-header">

    <div>
        <h1>Company Policies</h1>

        <p>
            Manage company policies and employee acknowledgements.
        </p>
    </div>

    @if (
        auth('company')
            ->user()
            ->hasPermission('policies.manage')
    )
        <a
            href="{{ route(
                'company.hr.policies.create'
            ) }}"
            class="policy-button"
        >
            <i class="fas fa-plus"></i>
            Add Policy
        </a>
    @endif

</div>

@if (session('success'))
    <div
        style="
            padding:12px 15px;
            background:rgba(46,191,114,.15);
            color:#6fe6a4;
            border-radius:7px;
            margin-bottom:20px;
        "
    >
        {{ session('success') }}
    </div>
@endif

@forelse ($policies as $policy)

    @if ($loop->first)
        <div class="policy-grid">
    @endif

    <div class="policy-card">

        <h3>
            {{ $policy->title }}
        </h3>

        <div
            style="
                color:#82b7ff;
                font-size:12px;
                margin-bottom:12px;
            "
        >
            Version:
            {{ $policy->version ?: 'Not specified' }}
        </div>

        <div class="policy-description">
            {{
                \Illuminate\Support\Str::limit(
                    $policy->description ?: 'No description provided.',
                    130
                )
            }}
        </div>

        <div class="policy-meta">

            <span class="policy-stat">
                Assigned:
                {{ $policy->acknowledgements_count }}
            </span>

            <span class="policy-stat">
                Pending:
                {{ $policy->pending_count }}
            </span>

            <span class="policy-stat">
                Acknowledged:
                {{ $policy->acknowledged_count }}
            </span>

        </div>

        <div class="policy-card-actions">

            <a
                href="{{ route(
                    'company.hr.policies.show',
                    $policy
                ) }}"
                class="policy-button"
            >
                View
            </a>

            @if ($policy->file_path)
                <a
                    href="{{ route(
                        'company.hr.policies.download',
                        $policy
                    ) }}"
                    class="
                        policy-button
                        policy-secondary
                    "
                >
                    Download
                </a>
            @endif

        </div>

    </div>

    @if ($loop->last)
        </div>
    @endif

@empty

    <div class="policy-empty">
        No company policies have been created yet.
    </div>

@endforelse

@if ($policies->hasPages())
    <div style="margin-top:20px;">
        {{ $policies->links() }}
    </div>
@endif

@endsection