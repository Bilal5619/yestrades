@extends('company.layouts.app')

@section('title', 'Edit Announcement')

@section('content')

@include('company.hr.announcements.partials.styles')

<div class="announcement-form-header">
    <div>
        <h1>Edit Announcement</h1>
        <p>Update the announcement details.</p>
    </div>

    <a href="{{ route('company.hr.announcements.index') }}" class="announcement-back">
        <i class="fas fa-arrow-left"></i>
        Announcements
    </a>
</div>

<div class="announcement-form-card">
    <form method="POST" action="{{ route('company.hr.announcements.update', $announcement) }}">
        @csrf
        @method('PUT')

        @include('company.hr.announcements._form')

        <div class="announcement-form-actions">
            <button type="submit" class="announcement-save">Save Changes</button>
        </div>
    </form>
</div>

@endsection
