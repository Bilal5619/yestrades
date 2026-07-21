<?php

namespace App\Http\Controllers\Admin\Careers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Careers\StoreCareerJobRequest;
use App\Models\Careers\CareerApplication;
use App\Models\Careers\CareerJob;
use App\Models\Careers\CareerJobQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CareerJobController extends Controller
{
  public function dashboard()
  {
    $stats = [
      'published_jobs' => CareerJob::where('status', 'published')->count(),
      'draft_jobs' => CareerJob::where('status', 'draft')->count(),
      'new_applications' => CareerApplication::where('status', 'new')->count(),
      'reviewing' => CareerApplication::where('status', 'reviewing')->count(),
      'shortlisted' => CareerApplication::where('status', 'shortlisted')->count(),
      'interviews' => CareerApplication::where('status', 'interview')->count(),
      'hired' => CareerApplication::where('status', 'hired')->count(),
      'general_cvs' => CareerApplication::where('application_type', 'general')->count(),
    ];

    $recentApplications = CareerApplication::with('job')->latest('submitted_at')->limit(8)->get();
    $closingSoon = CareerJob::active()->whereNotNull('closing_date')->orderBy('closing_date')->limit(8)->get();
    $applicationsByJob = CareerJob::withCount('applications')->having('applications_count', '>', 0)->orderByDesc('applications_count')->limit(8)->get();
    $statusBreakdown = CareerApplication::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

    return view('admin.careers.dashboard', compact('stats', 'recentApplications', 'closingSoon', 'applicationsByJob', 'statusBreakdown'));
  }

  public function index(Request $request)
  {
    $jobs = CareerJob::withCount('applications')
      ->when($request->search, fn ($query, $search) => $query->where('title', 'like', '%' . $search . '%')->orWhere('department', 'like', '%' . $search . '%'))
      ->when($request->status, fn ($query, $status) => $query->where('status', $status))
      ->orderBy('display_order')
      ->orderByDesc('id')
      ->paginate(20)
      ->appends($request->query());

    return view('admin.careers.jobs.index', compact('jobs'));
  }

  public function create()
  {
    $job = new CareerJob(['status' => 'draft', 'display_order' => 0]);
    return view('admin.careers.jobs.form', compact('job'));
  }

  public function store(StoreCareerJobRequest $request)
  {
    $data = $this->jobData($request);
    $data['created_by'] = auth('admin')->id();
    $data['updated_by'] = auth('admin')->id();
    if ($data['status'] === 'published') {
      $data['published_at'] = now();
    }

    $job = CareerJob::create($data);
    $this->syncQuestions($job, $request->input('questions', []));

    return redirect()->route('admin.careers.jobs.index')->with('success', __('Job created successfully.'));
  }

  public function show(CareerJob $job)
  {
    return redirect()->route('careers.show', $job->slug);
  }

  public function edit(CareerJob $job)
  {
    $job->load('questions');
    return view('admin.careers.jobs.form', compact('job'));
  }

  public function update(StoreCareerJobRequest $request, CareerJob $job)
  {
    $data = $this->jobData($request);
    $data['updated_by'] = auth('admin')->id();
    if ($data['status'] === 'published' && !$job->published_at) {
      $data['published_at'] = now();
    }

    $job->update($data);
    $this->syncQuestions($job, $request->input('questions', []));

    return redirect()->route('admin.careers.jobs.edit', $job)->with('success', __('Job updated successfully.'));
  }

  public function publish(CareerJob $job)
  {
    $job->update(['status' => 'published', 'published_at' => $job->published_at ?: now(), 'updated_by' => auth('admin')->id()]);
    return back()->with('success', __('Job published.'));
  }

  public function close(CareerJob $job)
  {
    $job->update(['status' => 'closed', 'updated_by' => auth('admin')->id()]);
    return back()->with('success', __('Job closed.'));
  }

  public function archive(CareerJob $job)
  {
    $job->update(['status' => 'archived', 'updated_by' => auth('admin')->id()]);
    return back()->with('success', __('Job archived.'));
  }

  public function duplicate(CareerJob $job)
  {
    $copy = $job->replicate(['slug', 'status', 'published_at']);
    $copy->title = $job->title . ' Copy';
    $copy->slug = Str::slug($copy->title) . '-' . Str::lower(Str::random(5));
    $copy->status = 'draft';
    $copy->published_at = null;
    $copy->created_by = auth('admin')->id();
    $copy->updated_by = auth('admin')->id();
    $copy->save();

    foreach ($job->questions as $question) {
      $copy->questions()->create($question->only(['question', 'help_text', 'question_type', 'options', 'is_required', 'display_order']));
    }

    return redirect()->route('admin.careers.jobs.edit', $copy)->with('success', __('Job duplicated as draft.'));
  }

  public function destroy(CareerJob $job)
  {
    $job->delete();
    return redirect()->route('admin.careers.jobs.index')->with('success', __('Job deleted safely.'));
  }

  private function jobData(StoreCareerJobRequest $request): array
  {
    $data = $request->safe()->except('questions');
    foreach (['description', 'responsibilities', 'requirements', 'preferred_skills', 'benefits'] as $field) {
      if (!empty($data[$field])) {
        $data[$field] = app('purifier')->clean($data[$field]);
      }
    }
    $data['featured'] = $request->boolean('featured');
    $data['display_order'] = $data['display_order'] ?? 0;
    $data['slug'] = Str::slug($data['slug']);
    return $data;
  }

  private function syncQuestions(CareerJob $job, array $questions): void
  {
    $job->questions()->delete();

    foreach ($questions as $question) {
      if (empty($question['question'])) {
        continue;
      }

      $options = null;
      if (!empty($question['options'])) {
        $options = collect(preg_split('/\r\n|\r|\n/', $question['options']))->map(fn ($option) => trim($option))->filter()->values()->all();
      }

      $job->questions()->create([
        'question' => $question['question'],
        'help_text' => $question['help_text'] ?? null,
        'question_type' => $question['question_type'] ?? 'short_text',
        'options' => $options,
        'is_required' => !empty($question['is_required']),
        'display_order' => $question['display_order'] ?? 0,
      ]);
    }
  }
}
