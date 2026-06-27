<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('service_bookings', 'group_token')) {
                $table->string('group_token', 60)->nullable()->after('order_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('service_bookings', 'group_token')) {
                $table->dropColumn('group_token');
            }
        });
    }
};

