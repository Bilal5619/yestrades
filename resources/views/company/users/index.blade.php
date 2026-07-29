@extends('company.layouts.app')

@section('title', 'Company Users')

@section('content')

<style>
    .company-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 24px;
    }

    .company-page-header h1 {
        margin: 0;
        font-size: 26px;
    }

    .company-primary-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 18px;
        border: 0;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
        cursor: pointer;
    }

    .company-table-card {
        background: #1e2a44;
        border-radius: 9px;
        overflow: hidden;
    }

    .company-table {
        width: 100%;
        border-collapse: collapse;
    }

    .company-table th,
    .company-table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .company-table th {
        background: #172036;
        font-size: 13px;
        color: #aab4ca;
    }

    .company-status {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
    }

    .company-status-active {
        background: rgba(46, 191, 114, 0.18);
        color: #59dc96;
    }

    .company-status-disabled {
        background: rgba(236, 87, 98, 0.18);
        color: #ff7c86;
    }

    .company-small-button {
        padding: 7px 11px;
        border: 0;
        border-radius: 5px;
        background: #f3a63a;
        color: #ffffff;
        cursor: pointer;
    }

    .company-message {
        margin-bottom: 18px;
        padding: 13px 15px;
        border-radius: 7px;
    }

    .company-message-success {
        background: rgba(46, 191, 114, 0.15);
        color: #59dc96;
    }

    .company-message-error {
        background: rgba(236, 87, 98, 0.15);
        color: #ff7c86;
    }
</style>

<div class="company-page-header">
    <div>
        <h1>Users and Permissions</h1>
        <p style="color:#aab4ca;">
            Manage company portal accounts and their roles.
        </p>
    </div>

    <a href="{{ route('company.users.create') }}"
       class="company-primary-button">
        <i class="fas fa-user-plus"></i>
        Add User
    </a>
</div>

@if (session('success'))
    <div class="company-message company-message-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="company-message company-message-error">
        {{ $errors->first() }}
    </div>
@endif

<div class="company-table-card">
    <table class="company-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Last Login</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $user->full_name }}</td>

                    <td>{{ $user->email }}</td>

                    <td>
                        {{ optional($user->companyRole)->name ?? 'No role' }}
                    </td>

                    <td>
                        @if ($user->status)
                            <span class="company-status company-status-active">
                                Active
                            </span>
                        @else
                            <span class="company-status company-status-disabled">
                                Disabled
                            </span>
                        @endif
                    </td>

                    <td>
                        {{ $user->last_login_at
                            ? $user->last_login_at->format('d M Y H:i')
                            : 'Never'
                        }}
                    </td>
                    <td>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">

        <a href="{{ route('company.users.edit', $user) }}"
           class="company-small-button"
           style="background:#2878dc; text-decoration:none;">
            Edit
        </a>

        @if (auth('company')->id() !== $user->id)

            <form method="POST"
                  action="{{ route(
                      'company.users.toggle-status',
                      $user
                  ) }}">

                @csrf

                <button type="submit"
                        class="company-small-button">
                    {{ $user->status ? 'Disable' : 'Enable' }}
                </button>
            </form>

        @else
            <span style="color:#aab4ca;">
                Current account
            </span>
        @endif

    </div>
</td>

                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        No company users found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:20px;">
    {{ $users->links() }}
</div>

@endsection