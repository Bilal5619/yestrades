<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_shifts', function (Blueprint $table) {
            // When a "forgot to clock out" push reminder was last sent for
            // this shift, so the reminder command doesn't spam the employee
            // every time it runs.
            $table->timestamp('missed_clockout_reminder_sent_at')->nullable()->after('status');
        });

        Schema::table('employee_jobs', function (Blueprint $table) {
            $table->timestamp('missed_finish_reminder_sent_at')->nullable()->after('finished_at');
        });
    }

    public function down(): void
    {
        Schema::table('employee_shifts', function (Blueprint $table) {
            $table->dropColumn('missed_clockout_reminder_sent_at');
        });

        Schema::table('employee_jobs', function (Blueprint $table) {
            $table->dropColumn('missed_finish_reminder_sent_at');
        });
    }
};
