@extends('company.layouts.app')

@section('title', $equipmentItem->name)

@section('content')

<style>
    .equipment-header { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px; margin-bottom: 24px; }
    .equipment-header h1 { margin: 0 0 7px; font-size: 28px; }
    .equipment-header p { margin: 0; color: #aab4ca; }
    .btn-primary-sm, .btn-outline-sm {
        display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 7px;
        font-size: 13px; text-decoration: none; cursor: pointer; border: none;
    }
    .btn-primary-sm { background: #2878dc; color: #fff; }
    .btn-outline-sm { background: transparent; color: #c9d2e5; border: 1px solid rgba(255,255,255,0.16); }

    .equipment-card { padding: 20px; border-radius: 10px; background: #1e2a44; margin-bottom: 24px; }
    .equipment-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px; margin-bottom: 14px; }
    .equipment-form-field { display: flex; flex-direction: column; gap: 6px; }
    .equipment-form-field label { color: #c9d2e5; font-size: 12px; font-weight: 600; }
    .equipment-form-field input, .equipment-form-field select, .equipment-form-field textarea {
        padding: 9px 11px; border: 1px solid rgba(255,255,255,0.12); border-radius: 7px; background: #151f35; color: #ffffff;
    }
    .equipment-table-wrapper { overflow-x: auto; }
    .equipment-table { width: 100%; min-width: 700px; border-collapse: collapse; }
    .equipment-table th, .equipment-table td { padding: 12px 10px; border-bottom: 1px solid rgba(255,255,255,0.07); text-align: left; font-size: 13px; }
    .equipment-table th { color: #aab4ca; font-size: 11px; text-transform: uppercase; }
    .equipment-empty { padding: 25px; text-align: center; color: #aab4ca; }
    .equipment-badge { display: inline-flex; padding: 4px 9px; border-radius: 13px; font-size: 11px; }
    .equipment-badge-available { background: rgba(89,220,150,0.16); color: #59dc96; }
    .equipment-badge-assigned { background: rgba(255,179,71,0.16); color: #ffc46f; }
    .equipment-badge-maintenance { background: rgba(255,99,99,0.16); color: #ff8a92; }
    .equipment-badge-retired { background: rgba(170,180,202,0.14); color: #c4ccdc; }
    .equipment-meta-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; }
    .equipment-meta-label { color: #aab4ca; font-size: 12px; margin-bottom: 4px; }
    .equipment-meta-value { font-size: 15px; font-weight: 600; }
</style>

<div class="equipment-header">
    <div>
        <h1>{{ $equipmentItem->name }}</h1>
        <p>{{ ucfirst($equipmentItem->category) }}{{ $equipmentItem->registration_number ? ' · ' . $equipmentItem->registration_number : '' }}</p>
    </div>
    <a href="{{ route('company.hr.equipment.index') }}" class="btn-outline-sm">
        <i class="fas fa-arrow-left"></i> All Equipment
    </a>
</div>

@if (session('success'))
    <div class="equipment-card" style="background:rgba(89,220,150,0.1); border:1px solid rgba(89,220,150,0.3);">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="equipment-card" style="background:rgba(255,99,99,0.1); border:1px solid rgba(255,99,99,0.3);">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="equipment-card">
    <div class="equipment-meta-grid">
        <div>
            <div class="equipment-meta-label">Status</div>
            <div class="equipment-meta-value">
                @if ($equipmentItem->status === 'available')
                    <span class="equipment-badge equipment-badge-available">Available</span>
                @elseif ($equipmentItem->status === 'assigned')
                    <span class="equipment-badge equipment-badge-assigned">Assigned</span>
                @elseif ($equipmentItem->status === 'maintenance')
                    <span class="equipment-badge equipment-badge-maintenance">Maintenance</span>
                @else
                    <span class="equipment-badge equipment-badge-retired">Retired</span>
                @endif
            </div>
        </div>
        <div>
            <div class="equipment-meta-label">Reference</div>
            <div class="equipment-meta-value">{{ $equipmentItem->reference_number ?? '—' }}</div>
        </div>
        <div>
            <div class="equipment-meta-label">Registration</div>
            <div class="equipment-meta-value">{{ $equipmentItem->registration_number ?? '—' }}</div>
        </div>
        <div>
            <div class="equipment-meta-label">Notes</div>
            <div class="equipment-meta-value" style="font-weight:400;">{{ $equipmentItem->notes ?? '—' }}</div>
        </div>
    </div>
</div>

@php
    $activeAssignment = $equipmentItem->assignments->firstWhere('returned_at', null);
@endphp

@if ($activeAssignment)
    <div class="equipment-card">
        <h2 style="font-size:16px; margin-bottom:14px;">Currently Assigned</h2>
        <p style="margin-bottom:14px;">
            Checked out to <strong>{{ optional($activeAssignment->employee)->full_name ?? 'Unknown' }}</strong>
            since {{ $activeAssignment->assigned_at->format('d-m-Y H:i') }}.
        </p>
        <form method="POST" action="{{ route('company.hr.equipment.return', $equipmentItem) }}">
            @csrf
            <div class="equipment-form-grid">
                <div class="equipment-form-field">
                    <label>Condition on Return</label>
                    <input type="text" name="condition_on_return" placeholder="e.g. Good, minor scratches">
                </div>
                <div class="equipment-form-field">
                    <label>
                        <input type="checkbox" name="send_to_maintenance" value="1" style="width:auto;">
                        Send to maintenance instead of Available
                    </label>
                </div>
            </div>
            <button type="submit" class="btn-primary-sm">Return / Check In</button>
        </form>
    </div>
@else
    <div class="equipment-card">
        <h2 style="font-size:16px; margin-bottom:14px;">Assign to Employee</h2>
        <form method="POST" action="{{ route('company.hr.equipment.assign', $equipmentItem) }}">
            @csrf
            <div class="equipment-form-grid">
                <div class="equipment-form-field">
                    <label>Employee</label>
                    <select name="employee_id" required>
                        <option value="">Select employee</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="equipment-form-field">
                    <label>Notes</label>
                    <input type="text" name="notes" placeholder="Optional">
                </div>
            </div>
            <button type="submit" class="btn-primary-sm">Assign</button>
        </form>
    </div>
@endif

<div class="equipment-card">
    <h2 style="font-size:16px; margin-bottom:14px;">Assignment History</h2>
    <div class="equipment-table-wrapper">
        <table class="equipment-table">
            <thead>
                <tr><th>Employee</th><th>Assigned</th><th>Returned</th><th>Condition</th><th>Assigned By</th></tr>
            </thead>
            <tbody>
                @forelse ($equipmentItem->assignments as $assignment)
                    <tr>
                        <td>{{ optional($assignment->employee)->full_name ?? 'Unknown' }}</td>
                        <td>{{ $assignment->assigned_at->format('d-m-Y H:i') }}</td>
                        <td>{{ $assignment->returned_at ? $assignment->returned_at->format('d-m-Y H:i') : '—' }}</td>
                        <td>{{ $assignment->condition_on_return ?? '—' }}</td>
                        <td>{{ optional($assignment->assignedBy)->full_name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="equipment-empty">No assignment history yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
