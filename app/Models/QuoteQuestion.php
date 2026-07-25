<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteQuestion extends Model
{
    public const TYPES = [
        'short_text',
        'long_text',
        'single_select',
        'multi_select',
        'yes_no',
        'number',
        'quantity',
        'dropdown',
        'date',
        'time',
        'address',
        'postcode',
        'photo_upload',
        'document_upload',
    ];

    // Question types that render an options repeater in the admin form.
    public const OPTION_BASED_TYPES = ['single_select', 'multi_select', 'dropdown'];

    // Question types that expect a file answer at submission time.
    public const UPLOAD_TYPES = ['photo_upload', 'document_upload'];

    protected $fillable = [
        'service_id',
        'title',
        'summary_label',
        'is_required',
        'image',
        'type',
        'quantity_min',
        'quantity_max',
        'quantity_default',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'quantity_min' => 'integer',
        'quantity_max' => 'integer',
        'quantity_default' => 'integer',
    ];

    public function options()
    {
        return $this->hasMany(QuoteQuestionOption::class, 'quote_question_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo(\App\Models\Services\Services::class, 'service_id');
    }

    public function isOptionBased(): bool
    {
        return in_array($this->type, self::OPTION_BASED_TYPES, true);
    }

    public function isUploadType(): bool
    {
        return in_array($this->type, self::UPLOAD_TYPES, true);
    }
}
