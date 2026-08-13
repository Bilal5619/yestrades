<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // The earlier tenant-fields migration (2026_08_12_000000) missed two
    // columns that the checkout/tenant-slot code actually writes to:
    // `scheduling_mode` (customer vs tenant-deferred booking) and
    // `tenant_owner_confirmation_sent_at`. On environments where that
    // migration ran against a table that didn't already have the tenant
    // columns, these two are missing, causing "Unknown column" SQL errors
    // at checkout. Guarded so it's a no-op anywhere they already exist.
    public function up(): void
    {
        if (!Schema::hasColumn('service_bookings', 'scheduling_mode')) {
            DB::statement("ALTER TABLE service_bookings ADD COLUMN scheduling_mode VARCHAR(20) NOT NULL DEFAULT 'customer' AFTER booking_date");
        }

        if (!Schema::hasColumn('service_bookings', 'tenant_owner_confirmation_sent_at')) {
            DB::statement('ALTER TABLE service_bookings ADD COLUMN tenant_owner_confirmation_sent_at TIMESTAMP NULL AFTER tenant_owner_confirmation_token_hash');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('service_bookings', 'tenant_owner_confirmation_sent_at')) {
            DB::statement('ALTER TABLE service_bookings DROP COLUMN tenant_owner_confirmation_sent_at');
        }

        if (Schema::hasColumn('service_bookings', 'scheduling_mode')) {
            DB::statement('ALTER TABLE service_bookings DROP COLUMN scheduling_mode');
        }
    }
};
