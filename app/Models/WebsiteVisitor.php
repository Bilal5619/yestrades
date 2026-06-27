<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteVisitor extends Model
{
  protected $guarded = [];

  protected $casts = [
    'first_visited_at' => 'datetime',
    'last_activity_at' => 'datetime',
  ];

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
  }

  public function pageViews()
  {
    return $this->hasMany(VisitorPageView::class, 'visitor_uuid', 'visitor_uuid');
  }

  public function latestPageView()
  {
    return $this->hasOne(VisitorPageView::class, 'visitor_uuid', 'visitor_uuid')->latestOfMany('visited_at');
  }
}
