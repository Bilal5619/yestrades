<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class VatSale extends Model
{
    protected $table = 'vat_sales';

    protected $fillable = [
        'customer_name',
        'reference',
        'source',
        'gross_amount',
        'net_amount',
        'vat_rate',
        'vat_amount',
        'sale_date',
        'payment_status',
        'description',
        'created_by',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'sale_date' => 'date',
    ];
}