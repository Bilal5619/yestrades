@extends('company.layouts.app')

@section('title', 'New Job')

@section('content')

@include('company.hr.jobs.partials.styles')

<div class="jobsheet-header">
    <div>
        <h1>New Job</h1>
    <p>
    Create a Yes Trades job and assign it to a field engineer.
    The engineer will receive the job in the staff app.
</p>
  </div>

    <a href="{{ route('company.hr.jobs.index') }}" class="jobsheet-back">
        <i class="fas fa-arrow-left"></i>
       Jobs
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
