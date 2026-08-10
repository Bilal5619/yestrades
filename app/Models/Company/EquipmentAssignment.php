<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentAssignment extends Model
{
    use HasFactory;

    protected $table = 'company_equipment_assignments';

    protected $fillable = [
        'equipment_id',
        'employee_id',
        'assigned_at',
        'returned_at',
        'condition_on_return',
        'notes',
        'assigned_by',
        'returned_to',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function equipment()
    {
        return $this->belongsTo(CompanyEquipment::class, 'equipment_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(CompanyUser::class, 'assigned_by');
    }

    public function returnedTo()
    {
        return $this->belongsTo(CompanyUser::class, 'returned_to');
    }

    public function isActive(): bool
    {
        return $this->returned_at === null;
    }
}
