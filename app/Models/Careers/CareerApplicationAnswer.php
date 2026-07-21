<?php

namespace App\Models\Careers;

use Illuminate\Database\Eloquent\Model;

class CareerApplicationAnswer extends Model
{
  protected $fillable = ['career_application_id', 'career_job_question_id', 'question_snapshot', 'answer_text', 'answer_json'];

  protected $casts = ['answer_json' => 'array'];
}
