<?php

namespace App\Http\Controllers\Admin\Careers;

use App\Http\Controllers\Controller;
use App\Mail\Careers\CandidateStatusMail;
use App\Models\Admin;
use App\Models\Careers\CareerApplication;
use App\Models\Careers\CareerApplicationActivity;
use App\Models\Careers\CareerApplicationNote;
use App\Models\Careers\CareerJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CareerApplicationController extends Controller
{
  public function index(Request $request)
  {
    $applications = CareerApplication::with(['job', 'assignee'])
      ->when($request->type === 'general', fn ($query) => $query->where('application_type', 'general'))
      ->when($request->search, function ($query, $search) {
        $query->where(function ($query) use ($search) {
          $query->where('reference_number', 'like', '%' . $search . '%')
            ->orWhere('first_name', 'like', '%' . $search . '%')
            ->orWhere('last_name', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%')
            ->orWhere('phone', 'like', '%' . $search . '%');
        });
      })
      ->when($request->job_id, fn ($query, $jobId) => $query->where('career_job_id', $jobId))
      ->when($request->department, fn ($query, $department) => $query->whereHas('job', fn ($jobQuery) => $jobQuery->where('department', $department)))
      ->when($request->application_type, fn ($query, $type) => $query->where('application_type', $type))
      ->when($request->status, fn ($query, $status) => $query->where('status', $status))
      ->when($request->right_to_work, fn ($query, $value) => $query->where('right_to_work', $value))
      ->when($request->sponsorship_required, fn ($query, $value) => $query->where('sponsorship_required', $value))
      ->when($request->assigned_to, fn ($query, $adminId) => $query->where('assigned_to', $adminId))
      ->when($request->date_from, fn ($query, $date) => $query->whereDate('submitted_at', '>=', $date))
      ->when($request->date_to, fn ($query, $date) => $query->whereDate('submitted_at', '<=', $date))
      ->latest('submitted_at')
      ->paginate(25)
      ->appends($request->query());

    $jobs = CareerJob::orderBy('title')->get(['id', 'title', 'department']);
    $admins = Admin::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
    $departments = CareerJob::distinct()->orderBy('department')->pluck('department')->filter();

    return view('admin.careers.applications.index', compact('applications', 'jobs', 'admins', 'departments'));
  }

  public function general(Request $request)
  {
    $request->merge(['type' => 'general']);
    return $this->index($request);
  }

  public function show(CareerApplication $application)
  {
    $application->load(['job', 'answers', 'activities.admin', 'notes.admin', 'assignee']);
    $admins = Admin::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
    $previous = CareerApplication::where('id', '<', $application->id)->orderByDesc('id')->first();
    $next = CareerApplication::where('id', '>', $application->id)->orderBy('id')->first();

    CareerApplicationActivity::firstOrCreate([
      'career_application_id' => $application->id,
      'admin_id' => auth('admin')->id(),
      'type' => 'viewed',
      'created_at' => now(),
    ], ['note' => 'Application viewed.']);

    return view('admin.careers.applications.show', compact('application', 'admins', 'previous', 'next'));
  }

  public function updateStatus(Request $request, CareerApplication $application)
  {
    $data = $request->validate([
      'status' => ['required', Rule::in(CareerApplication::STATUSES)],
      'note' => ['nullable', 'string', 'max:2000'],
    ]);

    $previous = $application->status;
    $timestamps = [];
    if ($data['status'] === 'reviewing' && !$application->reviewed_at) $timestamps['reviewed_at'] = now();
    if ($data['status'] === 'rejected') $timestamps['rejected_at'] = now();
    if ($data['status'] === 'hired') $timestamps['hired_at'] = now();

    $application->update(array_merge(['status' => $data['status']], $timestamps));
    $this->activity($application, 'status_changed', $data['note'] ?? null, $previous, $data['status']);

    return back()->with('success', __('Application status updated.'));
  }

  public function assign(Request $request, CareerApplication $application)
  {
    $data = $request->validate(['assigned_to' => ['nullable', 'exists:admins,id']]);
    $application->update(['assigned_to' => $data['assigned_to'] ?? null]);
    $this->activity($application, 'assigned', 'Application assignment changed.');

    return back()->with('success', __('Application assigned.'));
  }

  public function addNote(Request $request, CareerApplication $application)
  {
    $data = $request->validate(['note' => ['required', 'string', 'max:5000']]);
    CareerApplicationNote::create([
      'career_application_id' => $application->id,
      'admin_id' => auth('admin')->id(),
      'note' => $data['note'],
    ]);
    $this->activity($application, 'note_added', 'Private note added.');

    return back()->with('success', __('Note added.'));
  }

  public function downloadCv(CareerApplication $application)
  {
    return $this->download($application, 'cv');
  }

  public function downloadCoverLetter(CareerApplication $application)
  {
    return $this->download($application, 'cover_letter');
  }

  public function sendEmail(Request $request, CareerApplication $application)
  {
    $data = $request->validate([
      'subject' => ['required', 'string', 'max:190'],
      'message' => ['required', 'string', 'max:10000'],
    ]);

    Mail::to($application->email)->send(new CandidateStatusMail($application, $data['subject'], $data['message']));
    $this->activity($application, 'email_sent', 'Email sent: ' . $data['subject']);

    return back()->with('success', __('Email sent to candidate.'));
  }

  public function destroy(CareerApplication $application)
  {
    $application->delete();
    return redirect()->route('admin.careers.applications.index')->with('success', __('Application deleted safely.'));
  }

  private function download(CareerApplication $application, string $type)
  {
    $path = $type === 'cv' ? $application->cv_path : $application->cover_letter_path;
    $name = $type === 'cv' ? $application->cv_original_name : $application->cover_letter_original_name;

    abort_if(empty($path) || str_contains($path, '..') || !Storage::disk('local')->exists($path), 404);

    $this->activity($application, $type === 'cv' ? 'cv_downloaded' : 'cover_letter_downloaded', ucfirst(str_replace('_', ' ', $type)) . ' downloaded.');

    return Storage::disk('local')->download($path, $name ?: basename($path));
  }

  private function activity(CareerApplication $application, string $type, ?string $note = null, ?string $previous = null, ?string $new = null): void
  {
    CareerApplicationActivity::create([
      'career_application_id' => $application->id,
      'admin_id' => auth('admin')->id(),
      'type' => $type,
      'previous_status' => $previous,
      'new_status' => $new,
      'note' => $note,
    ]);
  }
}
