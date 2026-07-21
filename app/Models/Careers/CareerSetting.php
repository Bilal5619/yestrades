<?php

namespace App\Models\Careers;

use Illuminate\Database\Eloquent\Model;

class CareerSetting extends Model
{
  protected $fillable = ['key', 'value'];

  public static function valueFor(string $key, ?string $default = null): ?string
  {
    return optional(static::where('key', $key)->first())->value ?? $default;
  }
}
