@extends('company.layouts.app')

@section('title', 'Roles')

@section('content')

<style>
    .role-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 24px;
    }

    .role-page-header h1 {
        margin: 0;
        font-size: 26px;
    }

    .role-primary-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 18px;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
    }

    .role-table-card {
        background: #1e2a44;
        border-radius: 9px;
        overflow: hidden;
    }

    .role-table {
        width: 100%;
        border-collapse: collapse;
    }

    .role-table th,
    .role-table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .role-table th {
        background: #172036;
        color: #aab4ca;
        font-size: 13px;
    }

    .role-status {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
    }

    .role-status-active {
        background: rgba(46, 191, 114, 0.18);
        color: #59dc96;
    }

    .role-status-disabled {
        background: rgba(236, 87, 98, 0.18);
        color: #ff7c86;
    }

    .role-small-button {
        padding: 7px 11px;
        border: 0;
        border-radius: 5px;
        background: #f3a63a;
        color: #ffffff;
        cursor: pointer;
    }

    .role-message {
        margin-bottom: 18px;
        padding: 13px 15px;
        border-radius: 7px;
    }

    .role-message-success {
        background: rgba(46, 191, 114, 0.15);
        color: #59dc96;
    }

    .role-message-error {
        background: rgba(236, 87, 98, 0.15);
        color: #ff7c86;
    }
</style>

<div class="role-page-header">
    <div>
        <h1>Roles</h1>
        <p style="color:#aab4ca;">
            Create roles for HR, managers, office staff and employees.
        </p>
    </div>

    <a href="{{ route('company.roles.create') }}"
       class="role-primary-button">
        <i class="fas fa-plus"></i>
        Create Role
    </a>
</div>

@if (session('success'))
    <div class="role-message role-message-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="role-message role-message-error">
        {{ $errors->first() }}
    </div>
@endif

<div class="role-table-card">
    <table class="role-table">
        <thead>
            <tr>
                <th>Role</th>
                <th>Description</th>
                <th>Users</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($roles as $role)
                <tr>
                    <td>
                        {{ $role->name }}

                        @if ($role->slug === 'company-owner')
                            <small style="color:#aab4ca;">
                                System role
                            </small>
                        @endif
                    </td>

                    <td>
                        {{ $role->description ?: 'No description' }}
                    </td>

                    <td>
                        {{ $role->users_count }}
                    </td>

                    <td>
                        @if ($role->status)
                            <span class="role-status role-status-active">
                                Active
                            </span>
                        @else
                            <span class="role-status role-status-disabled">
                                Disabled
                            </span>
                        @endif
                    </td>

                    <td>
                        @if ($role->slug !== 'company-owner')

                            <div style="display:flex; gap:8px; flex-wrap:wrap;">

                                <a href="{{ route(
                                        'company.roles.permissions.edit',
                                        $role
                                    ) }}"
                                class="role-small-button"
                                style="
                                        background:#2878dc;
                                        text-decoration:none;
                                ">
                                    Manage Permissions
                                </a>

                                <form method="POST"
                                    action="{{ route(
                                        'company.roles.toggle-status',
                                        $role
                                    ) }}">

                                    @csrf

                                    <button type="submit"
                                            class="role-small-button">
                                        {{ $role->status
                                            ? 'Disable'
                                            : 'Enable'
                                        }}
                                    </button>
                                </form>

                            </div>

                        @else
                            Full access automatically
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        No roles found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection