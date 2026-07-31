<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::create('employee_jobs', function (Blueprint $table) {
      $table->id();
      $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
      $table->string('title');
      $table->text('description')->nullable();
      $table->string('customer_name')->nullable();
      $table->string('customer_phone')->nullable();
      $table->string('site_address')->nullable();
      $table->string('site_postcode')->nullable();
      $table->date('scheduled_date');
      $table->string('scheduled_start_time', 8)->nullable();
      $table->unsignedInteger('expected_duration_minutes')->nullable();
      $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
      $table->enum('status', ['assigned', 'accepted', 'en_route', 'on_site', 'completed', 'cancelled'])->default('assigned');
      $table->text('internal_notes')->nullable();
      $table->foreignId('created_by')->nullable()->constrained('company_users')->nullOnDelete();
      $table->foreignId('updated_by')->nullable()->constrained('company_users')->nullOnDelete();
      $table->timestamps();
      $table->softDeletes();

      $table->index(['employee_id', 'scheduled_date']);
    });
  }

  public function down()
  {
    Schema::dropIfExists('employee_jobs');
  }
};
