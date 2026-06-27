<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorRegistrationNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'category',
        'registration_number',
        'title',
        'number',
        'registration_authority',
        'expiry_date',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
