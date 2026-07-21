<?php

namespace App\Models\Careers;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;

class CareerApplicationActivity extends Model
{
  protected $fillable = ['career_application_id', 'admin_id', 'type', 'previous_status', 'new_status', 'note'];

  public function admin()
  {
    return $this->belongsTo(Admin::class);
  }
}
