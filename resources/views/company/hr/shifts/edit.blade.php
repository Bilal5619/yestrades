@extends('company.layouts.app')

@section('title', 'Edit Shift')

@section('content')

@include('company.hr.shifts.partials.styles')

<div class="shift-header">
    <div>
        <h1>Edit Shift</h1>
        <p>Update shift details.</p>
    </div>

    <a href="{{ route('company.hr.shifts.index') }}" class="shift-back">
        <i class="fas fa-arrow-left"></i>
        Rota and Shifts
    </a>
</div>

<div class="shift-form-card">
    <form method="POST" action="{{ route('company.hr.shifts.update', $shift) }}">
        @csrf
        @method('PUT')

        @include('company.hr.shifts._form')

        <div class="shift-form-actions" style="justify-content: space-between;">
            <button type="submit" form="shift-delete-form" class="shift-delete">Delete Shift</button>
            <button type="submit" class="shift-save">Save Changes</button>
        </div>
    </form>

    <form id="shift-delete-form" method="POST" action="{{ route('company.hr.shifts.destroy', $shift) }}" onsubmit="return confirm('Delete this shift?');" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
</div>

@endsection
