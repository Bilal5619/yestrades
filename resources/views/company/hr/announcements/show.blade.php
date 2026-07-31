@extends('company.layouts.app')

@section('title', $announcement->title)

@section('content')

@include('company.hr.announcements.partials.styles')

<style>
    .announcement-body {
        white-space: pre-line;
        line-height: 1.7;
        color: #dfe4f0;
    }

    .announcement-meta {
        color: #aab4ca;
        font-size: 13px;
        margin-bottom: 20px;
    }

    .announcement-read-list {
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid rgba(255,255,255,0.08);
    }

    .announcement-read-list h3 {
        font-size: 15px;
        margin-bottom: 12px;
    }

    .announcement-read-item {
        padding: 8px 0;
        color: #c9d2e5;
        font-size: 13px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
</style>

<div class="announcement-form-header">
    <div>
        <h1>{{ $announcement->title }}</h1>
        <p class="announcement-meta">
            {{ $announcement->isPublished() ? 'Published ' . $announcement->published_at->format('d M Y H:i') : 'Draft' }}
            &middot; Created by {{ optional($announcement->createdBy)->full_name ?? 'Unknown' }}
        </p>
    </div>

    <a href="{{ route('company.hr.announcements.index') }}" class="announcement-back">
        <i class="fas fa-arrow-left"></i>
        Announcements
    </a>
</div>

<div class="announcement-form-card">
    <div class="announcement-body">{{ $announcement->body }}</div>

    <div class="announcement-read-list">
        <h3>Read by ({{ $announcement->reads->count() }})</h3>

        @forelse ($announcement->reads as $read)
            <div class="announcement-read-item">
                {{ optional($read->employee)->full_name ?? 'Unknown employee' }} — {{ $read->read_at->format('d M Y H:i') }}
            </div>
        @empty
            <div class="announcement-read-item">No one has read this yet.</div>
        @endforelse
    </div>
</div>

@endsection
