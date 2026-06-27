<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'title',
        'summary_label',
        'is_required',
        'image',
        'type',
        'service_id',
        'service_package_id',
        'quantity_unit_price',
        'quantity_min',
        'quantity_max',
        'quantity_default'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'quantity_unit_price' => 'decimal:2',
        'quantity_min' => 'integer',
        'quantity_max' => 'integer',
        'quantity_default' => 'integer',
    ];


    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id', 'id');
    }

    public function conditions()
    {
        return $this->hasMany(QuestionCondition::class, 'question_id', 'id');
    }

    public function dependentConditions()
    {
        return $this->hasMany(QuestionCondition::class, 'depends_on_question_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function package()
    {
        return $this->belongsTo(\App\Models\Services\ServicePackage::class, 'service_package_id');
    }
}
