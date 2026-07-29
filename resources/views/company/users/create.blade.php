@extends('company.layouts.app')

@section('title', 'Add Company User')

@section('content')

<style>
    .company-form-card {
        max-width: 850px;
        padding: 28px;
        border-radius: 9px;
        background: #1e2a44;
    }

    .company-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .company-form-group {
        margin-bottom: 20px;
    }

    .company-form-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .company-form-control {
        width: 100%;
        height: 46px;
        padding: 0 13px;
        border: 1px solid #3a4967;
        border-radius: 6px;
        background: #172036;
        color: #ffffff;
        outline: none;
    }

    .company-form-error {
        display: block;
        margin-top: 6px;
        color: #ff7c86;
        font-size: 13px;
    }

    .company-form-actions {
        display: flex;
        gap: 12px;
        margin-top: 10px;
    }

    .company-save-button,
    .company-cancel-button {
        padding: 12px 18px;
        border: 0;
        border-radius: 6px;
        text-decoration: none;
        cursor: pointer;
    }

    .company-save-button {
        background: #2878dc;
        color: #ffffff;
    }

    .company-cancel-button {
        background: #3a4967;
        color: #ffffff;
    }

    @media (max-width: 700px) {
        .company-form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<h1 style="margin-top:0;">Add Company User</h1>

<p style="color:#aab4ca;">
    Create an account for HR, office staff, managers or other authorised users.
</p>

<div class="company-form-card">

    <form method="POST"
          action="{{ route('company.users.store') }}">

        @csrf

        <div class="company-form-grid">

            <div class="company-form-group">
                <label for="first_name">First name</label>

                <input
                    id="first_name"
                    type="text"
                    name="first_name"
                    value="{{ old('first_name') }}"
                    class="company-form-control"
                    required
                >

                @error('first_name')
                    <span class="company-form-error">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="company-form-group">
                <label for="last_name">Last name</label>

                <input
                    id="last_name"
                    type="text"
                    name="last_name"
                    value="{{ old('last_name') }}"
                    class="company-form-control"
                >
            </div>

        </div>

        <div class="company-form-group">
            <label for="email">Email address</label>

            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="company-form-control"
                required
            >

            @error('email')
                <span class="company-form-error">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <div class="company-form-grid">

            <div class="company-form-group">
                <label for="password">Temporary password</label>

                <input
                    id="password"
                    type="password"
                    name="password"
                    class="company-form-control"
                    required
                >
            </div>

            <div class="company-form-group">
                <label for="password_confirmation">
                    Confirm password
                </label>

                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    class="company-form-control"
                    required
                >
            </div>

        </div>

        <div class="company-form-grid">

            <div class="company-form-group">
                <label for="role_id">Role</label>

                <select
                    id="role_id"
                    name="role_id"
                    class="company-form-control"
                    required
                >
                    <option value="">Select role</option>

                    @foreach ($roles as $role)
                        <option
                            value="{{ $role->id }}"
                            {{ old('role_id') == $role->id
                                ? 'selected'
                                : ''
                            }}
                        >
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>

                @error('role_id')
                    <span class="company-form-error">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="company-form-group">
                <label for="status">Account status</label>

                <select
                    id="status"
                    name="status"
                    class="company-form-control"
                    required
                >
                    <option value="1">Active</option>
                    <option value="0">Disabled</option>
                </select>
            </div>

        </div>

        <div class="company-form-actions">
            <button type="submit"
                    class="company-save-button">
                Create User
            </button>

            <a href="{{ route('company.users.index') }}"
               class="company-cancel-button">
                Cancel
            </a>
        </div>

    </form>

</div>

@endsection