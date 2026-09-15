<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE service_bookings
            MODIFY booking_date DATE NULL,
            MODIFY start_date TIME NULL,
            MODIFY end_date TIME NULL,
            MODIFY staff_id BIGINT UNSIGNED NULL,
            MODIFY service_hour_id BIGINT UNSIGNED NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE service_bookings
            MODIFY booking_date DATE NOT NULL,
            MODIFY start_date TIME NOT NULL,
            MODIFY end_date TIME NOT NULL,
            MODIFY staff_id BIGINT UNSIGNED NOT NULL,
            MODIFY service_hour_id BIGINT UNSIGNED NOT NULL
        ");
    }
};