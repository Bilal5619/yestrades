<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorPageView extends Model
{
  protected $guarded = [];

  protected $casts = [
    'visited_at' => 'datetime',
  ];

  public function visitor()
  {
    return $this->belongsTo(WebsiteVisitor::class, 'visitor_uuid', 'visitor_uuid');
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
  }
}
