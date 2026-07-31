@extends('company.layouts.app')

@section('title', 'New Job')

@section('content')

@include('company.hr.jobs.partials.styles')

<div class="jobsheet-header">
    <div>
        <h1>New Job</h1>
        <p>Assign a job to an employee. It appears in their weekly job sheet in the app.</p>
    </div>

    <a href="{{ route('company.hr.jobs.index') }}" class="jobsheet-back">
        <i class="fas fa-arrow-left"></i>
        Job Sheet
    </a>
</div>

<div class="jobsheet-form-card">
    <form method="POST" action="{{ route('company.hr.jobs.store') }}">
        @csrf

        @include('company.hr.jobs._form')

        <div class="jobsheet-form-actions">
            <button type="submit" class="jobsheet-save">Create Job</button>
        </div>
    </form>
</div>

@endsection
