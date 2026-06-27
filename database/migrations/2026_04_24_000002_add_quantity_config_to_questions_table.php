<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'quantity_unit_price')) {
                $table->decimal('quantity_unit_price', 10, 2)->nullable()->after('service_id');
            }

            if (!Schema::hasColumn('questions', 'quantity_min')) {
                $table->unsignedInteger('quantity_min')->nullable()->after('quantity_unit_price');
            }

            if (!Schema::hasColumn('questions', 'quantity_max')) {
                $table->unsignedInteger('quantity_max')->nullable()->after('quantity_min');
            }
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('questions', 'quantity_unit_price')) {
                $columns[] = 'quantity_unit_price';
            }

            if (Schema::hasColumn('questions', 'quantity_min')) {
                $columns[] = 'quantity_min';
            }

            if (Schema::hasColumn('questions', 'quantity_max')) {
                $columns[] = 'quantity_max';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
