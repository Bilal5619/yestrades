<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyEquipment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'company_equipment';

    public const CATEGORIES = ['vehicle', 'tool', 'device', 'other'];
    public const STATUSES = ['available', 'assigned', 'maintenance', 'retired'];

    protected $fillable = [
        'name',
        'category',
        'reference_number',
        'registration_number',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    public function assignments()
    {
        return $this->hasMany(EquipmentAssignment::class, 'equipment_id');
    }

    // The currently active (not-yet-returned) assignment for this item, if any.
    public function currentAssignment()
    {
        return $this->hasOne(EquipmentAssignment::class, 'equipment_id')
            ->whereNull('returned_at')
            ->latest('assigned_at');
    }

    public function createdBy()
    {
        return $this->belongsTo(CompanyUser::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(CompanyUser::class, 'updated_by');
    }
}
