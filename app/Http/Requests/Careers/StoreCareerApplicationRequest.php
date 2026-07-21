<?php

namespace App\Http\Requests\Careers;

use App\Models\Careers\CareerApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCareerApplicationRequest extends FormRequest
{
  public function authorize() { return true; }

  public function rules()
  {
    $rules = [
      'first_name' => ['required', 'string', 'max:100'],
      'last_name' => ['required', 'string', 'max:100'],
      'email' => ['required', 'email', 'max:190'],
      'phone' => ['required', 'string', 'max:50'],
      'city' => ['required', 'string', 'max:120'],
      'postcode' => ['required', 'string', 'max:20'],
      'right_to_work' => ['required', Rule::in(CareerApplication::RIGHT_TO_WORK)],
      'sponsorship_required' => ['required', Rule::in(CareerApplication::SPONSORSHIP)],
      'linkedin_url' => ['nullable', 'url', 'max:255'],
      'portfolio_url' => ['nullable', 'url', 'max:255'],
      'cover_letter_text' => ['nullable', 'string', 'max:10000'],
      'earliest_start_date' => ['nullable', 'date'],
      'expected_salary' => ['nullable', 'string', 'max:120'],
      'additional_information' => ['nullable', 'string', 'max:5000'],
      'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'max:5120'],
      'cover_letter_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'max:5120'],
      'privacy_consent' => ['accepted'],
      'website' => ['nullable', 'size:0'],
    ];

    foreach ($this->route('job')->questions ?? [] as $question) {
      $key = 'questions.' . $question->id;
      $answerRules = $question->is_required ? ['required'] : ['nullable'];
      if ($question->question_type === 'multiple_choice') {
        $rules[$key] = array_merge($answerRules, ['array']);
        $rules[$key . '.*'] = ['string', 'max:255'];
      } elseif ($question->question_type === 'date') {
        $rules[$key] = array_merge($answerRules, ['date']);
      } elseif ($question->question_type === 'number') {
        $rules[$key] = array_merge($answerRules, ['numeric']);
      } else {
        $rules[$key] = array_merge($answerRules, ['string', 'max:5000']);
      }
    }

    return $rules;
  }
}
