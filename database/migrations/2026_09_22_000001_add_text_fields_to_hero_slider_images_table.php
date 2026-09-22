<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::table('hero_slider_images', function (Blueprint $table) {
      if (!Schema::hasColumn('hero_slider_images', 'title')) {
        $table->string('title')->nullable();
        $table->string('subtitle')->nullable();
        $table->string('button_text')->nullable();
        $table->string('url')->nullable();
      }
    });
  }

  public function down()
  {
    Schema::table('hero_slider_images', function (Blueprint $table) {
      $table->dropColumn(['title', 'subtitle', 'button_text', 'url']);
    });
  }
};
