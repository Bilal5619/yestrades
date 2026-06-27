<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'depends_on_question_id',
        'depends_on_option_id',
        'condition_type',
        'condition_value',
        'operator'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function dependsOnQuestion()
    {
        return $this->belongsTo(Question::class, 'depends_on_question_id');
    }

    public function dependsOnOption()
    {
        return $this->belongsTo(QuestionOption::class, 'depends_on_option_id');
    }
}
