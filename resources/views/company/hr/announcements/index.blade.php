@extends('company.layouts.app')

@section('title', 'Announcements')

@section('content')

<style>
    .announcement-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .announcement-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .announcement-header p {
        margin: 0;
        color: #aab4ca;
    }

    .announcement-add {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
    }

    .announcement-card {
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .announcement-table-wrapper {
        overflow-x: auto;
    }

    .announcement-table {
        width: 100%;
        min-width: 800px;
        border-collapse: collapse;
    }

    .announcement-table th,
    .announcement-table td {
        padding: 13px 10px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
    }

    .announcement-table th {
        color: #aab4ca;
        font-size: 12px;
    }

    .announcement-badge {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 13px;
        font-size: 12px;
    }

    .announcement-badge-urgent {
        background: rgba(255,99,99,0.16);
        color: #ff8a92;
    }

    .announcement-badge-draft {
        background: rgba(170,180,202,0.14);
        color: #c4ccdc;
    }

    .announcement-badge-published {
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .announcement-actions a {
        display: inline-flex;
        padding: 7px 11px;
        border-radius: 6px;
        color: #ffffff;
        text-decoration: none;
        margin-right: 6px;
    }

    .announcement-edit {
        background: #2878dc;
    }

    .announcement-delete {
        background: #d24b4b;
        border: none;
        cursor: pointer;
        font-size: 12px;
    }

    .announcement-empty {
        padding: 35px;
        text-align: center;
        color: #aab4ca;
    }
</style>

<div class="announcement-header">
    <div>
        <h1>Announcements</h1>
        <p>Create company announcements employees will see in the app and mark read.</p>
    </div>

    @if (auth('company')->user()->hasPermission('announcements.manage'))
        <a href="{{ route('company.hr.announcements.create') }}" class="announcement-add">
            <i class="fas fa-plus"></i>
            New Announcement
        </a>
    @endif
</div>

<div class="announcement-card">
    <div class="announcement-table-wrapper">
        <table class="announcement-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Published</th>
                    <th>Read By</th>
                    <th>Created By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($announcements as $announcement)
                    <tr>
                        <td>
                            <a href="{{ route('company.hr.announcements.show', $announcement) }}" style="color:#ffffff;">
                                {{ $announcement->title }}
                            </a>
                            @if ($announcement->is_urgent)
                                <span class="announcement-badge announcement-badge-urgent">Urgent</span>
                            @endif
                        </td>
                        <td>
                            <span class="announcement-badge {{ $announcement->isPublished() ? 'announcement-badge-published' : 'announcement-badge-draft' }}">
                                {{ $announcement->isPublished() ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td>{{ optional($announcement->published_at)->format('d M Y H:i') ?? '—' }}</td>
                        <td>{{ $announcement->reads_count }}</td>
                        <td>{{ optional($announcement->createdBy)->full_name ?? '—' }}</td>
                        <td class="announcement-actions">
                            @if (auth('company')->user()->hasPermission('announcements.manage'))
                                <a href="{{ route('company.hr.announcements.edit', $announcement) }}" class="announcement-edit">Edit</a>
                                <form method="POST" action="{{ route('company.hr.announcements.destroy', $announcement) }}" style="display:inline;" onsubmit="return confirm('Delete this announcement?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="announcement-delete">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="announcement-empty">No announcements yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $announcements->links() }}
</div>

@endsection
