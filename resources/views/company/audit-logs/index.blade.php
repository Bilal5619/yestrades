@extends('company.layouts.app')

@section('title', 'Audit Logs')

@section('content')

<style>
    .audit-header {
        margin-bottom: 24px;
    }

    .audit-header h1 {
        margin: 0 0 7px;
        font-size: 26px;
    }

    .audit-header p {
        margin: 0;
        color: #aab4ca;
    }

    .audit-panel {
        background: #1e2a44;
        border-radius: 9px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .audit-filters {
        display: grid;
        grid-template-columns:
            minmax(180px, 2fr)
            minmax(150px, 1fr)
            minmax(120px, 1fr)
            minmax(140px, 1fr)
            minmax(140px, 1fr)
            auto;
        gap: 12px;
        align-items: end;
    }

    .audit-field label {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
        color: #aab4ca;
    }

    .audit-field input,
    .audit-field select {
        width: 100%;
        box-sizing: border-box;
        background: #162138;
        color: #ffffff;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 6px;
        padding: 10px 11px;
    }

    .audit-button {
        border: 0;
        border-radius: 6px;
        padding: 11px 16px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    .audit-table-wrap {
        overflow-x: auto;
    }

    .audit-table {
        width: 100%;
        border-collapse: collapse;
    }

    .audit-table th,
    .audit-table td {
        padding: 13px 12px;
        text-align: left;
        border-bottom:
            1px solid rgba(255,255,255,.07);
        vertical-align: top;
    }

    .audit-table th {
        color: #aab4ca;
        font-size: 12px;
        font-weight: 600;
    }

    .audit-table td {
        font-size: 13px;
    }

    .audit-method {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        background: rgba(255,255,255,.09);
        font-size: 11px;
        font-weight: 600;
    }

    .audit-muted {
        color: #aab4ca;
        font-size: 12px;
    }

    @media (max-width: 1000px) {
        .audit-filters {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 600px) {
        .audit-filters {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="audit-header">
    <h1>Audit Logs</h1>

    <p>
        Review important actions performed by Company Portal users.
    </p>
</div>

<div class="audit-panel">

    <form
        method="GET"
        action="{{ route('company.audit-logs.index') }}"
        class="audit-filters"
    >

        <div class="audit-field">
            <label>Search</label>

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Action, route or IP"
            >
        </div>

        <div class="audit-field">
            <label>User</label>

            <select name="user_id">
                <option value="">All users</option>

                @foreach ($users as $user)
                    <option
                        value="{{ $user->id }}"
                        {{ (string) request('user_id') ===
                            (string) $user->id
                                ? 'selected'
                                : '' }}
                    >
                        {{ $user->full_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="audit-field">
            <label>Method</label>

            <select name="method">
                <option value="">All</option>

                @foreach (['POST', 'PUT', 'PATCH', 'DELETE'] as $method)
                    <option
                        value="{{ $method }}"
                        {{ request('method') === $method
                            ? 'selected'
                            : '' }}
                    >
                        {{ $method }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="audit-field">
            <label>From</label>

            <input
                type="date"
                name="date_from"
                value="{{ request('date_from') }}"
            >
        </div>

        <div class="audit-field">
            <label>To</label>

            <input
                type="date"
                name="date_to"
                value="{{ request('date_to') }}"
            >
        </div>

        <button
            type="submit"
            class="audit-button"
        >
            Filter
        </button>

    </form>

</div>

<div class="audit-panel audit-table-wrap">

    <table class="audit-table">

        <thead>
            <tr>
                <th>Date / Time</th>
                <th>User</th>
                <th>Action</th>
                <th>Method</th>
                <th>IP</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>

            @forelse ($logs as $log)

                <tr>

                    <td>
                        {{ $log->created_at->format('d M Y') }}

                        <div class="audit-muted">
                            {{ $log->created_at->format('H:i:s') }}
                        </div>
                    </td>

                    <td>
                        @if ($log->companyUser)
                            {{ $log->companyUser->full_name }}

                            <div class="audit-muted">
                                {{ $log->companyUser->email }}
                            </div>
                        @else
                            Deleted / unavailable user
                        @endif
                    </td>

                    <td>
                        {{ $log->action }}

                        @if ($log->request_path)
                            <div class="audit-muted">
                                {{ $log->request_path }}
                            </div>
                        @endif
                    </td>

                    <td>
                        <span class="audit-method">
                            {{ $log->method }}
                        </span>
                    </td>

                    <td>
                        {{ $log->ip_address ?: '—' }}
                    </td>

                    <td>
                        {{ $log->response_status ?: '—' }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6">
                        No audit logs found.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

</div>

{{ $logs->links() }}

@endsection