<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteStatusHistory extends Model
{
    // Eloquent would otherwise pluralize this to "quote_status_histories".
    protected $table = 'quote_status_history';

    protected $guarded = [];

    public function quoteRequest()
    {
        return $this->belongsTo(QuoteRequest::class, 'quote_request_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
