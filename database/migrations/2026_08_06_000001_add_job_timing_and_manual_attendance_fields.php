<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    // Job status enum gains 'in_progress' (Start Job) and 'awaiting_review'
    // (an alternative Finish Job outcome), alongside the existing values.
    DB::statement("ALTER TABLE employee_jobs MODIFY status ENUM('assigned','accepted','en_route','on_site','in_progress','completed','awaiting_review','cancelled') NOT NULL DEFAULT 'assigned'");

    Schema::table('employee_jobs', function (Blueprint $table) {
      $table->timestamp('started_at')->nullable()->after('expected_duration_minutes');
      $table->timestamp('finished_at')->nullable()->after('started_at');
    });

    Schema::table('employees', function (Blueprint $table) {
      $table->boolean('allow_manual_attendance')->default(false)->after('staff_id');
    });

    Schema::table('employee_attendances', function (Blueprint $table) {
      $table->foreignId('job_id')->nullable()->after('shift_id')->constrained('employee_jobs')->nullOnDelete();
    });
  }

  public function down()
  {
    Schema::table('employee_attendances', function (Blueprint $table) {
      $table->dropConstrainedForeignId('job_id');
    });

    Schema::table('employees', function (Blueprint $table) {
      $table->dropColumn('allow_manual_attendance');
    });

    Schema::table('employee_jobs', function (Blueprint $table) {
      $table->dropColumn(['started_at', 'finished_at']);
    });

    DB::statement("ALTER TABLE employee_jobs MODIFY status ENUM('assigned','accepted','en_route','on_site','completed','cancelled') NOT NULL DEFAULT 'assigned'");
  }
};
