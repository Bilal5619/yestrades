<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class AnnouncementRead extends Model
{
  public $timestamps = false;

  protected $table = 'announcement_reads';

  protected $fillable = [
    'announcement_id',
    'employee_id',
    'read_at',
  ];

  protected $casts = [
    'read_at' => 'datetime',
  ];

  public function announcement()
  {
    return $this->belongsTo(Announcement::class);
  }

  public function employee()
  {
    return $this->belongsTo(Employee::class);
  }
}
