@extends('company.layouts.app')

@section('title', 'Edit Job')

@section('content')

@include('company.hr.jobs.partials.styles')

<div class="jobsheet-header">
    <div>
        <h1>Edit Job</h1>
        <p>Update job details or status.</p>
    </div>

    <a href="{{ route('company.hr.jobs.index') }}" class="jobsheet-back">
        <i class="fas fa-arrow-left"></i>
        Job Sheet
    </a>
</div>

<div class="jobsheet-form-card">
    <form method="POST" action="{{ route('company.hr.jobs.update', $job) }}">
        @csrf
        @method('PUT')

        @include('company.hr.jobs._form')

        <div class="jobsheet-form-actions" style="justify-content: space-between;">
            <button type="submit" form="jobsheet-delete-form" class="jobsheet-delete">Delete Job</button>
            <button type="submit" class="jobsheet-save">Save Changes</button>
        </div>
    </form>

    <form id="jobsheet-delete-form" method="POST" action="{{ route('company.hr.jobs.destroy', $job) }}" onsubmit="return confirm('Delete this job?');" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
</div>

@endsection
