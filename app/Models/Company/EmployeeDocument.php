<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeDocument extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'employee_documents';

    protected $fillable = [
        'employee_id',
        'document_type_id',
        'title',
        'document_number',
        'file_path',
        'original_file_name',
        'file_type',
        'file_size',
        'issue_date',
        'expiry_date',
        'is_required',
        'review_status',
        'review_notes',
        'notes',
        'uploaded_by',
        'uploaded_at',
        'reviewed_by',
        'reviewed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_required' => 'boolean',
        'uploaded_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function documentType()
    {
        return $this->belongsTo(
            EmployeeDocumentType::class,
            'document_type_id'
        );
    }

    public function uploadedBy()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'uploaded_by'
        );
    }

    public function reviewedBy()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'reviewed_by'
        );
    }

    public function getDocumentStatusAttribute()
    {
        if (!$this->file_path) {
            return 'missing';
        }

        if ($this->review_status === 'rejected') {
            return 'rejected';
        }

        if ($this->review_status === 'pending_review') {
            return 'pending_review';
        }

        if (!$this->expiry_date) {
            return 'valid';
        }

        if ($this->expiry_date->isPast()) {
            return 'expired';
        }

        if (
            $this->expiry_date->lte(
                Carbon::today()->addDays(30)
            )
        ) {
            return 'expiring_soon';
        }

        return 'valid';
    }
 
}