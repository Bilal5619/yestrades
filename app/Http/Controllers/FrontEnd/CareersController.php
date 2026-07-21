<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Http\Requests\Careers\StoreCareerApplicationRequest;
use App\Http\Requests\Careers\StoreGeneralCvRequest;
use App\Mail\Careers\AdminApplicationNotificationMail;
use App\Mail\Careers\ApplicantConfirmationMail;
use App\Models\Careers\CareerApplication;
use App\Models\Careers\CareerApplicationActivity;
use App\Models\Careers\CareerJob;
use App\Models\Careers\CareerSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CareersController extends Controller
{
  public function index(Request $request)
  {
    $jobsQuery = CareerJob::active()
      ->withCount('applications')
      ->orderByDesc('featured')
      ->orderBy('display_order')
      ->orderByDesc('published_at');

    $filters = $request->only(['search', 'department', 'employment_type', 'workplace_type', 'location']);

    $jobsQuery->when($filters['search'] ?? null, function ($query, $search) {
      $query->where(function ($query) use ($search) {
        $query->where('title', 'like', '%' . $search . '%')
          ->orWhere('summary', 'like', '%' . $search . '%')
          ->orWhere('department', 'like', '%' . $search . '%');
      });
    });

    foreach (['department', 'employment_type', 'workplace_type', 'location'] as $filter) {
      $jobsQuery->when($filters[$filter] ?? null, fn ($query, $value) => $query->where($filter, $value));
    }

    $jobs = $jobsQuery->paginate(9)->appends($request->query());
    $activeJobs = CareerJob::active();
    $filterOptions = [
      'departments' => (clone $activeJobs)->distinct()->orderBy('department')->pluck('department')->filter(),
      'employmentTypes' => CareerJob::EMPLOYMENT_TYPES,
      'workplaceTypes' => CareerJob::WORKPLACE_TYPES,
      'locations' => CareerJob::active()->distinct()->orderBy('location')->pluck('location')->filter(),
    ];

    return view('frontend.careers.index', compact('jobs', 'filters', 'filterOptions'));
  }

  public function show(string $slug)
  {
    $job = CareerJob::active()->with('questions')->where('slug', $slug)->firstOrFail();
    return view('frontend.careers.show', compact('job'));
  }

  public function store(StoreCareerApplicationRequest $request, CareerJob $job)
  {
    abort_unless($job->is_open, 404);

    $application = $this->createApplication($request, $job, 'job');

    foreach ($job->questions as $question) {
      $answer = $request->input('questions.' . $question->id);
      if ($answer === null || $answer === '') {
        continue;
      }

      $application->answers()->create([
        'career_job_question_id' => $question->id,
        'question_snapshot' => $question->question,
        'answer_text' => is_array($answer) ? null : (string) $answer,
        'answer_json' => is_array($answer) ? array_values($answer) : null,
      ]);
    }

    $this->sendNotifications($application);

    return redirect()->route('careers.success', $application->reference_number);
  }

  public function createGeneral()
  {
    return view('frontend.careers.general');
  }

  public function storeGeneral(StoreGeneralCvRequest $request)
  {
    $application = $this->createApplication($request, null, 'general');
    $this->sendNotifications($application);

    return redirect()->route('careers.success', $application->reference_number);
  }

  public function success(string $reference)
  {
    $application = CareerApplication::where('reference_number', $reference)->firstOrFail();
    return view('frontend.careers.success', compact('application'));
  }

  private function createApplication(Request $request, ?CareerJob $job, string $type): CareerApplication
  {
    $cv = $this->storePrivateDocument($request->file('cv'));
    $coverLetter = $request->hasFile('cover_letter_file') ? $this->storePrivateDocument($request->file('cover_letter_file')) : null;

    $application = CareerApplication::create([
      'reference_number' => CareerApplication::makeReferenceNumber(),
      'career_job_id' => optional($job)->id,
      'job_title_snapshot' => optional($job)->title,
      'application_type' => $type,
      'first_name' => $request->first_name,
      'last_name' => $request->last_name,
      'email' => $request->email,
      'phone' => $request->phone,
      'city' => $request->city,
      'postcode' => $request->postcode,
      'right_to_work' => $request->right_to_work,
      'sponsorship_required' => $request->sponsorship_required,
      'linkedin_url' => $request->linkedin_url,
      'portfolio_url' => $request->portfolio_url,
      'cover_letter_text' => $request->cover_letter_text,
      'earliest_start_date' => $request->earliest_start_date,
      'expected_salary' => $request->expected_salary,
      'additional_information' => $request->additional_information,
      'areas_of_interest' => $request->areas_of_interest,
      'preferred_employment_type' => $request->preferred_employment_type,
      'cv_path' => $cv['path'],
      'cv_original_name' => $cv['original_name'],
      'cv_mime_type' => $cv['mime_type'],
      'cv_size' => $cv['size'],
      'cover_letter_path' => $coverLetter['path'] ?? null,
      'cover_letter_original_name' => $coverLetter['original_name'] ?? null,
      'cover_letter_mime_type' => $coverLetter['mime_type'] ?? null,
      'cover_letter_size' => $coverLetter['size'] ?? null,
      'status' => 'new',
      'source' => 'careers_page',
      'submitted_at' => now(),
      'consent_accepted' => true,
      'consent_accepted_at' => now(),
      'privacy_policy_reference' => route('privacy_policy'),
      'ip_address' => $request->ip(),
      'user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
    ]);

    CareerApplicationActivity::create([
      'career_application_id' => $application->id,
      'type' => 'submitted',
      'new_status' => 'new',
      'note' => $type === 'general' ? 'General CV submission received.' : 'Job application submitted.',
    ]);

    return $application;
  }

  private function storePrivateDocument($file): array
  {
    $extension = strtolower($file->getClientOriginalExtension());
    $filename = (string) Str::uuid() . '.' . $extension;
    $path = $file->storeAs('career-documents/' . now()->format('Y/m'), $filename, 'local');

    return [
      'path' => $path,
      'original_name' => $file->getClientOriginalName(),
      'mime_type' => $file->getMimeType(),
      'size' => $file->getSize(),
    ];
  }

  private function sendNotifications(CareerApplication $application): void
  {
    Mail::to($application->email)->send(new ApplicantConfirmationMail($application));

    $recipient = optional($application->job)->application_email
      ?: CareerSetting::valueFor('notification_email')
      ?: config('mail.from.address');

    if ($recipient) {
      Mail::to($recipient)->send(new AdminApplicationNotificationMail($application));
    }
  }
}
