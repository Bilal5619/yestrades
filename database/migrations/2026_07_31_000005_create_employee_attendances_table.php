<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::create('employee_attendances', function (Blueprint $table) {
      $table->id();
      $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
      $table->foreignId('shift_id')->nullable()->constrained('employee_shifts')->nullOnDelete();
      $table->enum('clock_type', ['in', 'out']);
      $table->timestamp('server_timestamp');
      $table->timestamp('device_timestamp')->nullable();
      $table->decimal('latitude', 10, 7)->nullable();
      $table->decimal('longitude', 10, 7)->nullable();
      $table->decimal('accuracy_meters', 8, 2)->nullable();
      $table->string('expected_location')->nullable();
      $table->decimal('distance_from_site_meters', 8, 2)->nullable();
      $table->boolean('within_geofence')->nullable();
      $table->string('device_platform')->nullable();
      $table->boolean('is_manual_entry')->default(false);
      $table->text('override_reason')->nullable();
      $table->foreignId('recorded_by')->nullable()->constrained('company_users')->nullOnDelete();
      $table->timestamps();

      $table->index(['employee_id', 'server_timestamp']);
    });
  }

  public function down()
  {
    Schema::dropIfExists('employee_attendances');
  }
};
