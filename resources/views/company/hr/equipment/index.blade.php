@extends('company.layouts.app')

@section('title', 'Equipment and Vehicles')

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

    .equipment-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 24px; }
    .equipment-stat-card { padding: 18px; border-radius: 10px; background: #1e2a44; }
    .equipment-stat-label { color: #aab4ca; font-size: 12px; margin-bottom: 8px; }
    .equipment-stat-value { font-size: 24px; font-weight: 700; }

    .equipment-card { padding: 20px; border-radius: 10px; background: #1e2a44; margin-bottom: 24px; }
    .equipment-filter-bar { display: flex; align-items: flex-end; gap: 14px; flex-wrap: wrap; margin-bottom: 20px; padding: 18px; border-radius: 10px; background: #1e2a44; }
    .equipment-filter-field { display: flex; flex-direction: column; gap: 6px; }
    .equipment-filter-field label { color: #c9d2e5; font-size: 12px; font-weight: 600; }
    .equipment-filter-field input, .equipment-filter-field select {
        padding: 9px 11px; border: 1px solid rgba(255,255,255,0.12); border-radius: 7px; background: #151f35; color: #ffffff;
    }
    .equipment-filter-submit { min-height: 40px; padding: 0 16px; border: none; border-radius: 7px; background: #2878dc; color: #ffffff; cursor: pointer; }

    .equipment-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px; margin-bottom: 14px; }
    .equipment-form-field { display: flex; flex-direction: column; gap: 6px; }
    .equipment-form-field label { color: #c9d2e5; font-size: 12px; font-weight: 600; }
    .equipment-form-field input, .equipment-form-field select, .equipment-form-field textarea {
        padding: 9px 11px; border: 1px solid rgba(255,255,255,0.12); border-radius: 7px; background: #151f35; color: #ffffff;
    }

    .equipment-table-wrapper { overflow-x: auto; }
    .equipment-table { width: 100%; min-width: 800px; border-collapse: collapse; }
    .equipment-table th, .equipment-table td { padding: 12px 10px; border-bottom: 1px solid rgba(255,255,255,0.07); text-align: left; font-size: 13px; }
    .equipment-table th { color: #aab4ca; font-size: 11px; text-transform: uppercase; }
    .equipment-empty { padding: 25px; text-align: center; color: #aab4ca; }

    .equipment-badge { display: inline-flex; padding: 4px 9px; border-radius: 13px; font-size: 11px; }
    .equipment-badge-available { background: rgba(89,220,150,0.16); color: #59dc96; }
    .equipment-badge-assigned { background: rgba(255,179,71,0.16); color: #ffc46f; }
    .equipment-badge-maintenance { background: rgba(255,99,99,0.16); color: #ff8a92; }
    .equipment-badge-retired { background: rgba(170,180,202,0.14); color: #c4ccdc; }
</style>

<div class="equipment-header">
    <div>
        <h1>Equipment and Vehicles</h1>
        <p>Track company equipment and vehicles, who they're checked out to, and their status.</p>
    </div>
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

<div class="equipment-stats">
    <div class="equipment-stat-card">
        <div class="equipment-stat-label">Total Items</div>
        <div class="equipment-stat-value">{{ $stats['total'] }}</div>
    </div>
    <div class="equipment-stat-card">
        <div class="equipment-stat-label">Assigned</div>
        <div class="equipment-stat-value" style="color:#ffc46f;">{{ $stats['assigned'] }}</div>
    </div>
    <div class="equipment-stat-card">
        <div class="equipment-stat-label">Available</div>
        <div class="equipment-stat-value" style="color:#59dc96;">{{ $stats['available'] }}</div>
    </div>
    <div class="equipment-stat-card">
        <div class="equipment-stat-label">In Maintenance</div>
        <div class="equipment-stat-value" style="color:#ff8a92;">{{ $stats['maintenance'] }}</div>
    </div>
</div>

<div class="equipment-card">
    <h2 style="font-size:16px; margin-bottom:14px;">Add New Equipment / Vehicle</h2>
    <form method="POST" action="{{ route('company.hr.equipment.store') }}">
        @csrf
        <div class="equipment-form-grid">
            <div class="equipment-form-field">
                <label>Name</label>
                <input type="text" name="name" placeholder="e.g. Ford Transit - AB12 CDE" required>
            </div>
            <div class="equipment-form-field">
                <label>Category</label>
                <select name="category" required>
                    @foreach (\App\Models\Company\CompanyEquipment::CATEGORIES as $category)
                        <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="equipment-form-field">
                <label>Reference / Asset No.</label>
                <input type="text" name="reference_number" placeholder="Optional">
            </div>
            <div class="equipment-form-field">
                <label>Registration No. (vehicles)</label>
                <input type="text" name="registration_number" placeholder="Optional">
            </div>
        </div>
        <div class="equipment-form-field" style="margin-bottom:14px;">
            <label>Notes</label>
            <textarea name="notes" rows="2" maxlength="500" placeholder="Optional notes"></textarea>
        </div>
        <button type="submit" class="btn-primary-sm">Add Item</button>
    </form>
</div>

<form method="GET" action="{{ route('company.hr.equipment.index') }}" class="equipment-filter-bar">
    <div class="equipment-filter-field">
        <label>Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name / reference / reg. no.">
    </div>
    <div class="equipment-filter-field">
        <label>Category</label>
        <select name="category">
            <option value="">All categories</option>
            @foreach (\App\Models\Company\CompanyEquipment::CATEGORIES as $category)
                <option value="{{ $category }}" @selected(request('category') == $category)>{{ ucfirst($category) }}</option>
            @endforeach
        </select>
    </div>
    <div class="equipment-filter-field">
        <label>Status</label>
        <select name="status">
            <option value="">All statuses</option>
            @foreach (\App\Models\Company\CompanyEquipment::STATUSES as $status)
                <option value="{{ $status }}" @selected(request('status') == $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="equipment-filter-submit">Filter</button>
</form>

<div class="equipment-card">
    <div class="equipment-table-wrapper">
        <table class="equipment-table">
            <thead>
                <tr>
                    <th>Name</th><th>Category</th><th>Reference</th><th>Reg. No.</th>
                    <th>Status</th><th>Assigned To</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($equipment as $item)
                    <tr>
                        <td><a href="{{ route('company.hr.equipment.show', $item) }}" style="color:#6fa8ee;">{{ $item->name }}</a></td>
                        <td>{{ ucfirst($item->category) }}</td>
                        <td>{{ $item->reference_number ?? '—' }}</td>
                        <td>{{ $item->registration_number ?? '—' }}</td>
                        <td>
                            @if ($item->status === 'available')
                                <span class="equipment-badge equipment-badge-available">Available</span>
                            @elseif ($item->status === 'assigned')
                                <span class="equipment-badge equipment-badge-assigned">Assigned</span>
                            @elseif ($item->status === 'maintenance')
                                <span class="equipment-badge equipment-badge-maintenance">Maintenance</span>
                            @else
                                <span class="equipment-badge equipment-badge-retired">Retired</span>
                            @endif
                        </td>
                        <td>{{ optional($item->currentAssignment->employee ?? null)->full_name ?? '—' }}</td>
                        <td><a href="{{ route('company.hr.equipment.show', $item) }}" style="color:#c9d2e5; font-size:12px;">Manage</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="equipment-empty">No equipment or vehicles recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $equipment->links() }}

@endsection
