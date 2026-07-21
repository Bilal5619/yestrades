<?php

namespace App\Http\Requests\Careers;

use App\Models\Careers\CareerApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGeneralCvRequest extends FormRequest
{
  public function authorize() { return true; }

  public function rules()
  {
    return [
      'first_name' => ['required', 'string', 'max:100'],
      'last_name' => ['required', 'string', 'max:100'],
      'email' => ['required', 'email', 'max:190'],
      'phone' => ['required', 'string', 'max:50'],
      'city' => ['required', 'string', 'max:120'],
      'postcode' => ['required', 'string', 'max:20'],
      'areas_of_interest' => ['required', 'string', 'max:1000'],
      'preferred_employment_type' => ['nullable', 'string', 'max:120'],
      'right_to_work' => ['required', Rule::in(CareerApplication::RIGHT_TO_WORK)],
      'sponsorship_required' => ['required', Rule::in(CareerApplication::SPONSORSHIP)],
      'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'max:5120'],
      'cover_letter_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'max:5120'],
      'cover_letter_text' => ['nullable', 'string', 'max:10000'],
      'linkedin_url' => ['nullable', 'url', 'max:255'],
      'portfolio_url' => ['nullable', 'url', 'max:255'],
      'privacy_consent' => ['accepted'],
      'website' => ['nullable', 'size:0'],
    ];
  }
}
