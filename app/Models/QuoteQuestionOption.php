<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteQuestionOption extends Model
{
    protected $fillable = [
        'quote_question_id',
        'option_text',
        'description',
        'highlights',
        'option_image',
    ];

    protected $casts = [
        'highlights' => 'array',
    ];

    public function question()
    {
        return $this->belongsTo(QuoteQuestion::class, 'quote_question_id');
    }
}
