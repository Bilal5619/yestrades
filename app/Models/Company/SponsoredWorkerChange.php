<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SponsoredWorkerChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'sponsored_worker_record_id',
        'change_type',
        'field_name',
        'old_value',
        'new_value',
        'effective_date',
        'detected_at',
        'requires_ukvi_review',
        'review_status',
        'notes',
        'created_by',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'detected_at' => 'datetime',
        'requires_ukvi_review' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function sponsoredWorkerRecord()
    {
        return $this->belongsTo(
            SponsoredWorkerRecord::class,
            'sponsored_worker_record_id'
        );
    }

    public function createdByUser()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'created_by'
        );
    }

    public function reviewedByUser()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'reviewed_by'
        );
    }

    public function getChangeTypeLabelAttribute()
    {
        return ucwords(
            str_replace(
                '_',
                ' ',
                $this->change_type
            )
        );
    }

    public function getReviewStatusLabelAttribute()
    {
        return ucwords(
            str_replace(
                '_',
                ' ',
                $this->review_status
            )
        );
    }
}