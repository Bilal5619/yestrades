<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::create('employee_shifts', function (Blueprint $table) {
      $table->id();
      $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
      $table->date('shift_date');
      $table->string('start_time', 8);
      $table->string('end_time', 8);
      $table->unsignedInteger('break_minutes')->default(0);
      $table->string('work_location')->nullable();
      $table->enum('status', ['scheduled', 'completed', 'missed', 'cancelled'])->default('scheduled');
      $table->text('notes')->nullable();
      $table->foreignId('created_by')->nullable()->constrained('company_users')->nullOnDelete();
      $table->foreignId('updated_by')->nullable()->constrained('company_users')->nullOnDelete();
      $table->timestamps();
      $table->softDeletes();

      $table->index(['employee_id', 'shift_date']);
    });
  }

  public function down()
  {
    Schema::dropIfExists('employee_shifts');
  }
};
