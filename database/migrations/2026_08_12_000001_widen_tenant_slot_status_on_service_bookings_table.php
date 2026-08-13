<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // The status value "tenant_selected_pending_owner_confirmation" is 43
    // characters, which would truncate/fail on the original varchar(40).
    // Raw SQL avoids the Doctrine DBAL requirement, matching how this
    // codebase widens other columns.
    public function up(): void
    {
        DB::statement('ALTER TABLE service_bookings MODIFY tenant_slot_status VARCHAR(80) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE service_bookings MODIFY tenant_slot_status VARCHAR(40) NULL');
    }
};
