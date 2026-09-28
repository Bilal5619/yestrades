<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::table('basic_settings', function (Blueprint $table) {
      if (!Schema::hasColumn('basic_settings', 'certificate_reminder_email')) {
        $table->string('certificate_reminder_email')->nullable();
      }
    });

    DB::table('basic_settings')
      ->where('uniqid', 12345)
      ->whereNull('certificate_reminder_email')
      ->update(['certificate_reminder_email' => 'yestradesltd@gmail.com']);
  }

  public function down()
  {
    Schema::table('basic_settings', function (Blueprint $table) {
      $table->dropColumn('certificate_reminder_email');
    });
  }
};
