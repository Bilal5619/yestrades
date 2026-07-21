<?php

namespace App\Http\Requests\Careers;

use App\Models\Careers\CareerJob;
use App\Models\Careers\CareerJobQuestion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCareerJobRequest extends FormRequest
{
  public function authorize() { return auth('admin')->check(); }

  public function rules()
  {
    $jobId = $this->route('job') ? $this->route('job')->id : null;

    return [
      'title' => ['required', 'string', 'max:190'],
      'slug' => ['required', 'string', 'max:190', Rule::unique('career_jobs', 'slug')->ignore($jobId)],
      'department' => ['required', 'string', 'max:120'],
      'location' => ['required', 'string', 'max:120'],
      'employment_type' => ['required', Rule::in(CareerJob::EMPLOYMENT_TYPES)],
      'workplace_type' => ['required', Rule::in(CareerJob::WORKPLACE_TYPES)],
      'salary_text' => ['nullable', 'string', 'max:190'],
      'summary' => ['required', 'string', 'max:1200'],
      'description' => ['required', 'string'],
      'responsibilities' => ['nullable', 'string'],
      'requirements' => ['nullable', 'string'],
      'preferred_skills' => ['nullable', 'string'],
      'benefits' => ['nullable', 'string'],
      'equal_opportunity_statement' => ['nullable', 'string', 'max:2000'],
      'application_email' => ['nullable', 'email', 'max:190'],
      'closing_date' => ['nullable', 'date'],
      'status' => ['required', Rule::in(CareerJob::STATUSES)],
      'positions_available' => ['nullable', 'integer', 'min:1', 'max:999'],
      'display_order' => ['nullable', 'integer'],
      'featured' => ['nullable', 'boolean'],
      'questions' => ['nullable', 'array'],
      'questions.*.question' => ['nullable', 'string', 'max:255'],
      'questions.*.help_text' => ['nullable', 'string', 'max:500'],
      'questions.*.question_type' => ['nullable', Rule::in(CareerJobQuestion::TYPES)],
      'questions.*.options' => ['nullable', 'string', 'max:2000'],
      'questions.*.is_required' => ['nullable', 'boolean'],
      'questions.*.display_order' => ['nullable', 'integer'],
    ];
  }
}
