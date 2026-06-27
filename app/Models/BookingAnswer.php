<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAnswer extends Model
{
    protected $fillable = [
        'booking_id',
        'question_id',
        'option_id',
        'answer'
    ];

    public function booking()
    {
        return $this->belongsTo(\App\Models\Services\ServiceBooking::class, 'booking_id', 'id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function option()
    {
        return $this->belongsTo(QuestionOption::class);
    }
}
