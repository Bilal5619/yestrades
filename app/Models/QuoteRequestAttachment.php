<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteRequestAttachment extends Model
{
    protected $guarded = [];

    public const STORAGE_DIR = 'assets/file/quote-requests/';

    public const TYPE_IMAGE = 'image';
    public const TYPE_DOCUMENT = 'document';

    protected static function booted()
    {
        static::deleting(function (QuoteRequestAttachment $attachment) {
            if ($attachment->file_name) {
                @unlink(public_path(self::STORAGE_DIR . $attachment->file_name));
            }
        });
    }

    public function quoteRequest()
    {
        return $this->belongsTo(QuoteRequest::class, 'quote_request_id');
    }

    public function question()
    {
        return $this->belongsTo(QuoteQuestion::class, 'quote_question_id');
    }

    public function getUrlAttribute()
    {
        return $this->file_name ? asset(self::STORAGE_DIR . $this->file_name) : null;
    }
}
