<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vendor_weekly_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id')->index();
            // 0 = Sunday ... 6 = Saturday (Carbon dayOfWeek)
            $table->tinyInteger('day_of_week');
            $table->boolean('is_open')->default(true);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();

            $table->unique(['vendor_id', 'day_of_week']);
        });

        Schema::create('vendor_availability_exceptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id')->index();
            $table->date('date')->index();
            // full day block, or a partial time range when is_full_day = false
            $table->boolean('is_full_day')->default(true);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_availability_exceptions');
        Schema::dropIfExists('vendor_weekly_hours');
    }
};
