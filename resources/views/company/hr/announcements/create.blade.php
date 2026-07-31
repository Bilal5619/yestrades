@extends('company.layouts.app')

@section('title', 'New Announcement')

@section('content')

@include('company.hr.announcements.partials.styles')

<div class="announcement-form-header">
    <div>
        <h1>New Announcement</h1>
        <p>This will appear in the employee app once published.</p>
    </div>

    <a href="{{ route('company.hr.announcements.index') }}" class="announcement-back">
        <i class="fas fa-arrow-left"></i>
        Announcements
    </a>
</div>

<div class="announcement-form-card">
    <form method="POST" action="{{ route('company.hr.announcements.store') }}">
        @csrf

        @include('company.hr.announcements._form')

        <div class="announcement-form-actions">
            <button type="submit" class="announcement-save">Create Announcement</button>
        </div>
    </form>
</div>

@endsection
