<?php

namespace App\Models\BasicSettings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Basic extends Model
{
  use HasFactory;

  protected $table = 'basic_settings';

  /**
   * The attributes that aren't mass assignable.
   *
   * @var array
   */
  protected $guarded = [];

  public static function settings(array $columns = ['*'])
  {
    return static::query()->where('uniqid', 12345)->first($columns)
      ?? static::query()->first($columns);
  }

  public static function recaptchaEnabled(): bool
  {
    $status = static::query()
      ->where('uniqid', 12345)
      ->value('google_recaptcha_status');

    return (int) $status === 1;
  }
}
