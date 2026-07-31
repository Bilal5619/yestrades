<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
  use HasFactory;
  use SoftDeletes;

  protected $table = 'announcements';

  protected $fillable = [
    'title',
    'body',
    'is_urgent',
    'published_at',
    'created_by',
    'updated_by',
  ];

  protected $casts = [
    'is_urgent' => 'boolean',
    'published_at' => 'datetime',
  ];

  public function createdBy()
  {
    return $this->belongsTo(CompanyUser::class, 'created_by');
  }

  public function updatedBy()
  {
    return $this->belongsTo(CompanyUser::class, 'updated_by');
  }

  public function reads()
  {
    return $this->hasMany(AnnouncementRead::class);
  }

  public function isPublished(): bool
  {
    return $this->published_at !== null && $this->published_at->lte(now());
  }
}
