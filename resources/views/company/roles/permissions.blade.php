@extends('company.layouts.app')

@section('title', 'Role Permissions')

@section('content')

<style>
    .permission-header {
        margin-bottom: 24px;
    }

    .permission-header h1 {
        margin: 0 0 6px;
        font-size: 26px;
    }

    .permission-card {
        margin-bottom: 20px;
        padding: 22px;
        border-radius: 9px;
        background: #1e2a44;
    }

    .permission-card h3 {
        margin: 0 0 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .permission-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .permission-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 12px;
        border: 1px solid #3a4967;
        border-radius: 7px;
        background: #172036;
        cursor: pointer;
    }

    .permission-item input {
        width: 17px;
        height: 17px;
        margin-top: 2px;
    }

    .permission-name {
        display: block;
        font-weight: 600;
    }

    .permission-slug {
        display: block;
        margin-top: 3px;
        color: #aab4ca;
        font-size: 12px;
    }

    .permission-actions {
        display: flex;
        gap: 12px;
        margin-top: 22px;
    }

    .permission-save,
    .permission-cancel,
    .permission-select-button {
        padding: 11px 17px;
        border: 0;
        border-radius: 6px;
        color: #ffffff;
        text-decoration: none;
        cursor: pointer;
    }

    .permission-save {
        background: #2878dc;
    }

    .permission-cancel {
        background: #3a4967;
    }

    .permission-select-button {
        margin-right: 8px;
        margin-bottom: 18px;
        background: #50627f;
    }

    @media (max-width: 750px) {
        .permission-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="permission-header">
    <h1>Permissions: {{ $role->name }}</h1>

    <p style="color:#aab4ca;">
        Choose which areas this role can access.
    </p>
</div>

<button type="button"
        class="permission-select-button"
        onclick="selectAllPermissions()">
    Select All
</button>

<button type="button"
        class="permission-select-button"
        onclick="clearAllPermissions()">
    Clear All
</button>

<form method="POST"
      action="{{ route(
          'company.roles.permissions.update',
          $role
      ) }}">

    @csrf
    @method('PUT')

    @foreach ($permissions as $module => $modulePermissions)

        <div class="permission-card">
            <h3>{{ $module }}</h3>

            <div class="permission-grid">

                @foreach ($modulePermissions as $permission)

                    <label class="permission-item">

                        <input
                            type="checkbox"
                            name="permissions[]"
                            value="{{ $permission->id }}"
                            {{ in_array(
                                $permission->id,
                                old(
                                    'permissions',
                                    $selectedPermissions
                                )
                            ) ? 'checked' : '' }}
                        >

                        <span>
                            <span class="permission-name">
                                {{ $permission->name }}
                            </span>

                            <span class="permission-slug">
                                {{ $permission->slug }}
                            </span>
                        </span>

                    </label>

                @endforeach

            </div>
        </div>

    @endforeach

    <div class="permission-actions">
        <button type="submit"
                class="permission-save">
            Save Permissions
        </button>

        <a href="{{ route('company.roles.index') }}"
           class="permission-cancel">
            Cancel
        </a>
    </div>

</form>

<script>
    function selectAllPermissions() {
        document
            .querySelectorAll('input[name="permissions[]"]')
            .forEach(function (checkbox) {
                checkbox.checked = true;
            });
    }

    function clearAllPermissions() {
        document
            .querySelectorAll('input[name="permissions[]"]')
            .forEach(function (checkbox) {
                checkbox.checked = false;
            });
    }
</script>

@endsection