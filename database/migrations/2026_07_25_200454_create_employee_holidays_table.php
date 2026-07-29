<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(
            'employee_holidays',
            function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('employee_id');

                $table->enum('holiday_type', [
                    'annual_leave',
                    'sick_leave',
                    'unpaid_leave',
                    'maternity_leave',
                    'paternity_leave',
                    'compassionate_leave',
                    'other',
                ])->default('annual_leave');

                $table->date('start_date');
                $table->date('end_date');

                $table->enum('start_period', [
                    'full_day',
                    'morning',
                    'afternoon',
                ])->default('full_day');

                $table->enum('end_period', [
                    'full_day',
                    'morning',
                    'afternoon',
                ])->default('full_day');

                $table->decimal(
                    'total_days',
                    6,
                    2
                )->default(0);

                $table->decimal(
                    'total_hours',
                    8,
                    2
                )->nullable();

                $table->text('reason')->nullable();
                $table->text('notes')->nullable();

                $table->enum('status', [
                    'pending',
                    'approved',
                    'rejected',
                    'cancelled',
                ])->default('pending');

                $table->unsignedBigInteger('requested_by')
                    ->nullable();

                $table->timestamp('requested_at')
                    ->nullable();

                $table->unsignedBigInteger('approved_by')
                    ->nullable();

                $table->timestamp('approved_at')
                    ->nullable();

                $table->unsignedBigInteger('rejected_by')
                    ->nullable();

                $table->timestamp('rejected_at')
                    ->nullable();

                $table->text('rejection_reason')
                    ->nullable();

                $table->unsignedBigInteger('cancelled_by')
                    ->nullable();

                $table->timestamp('cancelled_at')
                    ->nullable();

                $table->text('cancellation_reason')
                    ->nullable();

                $table->unsignedBigInteger('created_by')
                    ->nullable();

                $table->unsignedBigInteger('updated_by')
                    ->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('employee_id')
                    ->references('id')
                    ->on('employees')
                    ->cascadeOnDelete();

                $table->foreign('requested_by')
                    ->references('id')
                    ->on('company_users')
                    ->nullOnDelete();

                $table->foreign('approved_by')
                    ->references('id')
                    ->on('company_users')
                    ->nullOnDelete();

                $table->foreign('rejected_by')
                    ->references('id')
                    ->on('company_users')
                    ->nullOnDelete();

                $table->foreign('cancelled_by')
                    ->references('id')
                    ->on('company_users')
                    ->nullOnDelete();

                $table->foreign('created_by')
                    ->references('id')
                    ->on('company_users')
                    ->nullOnDelete();

                $table->foreign('updated_by')
                    ->references('id')
                    ->on('company_users')
                    ->nullOnDelete();

                $table->index('employee_id');
                $table->index('holiday_type');
                $table->index('status');
                $table->index('start_date');
                $table->index('end_date');
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists('employee_holidays');
    }
};