<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Use raw SQL to avoid Doctrine DBAL requirement
        // Check if columns already exist before adding (for local environments)
        $columns = DB::select("SHOW COLUMNS FROM service_bookings LIKE 'tenant_name'");
        
        if (empty($columns)) {
            DB::statement('ALTER TABLE service_bookings 
                ADD COLUMN tenant_name VARCHAR(255) NULL AFTER tenant_note,
                ADD COLUMN tenant_email VARCHAR(255) NULL AFTER tenant_name,
                ADD COLUMN tenant_phone VARCHAR(50) NULL AFTER tenant_email,
                ADD COLUMN tenant_slot_status VARCHAR(80) NULL AFTER tenant_phone,
                ADD COLUMN tenant_slot_plain_token VARCHAR(255) NULL AFTER tenant_slot_status,
                ADD COLUMN tenant_slot_token_hash VARCHAR(255) NULL AFTER tenant_slot_plain_token,
                ADD COLUMN tenant_slot_expires_at TIMESTAMP NULL AFTER tenant_slot_token_hash,
                ADD COLUMN tenant_slot_selected_at TIMESTAMP NULL AFTER tenant_slot_expires_at,
                ADD COLUMN tenant_slot_used_at TIMESTAMP NULL AFTER tenant_slot_selected_at,
                ADD COLUMN tenant_owner_confirmation_token_hash VARCHAR(255) NULL AFTER tenant_slot_used_at,
                ADD COLUMN tenant_owner_confirmed_at TIMESTAMP NULL AFTER tenant_owner_confirmation_token_hash
            ');
        }
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE service_bookings 
            DROP COLUMN tenant_name,
            DROP COLUMN tenant_email,
            DROP COLUMN tenant_phone,
            DROP COLUMN tenant_slot_status,
            DROP COLUMN tenant_slot_plain_token,
            DROP COLUMN tenant_slot_token_hash,
            DROP COLUMN tenant_slot_expires_at,
            DROP COLUMN tenant_slot_selected_at,
            DROP COLUMN tenant_slot_used_at,
            DROP COLUMN tenant_owner_confirmation_token_hash,
            DROP COLUMN tenant_owner_confirmed_at
        ');
    }
};
