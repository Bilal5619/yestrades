<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Links an internal HR job (employee_jobs) back to the website booking
// (service_bookings) it was created from, when a website booking is
// assigned directly to a Yes Trades employee instead of an external vendor.
// Nullable + unique: most employee_jobs rows are still manually created by
// HR and have no website booking behind them at all.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_jobs', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_jobs', 'service_booking_id')) {
                $table->unsignedBigInteger('service_booking_id')->nullable()->unique()->after('id');
                $table->foreign('service_booking_id')->references('id')->on('service_bookings')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_jobs', function (Blueprint $table) {
            if (Schema::hasColumn('employee_jobs', 'service_booking_id')) {
                $table->dropForeign(['service_booking_id']);
                $table->dropColumn('service_booking_id');
            }
        });
    }
};
