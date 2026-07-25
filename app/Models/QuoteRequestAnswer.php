<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteRequestAnswer extends Model
{
    protected $fillable = [
        'quote_request_id',
        'quote_question_id',
        'answer',
    ];

    public function quoteRequest()
    {
        return $this->belongsTo(QuoteRequest::class, 'quote_request_id');
    }

    public function question()
    {
        return $this->belongsTo(QuoteQuestion::class, 'quote_question_id');
    }
}
