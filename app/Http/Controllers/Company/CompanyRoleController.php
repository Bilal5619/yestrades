<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\CompanyRole;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Company\CompanyPermission;
class CompanyRoleController extends Controller
{
    public function index()
    {
        $roles = CompanyRole::withCount('users')
            ->orderBy('name')
            ->get();

        return view('company.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('company.roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:company_roles,name',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $number = 2;

        while (CompanyRole::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $number;
            $number++;
        }

        CompanyRole::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_system' => false,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('company.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function toggleStatus(CompanyRole $role)
    {
        if ($role->slug === 'company-owner') {
            return back()->withErrors([
                'role' => 'The Company Owner role cannot be disabled.',
            ]);
        }

        $role->update([
            'status' => !$role->status,
        ]);

        return back()->with(
            'success',
            'Role status updated successfully.'
        );
    }

    public function editPermissions(CompanyRole $role)
{
    if ($role->slug === 'company-owner') {
        return redirect()
            ->route('company.roles.index')
            ->withErrors([
                'role' => 'The Company Owner automatically has full access.',
            ]);
    }

    $permissions = CompanyPermission::orderBy('module')
        ->orderBy('name')
        ->get()
        ->groupBy('module');

    $selectedPermissions = $role->permissions()
        ->pluck('company_permissions.id')
        ->toArray();

    return view(
        'company.roles.permissions',
        compact(
            'role',
            'permissions',
            'selectedPermissions'
        )
    );
}

public function updatePermissions(
    Request $request,
    CompanyRole $role
) {
    if ($role->slug === 'company-owner') {
        return redirect()
            ->route('company.roles.index')
            ->withErrors([
                'role' => 'The Company Owner automatically has full access.',
            ]);
    }

    $validated = $request->validate([
        'permissions' => [
            'nullable',
            'array',
        ],
        'permissions.*' => [
            'integer',
            'exists:company_permissions,id',
        ],
    ]);

    $role->permissions()->sync(
        $validated['permissions'] ?? []
    );

    return redirect()
        ->route('company.roles.index')
        ->with(
            'success',
            'Permissions updated successfully for ' . $role->name . '.'
        );
}
}