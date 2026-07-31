@extends('company.layouts.app')

@section('title', 'New Shift')

@section('content')

@include('company.hr.shifts.partials.styles')

<div class="shift-header">
    <div>
        <h1>New Shift</h1>
        <p>Schedule a shift for an employee.</p>
    </div>

    <a href="{{ route('company.hr.shifts.index') }}" class="shift-back">
        <i class="fas fa-arrow-left"></i>
        Rota and Shifts
    </a>
</div>

<div class="shift-form-card">
    <form method="POST" action="{{ route('company.hr.shifts.store') }}">
        @csrf

        @include('company.hr.shifts._form')

        <div class="shift-form-actions">
            <button type="submit" class="shift-save">Create Shift</button>
        </div>
    </form>
</div>

@endsection
