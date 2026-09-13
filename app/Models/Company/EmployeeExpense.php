<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeExpense extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'employee_expenses';

    public const CATEGORIES = ['fuel', 'materials', 'tools', 'parking', 'travel', 'other'];
    public const STATUSES = ['pending', 'approved', 'rejected'];
    public const EXPENSE_TYPES = ['expense', 'return', 'personal'];

    public const SOURCES = ['admin', 'employee_app'];

    protected $fillable = [
        'employee_id',
        'category',
        'expense_type',
        'merchant',
        'amount',
        'net_amount',
        'vat_rate',
        'vat_amount',
        'return_amount',
        'return_net_amount',
        'return_vat_rate',
        'return_vat_amount',
        'return_date',
        'return_receipt_path',
        'return_note',
        'currency',
        'expense_date',
        'description',
        'receipt_path',
        'status',
        'source',
        'submitted_by',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

   protected $casts = [
    'expense_date' => 'date',
    'amount' => 'decimal:2',
    'net_amount' => 'decimal:2',
    'vat_rate' => 'decimal:2',
    'vat_amount' => 'decimal:2',
    'reviewed_at' => 'datetime',
];
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(CompanyUser::class, 'submitted_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(CompanyUser::class, 'reviewed_by');
    }
}
