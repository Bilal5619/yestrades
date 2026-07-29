<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\CompanyRole;
use App\Models\Company\CompanyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CompanyUserController extends Controller
{
    public function index()
    {
        $users = CompanyUser::with('companyRole')
            ->latest()
            ->paginate(15);

        return view('company.users.index', compact('users'));
    }

    public function create()
    {
        $roles = CompanyRole::where('status', true)
            ->orderBy('name')
            ->get();

        return view('company.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],
            'last_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:company_users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'role_id' => [
                'required',
                'exists:company_roles,id',
            ],
            'status' => [
                'required',
                'boolean',
            ],
        ]);

        CompanyUser::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'role' => 'company_user',
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('company.users.index')
            ->with('success', 'Company user created successfully.');
    }

    public function toggleStatus(CompanyUser $user)
    {
        $loggedInUser = auth('company')->user();

        if ($loggedInUser->id === $user->id) {
            return back()->withErrors([
                'status' => 'You cannot disable your own account.',
            ]);
        }

        $user->update([
            'status' => !$user->status,
        ]);

        return back()->with(
            'success',
            'User status updated successfully.'
        );
    }
    public function edit(CompanyUser $user)
{
    $roles = CompanyRole::where('status', true)
        ->orderBy('name')
        ->get();

    return view(
        'company.users.edit',
        compact('user', 'roles')
    );
}

public function update(Request $request, CompanyUser $user)
{
    $loggedInUser = auth('company')->user();

    $validated = $request->validate([
        'first_name' => [
            'required',
            'string',
            'max:100',
        ],
        'last_name' => [
            'nullable',
            'string',
            'max:100',
        ],
        'email' => [
            'required',
            'email',
            'max:255',
            'unique:company_users,email,' . $user->id,
        ],
        'role_id' => [
            'required',
            'exists:company_roles,id',
        ],
        'status' => [
            'required',
            'boolean',
        ],
        'password' => [
            'nullable',
            'string',
            'min:8',
            'confirmed',
        ],
    ]);

    if (
        $loggedInUser->id === $user->id &&
        (int) $validated['status'] === 0
    ) {
        return back()
            ->withInput()
            ->withErrors([
                'status' => 'You cannot disable your own account.',
            ]);
    }

    $user->first_name = $validated['first_name'];
    $user->last_name = $validated['last_name'] ?? null;
    $user->email = $validated['email'];
    $user->role_id = $validated['role_id'];
    $user->status = $validated['status'];

    if (!empty($validated['password'])) {
        $user->password = Hash::make($validated['password']);
    }

    $user->save();

    return redirect()
        ->route('company.users.index')
        ->with('success', 'Company user updated successfully.');
}
}