<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(
            'employee_holiday_allowances',
            function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('employee_id');

                $table->unsignedSmallInteger('holiday_year');

                $table->decimal(
                    'annual_entitlement',
                    6,
                    2
                )->default(0);

                $table->decimal(
                    'carried_over',
                    6,
                    2
                )->default(0);

                $table->decimal(
                    'adjustment',
                    6,
                    2
                )->default(0);

                $table->text('notes')->nullable();

                $table->unsignedBigInteger('created_by')
                    ->nullable();

                $table->unsignedBigInteger('updated_by')
                    ->nullable();

                $table->timestamps();

                $table->foreign('employee_id')
                    ->references('id')
                    ->on('employees')
                    ->cascadeOnDelete();

                $table->foreign('created_by')
                    ->references('id')
                    ->on('company_users')
                    ->nullOnDelete();

                $table->foreign('updated_by')
                    ->references('id')
                    ->on('company_users')
                    ->nullOnDelete();

                $table->unique([
                    'employee_id',
                    'holiday_year',
                ]);

                $table->index('holiday_year');
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists(
            'employee_holiday_allowances'
        );
    }
};