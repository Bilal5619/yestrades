<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('basic_settings', function (Blueprint $table) {
            $table->unsignedTinyInteger('ad_slider_max_images')->default(10)->after('theme_version');
        });
    }

    public function down()
    {
        Schema::table('basic_settings', function (Blueprint $table) {
            $table->dropColumn('ad_slider_max_images');
        });
    }
};
