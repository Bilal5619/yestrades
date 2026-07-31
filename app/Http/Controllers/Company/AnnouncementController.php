<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::query()
            ->with(['createdBy'])
            ->withCount('reads')
            ->orderByDesc('is_urgent')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(15);

        return view(
            'company.hr.announcements.index',
            compact('announcements')
        );
    }

    public function create()
    {
        return view('company.hr.announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateAnnouncement($request);

        $validated['is_urgent'] = $request->boolean('is_urgent');
        $validated['created_by'] = auth('company')->id();
        $validated['updated_by'] = auth('company')->id();

        Announcement::create($validated);

        return redirect()
            ->route('company.hr.announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    public function show(Announcement $announcement)
    {
        $announcement->load(['createdBy', 'reads.employee']);

        return view(
            'company.hr.announcements.show',
            compact('announcement')
        );
    }

    public function edit(Announcement $announcement)
    {
        return view(
            'company.hr.announcements.edit',
            compact('announcement')
        );
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $this->validateAnnouncement($request);

        $validated['is_urgent'] = $request->boolean('is_urgent');
        $validated['updated_by'] = auth('company')->id();

        $announcement->update($validated);

        return redirect()
            ->route('company.hr.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()
            ->route('company.hr.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }

    private function validateAnnouncement(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
        ]);
    }
}
