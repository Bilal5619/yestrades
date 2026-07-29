@extends('company.layouts.app')

@section('title', 'Create Role')

@section('content')

<style>
    .role-form-card {
        max-width: 800px;
        padding: 28px;
        border-radius: 9px;
        background: #1e2a44;
    }

    .role-form-group {
        margin-bottom: 20px;
    }

    .role-form-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .role-form-control {
        width: 100%;
        padding: 12px 13px;
        border: 1px solid #3a4967;
        border-radius: 6px;
        background: #172036;
        color: #ffffff;
        outline: none;
    }

    textarea.role-form-control {
        min-height: 120px;
        resize: vertical;
    }

    .role-form-error {
        display: block;
        margin-top: 6px;
        color: #ff7c86;
        font-size: 13px;
    }

    .role-form-actions {
        display: flex;
        gap: 12px;
    }

    .role-save-button,
    .role-cancel-button {
        padding: 12px 18px;
        border: 0;
        border-radius: 6px;
        text-decoration: none;
        cursor: pointer;
    }

    .role-save-button {
        background: #2878dc;
        color: #ffffff;
    }

    .role-cancel-button {
        background: #3a4967;
        color: #ffffff;
    }
</style>

<h1 style="margin-top:0;">Create Role</h1>

<p style="color:#aab4ca;">
    Create the role first. Permissions will be assigned in the next step.
</p>

<div class="role-form-card">

    <form method="POST"
          action="{{ route('company.roles.store') }}">

        @csrf

        <div class="role-form-group">
            <label for="name">Role name</label>

            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                class="role-form-control"
                placeholder="Example: HR Manager"
                required
            >

            @error('name')
                <span class="role-form-error">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <div class="role-form-group">
            <label for="description">Description</label>

            <textarea
                id="description"
                name="description"
                class="role-form-control"
                placeholder="Describe what this role is for."
            >{{ old('description') }}</textarea>

            @error('description')
                <span class="role-form-error">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <div class="role-form-group">
            <label for="status">Status</label>

            <select
                id="status"
                name="status"
                class="role-form-control"
                required
            >
                <option value="1"
                    {{ old('status', '1') === '1' ? 'selected' : '' }}>
                    Active
                </option>

                <option value="0"
                    {{ old('status') === '0' ? 'selected' : '' }}>
                    Disabled
                </option>
            </select>
        </div>

        <div class="role-form-actions">
            <button type="submit"
                    class="role-save-button">
                Create Role
            </button>

            <a href="{{ route('company.roles.index') }}"
               class="role-cancel-button">
                Cancel
            </a>
        </div>

    </form>

</div>

@endsection