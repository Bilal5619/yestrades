<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_expenses', function (Blueprint $table) {
            $table->decimal('return_amount', 10, 2)
                ->nullable()
                ->after('vat_amount');

            $table->decimal('return_net_amount', 10, 2)
                ->nullable()
                ->after('return_amount');

            $table->decimal('return_vat_rate', 5, 2)
                ->nullable()
                ->after('return_net_amount');

            $table->decimal('return_vat_amount', 10, 2)
                ->nullable()
                ->after('return_vat_rate');

            $table->date('return_date')
                ->nullable()
                ->after('return_vat_amount');

            $table->string('return_receipt_path')
                ->nullable()
                ->after('return_date');

            $table->string('return_note')
                ->nullable()
                ->after('return_receipt_path');
        });
    }

    public function down(): void
    {
        Schema::table('employee_expenses', function (Blueprint $table) {
            $table->dropColumn([
                'return_amount',
                'return_net_amount',
                'return_vat_rate',
                'return_vat_amount',
                'return_date',
                'return_receipt_path',
                'return_note',
            ]);
        });
    }
};