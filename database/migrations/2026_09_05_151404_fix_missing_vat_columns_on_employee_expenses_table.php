<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_expenses', 'net_amount')) {
                $table->decimal('net_amount', 10, 2)->nullable()->after('amount');
            }

            if (!Schema::hasColumn('employee_expenses', 'vat_rate')) {
                $table->decimal('vat_rate', 5, 2)->default(0)->after('net_amount');
            }

            if (!Schema::hasColumn('employee_expenses', 'vat_amount')) {
                $table->decimal('vat_amount', 10, 2)->default(0)->after('vat_rate');
            }
        });
    }

    public function down(): void
    {
        // Intentionally left empty because this migration repairs
        // columns that may already exist.
    }
};