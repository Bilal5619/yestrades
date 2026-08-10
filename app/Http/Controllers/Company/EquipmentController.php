<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\CompanyEquipment;
use App\Models\Company\Employee;
use App\Models\Company\EquipmentAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = CompanyEquipment::query()->with('currentAssignment.employee');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($equipmentQuery) use ($search) {
                $equipmentQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $equipment = $query->orderBy('name')->paginate(20)->withQueryString();

        $stats = [
            'total' => CompanyEquipment::query()->count(),
            'assigned' => CompanyEquipment::query()->where('status', 'assigned')->count(),
            'available' => CompanyEquipment::query()->where('status', 'available')->count(),
            'maintenance' => CompanyEquipment::query()->where('status', 'maintenance')->count(),
        ];

        $employees = Employee::query()
            ->where('employment_status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('company.hr.equipment.index', compact('equipment', 'stats', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:' . implode(',', CompanyEquipment::CATEGORIES)],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['status'] = 'available';
        $validated['created_by'] = Auth::guard('company')->id();

        CompanyEquipment::create($validated);

        return back()->with('success', 'Equipment added successfully.');
    }

    public function show(CompanyEquipment $equipmentItem)
    {
        $equipmentItem->load(['assignments' => function ($query) {
            $query->with(['employee', 'assignedBy', 'returnedTo'])->latest('assigned_at');
        }]);

        $employees = Employee::query()
            ->where('employment_status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('company.hr.equipment.show', [
            'equipmentItem' => $equipmentItem,
            'employees' => $employees,
        ]);
    }

    public function assign(Request $request, CompanyEquipment $equipmentItem)
    {
        if ($equipmentItem->status === 'assigned') {
            return back()->withErrors(['equipment' => 'This item is already assigned. Return it before reassigning.']);
        }

        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        EquipmentAssignment::create([
            'equipment_id' => $equipmentItem->id,
            'employee_id' => $validated['employee_id'],
            'assigned_at' => now(),
            'notes' => $validated['notes'] ?? null,
            'assigned_by' => Auth::guard('company')->id(),
        ]);

        $equipmentItem->update(['status' => 'assigned']);

        return back()->with('success', 'Equipment assigned successfully.');
    }

    public function returnItem(Request $request, CompanyEquipment $equipmentItem)
    {
        $assignment = $equipmentItem->assignments()->whereNull('returned_at')->latest('assigned_at')->first();

        if (!$assignment) {
            return back()->withErrors(['equipment' => 'This item does not have an active assignment.']);
        }

        $validated = $request->validate([
            'condition_on_return' => ['nullable', 'string', 'max:255'],
        ]);

        $assignment->update([
            'returned_at' => now(),
            'condition_on_return' => $validated['condition_on_return'] ?? null,
            'returned_to' => Auth::guard('company')->id(),
        ]);

        $equipmentItem->update([
            'status' => $request->input('send_to_maintenance') ? 'maintenance' : 'available',
        ]);

        return back()->with('success', 'Equipment returned successfully.');
    }

    public function update(Request $request, CompanyEquipment $equipmentItem)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:' . implode(',', CompanyEquipment::CATEGORIES)],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:' . implode(',', CompanyEquipment::STATUSES)],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['updated_by'] = Auth::guard('company')->id();

        $equipmentItem->update($validated);

        return back()->with('success', 'Equipment updated successfully.');
    }
}
