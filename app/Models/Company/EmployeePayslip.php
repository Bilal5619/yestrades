<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePayslip extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'pay_period',
        'payment_date',
        'original_filename',
        'stored_filename',
        'file_path',
        'mime_type',
        'file_size',
        'notes',
        'uploaded_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'file_size' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(
            Employee::class,
            'employee_id'
        );
    }

    public function uploadedByUser()
    {
        return $this->belongsTo(
            CompanyUser::class,
            'uploaded_by'
        );
    }
}