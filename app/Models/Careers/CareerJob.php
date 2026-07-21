<?php

namespace App\Models\Careers;

use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CareerJob extends Model
{
  use HasFactory, SoftDeletes;

  public const STATUSES = ['draft', 'published', 'closed', 'archived'];
  public const EMPLOYMENT_TYPES = ['Full-time', 'Part-time', 'Permanent', 'Temporary', 'Contract', 'Apprenticeship', 'Internship', 'Zero-hours', 'Other'];
  public const WORKPLACE_TYPES = ['On-site', 'Hybrid', 'Remote', 'Field-based'];

  protected $fillable = [
    'title',
    'slug',
    'department',
    'location',
    'employment_type',
    'workplace_type',
    'salary_text',
    'summary',
    'description',
    'responsibilities',
    'requirements',
    'preferred_skills',
    'benefits',
    'equal_opportunity_statement',
    'application_email',
    'closing_date',
    'status',
    'positions_available',
    'display_order',
    'featured',
    'published_at',
    'created_by',
    'updated_by',
  ];

  protected $casts = [
    'closing_date' => 'date',
    'published_at' => 'datetime',
    'featured' => 'boolean',
  ];

  public function questions()
  {
    return $this->hasMany(CareerJobQuestion::class)->orderBy('display_order')->orderBy('id');
  }

  public function applications()
  {
    return $this->hasMany(CareerApplication::class);
  }

  public function creator()
  {
    return $this->belongsTo(Admin::class, 'created_by');
  }

  public function scopeActive($query)
  {
    return $query->where('status', 'published')
      ->where(function ($query) {
        $query->whereNull('closing_date')->orWhereDate('closing_date', '>=', Carbon::today());
      });
  }

  public function getIsOpenAttribute(): bool
  {
    return $this->status === 'published' && (is_null($this->closing_date) || $this->closing_date->isToday() || $this->closing_date->isFuture());
  }
}
