<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sponsored_worker_records', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('employee_id');

            $table->string('immigration_route', 100)->nullable();
            $table->string('immigration_status', 50)->nullable();

            $table->date('visa_start_date')->nullable();
            $table->date('visa_expiry_date')->nullable();

            $table->string('cos_number', 50)->nullable();
            $table->string('cos_job_title', 150)->nullable();
            $table->string('occupation_code', 20)->nullable();

            $table->decimal('cos_salary', 12, 2)->nullable();
            $table->decimal('actual_salary', 12, 2)->nullable();

            $table->decimal('cos_weekly_hours', 5, 2)->nullable();

            $table->string('cos_work_location', 255)->nullable();

            $table->date('sponsorship_start_date')->nullable();
            $table->date('sponsorship_end_date')->nullable();

            $table->date('actual_first_working_date')->nullable();

            $table->string('record_status', 30)
                ->default('active');

            $table->text('sponsored_duties')->nullable();
            $table->text('permitted_absence_notes')->nullable();
            $table->text('compliance_notes')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->unique(
                'employee_id',
                'swr_employee_unique'
            );

            $table->index(
                'visa_expiry_date',
                'swr_visa_expiry_idx'
            );

            $table->index(
                'record_status',
                'swr_status_idx'
            );

            $table->foreign(
                'employee_id',
                'swr_employee_fk'
            )
                ->references('id')
                ->on('employees')
                ->cascadeOnDelete();

            $table->foreign(
                'created_by',
                'swr_created_by_fk'
            )
                ->references('id')
                ->on('company_users')
                ->nullOnDelete();

            $table->foreign(
                'updated_by',
                'swr_updated_by_fk'
            )
                ->references('id')
                ->on('company_users')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sponsored_worker_records');
    }
};