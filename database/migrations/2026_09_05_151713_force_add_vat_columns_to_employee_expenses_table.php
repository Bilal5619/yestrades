<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('employee_expenses', 'net_amount')) {
            Schema::table('employee_expenses', function (Blueprint $table) {
                $table->decimal('net_amount', 10, 2)
                    ->nullable()
                    ->after('amount');
            });
        }

        if (!Schema::hasColumn('employee_expenses', 'vat_rate')) {
            Schema::table('employee_expenses', function (Blueprint $table) {
                $table->decimal('vat_rate', 5, 2)
                    ->default(0)
                    ->after('net_amount');
            });
        }

        if (!Schema::hasColumn('employee_expenses', 'vat_amount')) {
            Schema::table('employee_expenses', function (Blueprint $table) {
                $table->decimal('vat_amount', 10, 2)
                    ->default(0)
                    ->after('vat_rate');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('employee_expenses', 'vat_amount')) {
            Schema::table('employee_expenses', function (Blueprint $table) {
                $table->dropColumn('vat_amount');
            });
        }

        if (Schema::hasColumn('employee_expenses', 'vat_rate')) {
            Schema::table('employee_expenses', function (Blueprint $table) {
                $table->dropColumn('vat_rate');
            });
        }

        if (Schema::hasColumn('employee_expenses', 'net_amount')) {
            Schema::table('employee_expenses', function (Blueprint $table) {
                $table->dropColumn('net_amount');
            });
        }
    }
};