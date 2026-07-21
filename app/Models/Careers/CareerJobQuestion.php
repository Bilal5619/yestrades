<?php

namespace App\Models\Careers;

use Illuminate\Database\Eloquent\Model;

class CareerJobQuestion extends Model
{
  public const TYPES = ['short_text', 'long_text', 'yes_no', 'single_choice', 'multiple_choice', 'date', 'number'];

  protected $fillable = ['career_job_id', 'question', 'help_text', 'question_type', 'options', 'is_required', 'display_order'];

  protected $casts = [
    'options' => 'array',
    'is_required' => 'boolean',
  ];

  public function job()
  {
    return $this->belongsTo(CareerJob::class, 'career_job_id');
  }
}
