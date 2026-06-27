<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('service_packages', 'price_placeholder')) {
                $table->string('price_placeholder')->nullable()->after('offer_label');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_packages', function (Blueprint $table) {
            if (Schema::hasColumn('service_packages', 'price_placeholder')) {
                $table->dropColumn('price_placeholder');
            }
        });
    }
};
