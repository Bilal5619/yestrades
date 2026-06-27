<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->date('requested_booking_date')->nullable()->after('end_date');
            $table->string('requested_start_time')->nullable()->after('requested_booking_date');
            $table->string('requested_end_time')->nullable()->after('requested_start_time');
            // null = no request, 'requested' = awaiting admin, 'approved', 'rejected'
            $table->string('reschedule_status')->nullable()->after('requested_end_time');
        });
    }

    public function down()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->dropColumn(['requested_booking_date', 'requested_start_time', 'requested_end_time', 'reschedule_status']);
        });
    }
};
