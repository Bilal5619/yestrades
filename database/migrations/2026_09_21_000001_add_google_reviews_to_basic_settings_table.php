<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::table('basic_settings', function (Blueprint $table) {
      if (!Schema::hasColumn('basic_settings', 'google_places_api_key')) {
        $table->string('google_places_api_key')->nullable();
      }
      if (!Schema::hasColumn('basic_settings', 'google_place_id')) {
        $table->string('google_place_id')->nullable();
      }
      if (!Schema::hasColumn('basic_settings', 'google_reviews_status')) {
        $table->tinyInteger('google_reviews_status')->default(0);
      }
    });
  }

  public function down()
  {
    Schema::table('basic_settings', function (Blueprint $table) {
      $table->dropColumn(['google_places_api_key', 'google_place_id', 'google_reviews_status']);
    });
  }
};
