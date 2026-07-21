<?php

namespace App\Models\Careers;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;

class CareerApplicationNote extends Model
{
  protected $fillable = ['career_application_id', 'admin_id', 'note'];

  public function admin()
  {
    return $this->belongsTo(Admin::class);
  }
}
