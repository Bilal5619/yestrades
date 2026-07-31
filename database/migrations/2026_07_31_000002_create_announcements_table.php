<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::create('announcements', function (Blueprint $table) {
      $table->id();
      $table->string('title');
      $table->text('body');
      $table->boolean('is_urgent')->default(false);
      $table->timestamp('published_at')->nullable();
      $table->foreignId('created_by')->nullable()->constrained('company_users')->nullOnDelete();
      $table->foreignId('updated_by')->nullable()->constrained('company_users')->nullOnDelete();
      $table->timestamps();
      $table->softDeletes();
    });

    Schema::create('announcement_reads', function (Blueprint $table) {
      $table->id();
      $table->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
      $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
      $table->timestamp('read_at');
      $table->unique(['announcement_id', 'employee_id']);
    });
  }

  public function down()
  {
    Schema::dropIfExists('announcement_reads');
    Schema::dropIfExists('announcements');
  }
};
