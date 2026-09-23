<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::table('hero_slider_images', function (Blueprint $table) {
      if (!Schema::hasColumn('hero_slider_images', 'service_id')) {
        $table->unsignedInteger('service_id')->nullable()->after('id');
      }
    });

    // Service slides carry no upload, so the image column must accept NULL.
    // Raw SQL keeps this working without the doctrine/dbal package.
    DB::statement("ALTER TABLE `hero_slider_images` MODIFY `image` VARCHAR(255) NULL");
  }

  public function down()
  {
    Schema::table('hero_slider_images', function (Blueprint $table) {
      $table->dropColumn('service_id');
    });
  }
};
