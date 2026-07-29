<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // Employee identity
            $table->string('employee_number')->unique();

            // Personal details
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();

            // Contact details
            $table->string('personal_email')->nullable();
            $table->string('work_email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('alternative_phone')->nullable();

            // Profile photo
            $table->string('profile_photo')->nullable();

            // National Insurance
            $table->string('national_insurance_number')->nullable();

            // Address
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('county')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->default('United Kingdom');

            // Employment details
            $table->string('job_title')->nullable();
            $table->string('department')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();

            $table->enum('employment_type', [
                'full_time',
                'part_time',
                'temporary',
                'contractor',
                'apprentice',
                'zero_hours',
            ])->nullable();

            $table->date('start_date')->nullable();
            $table->date('probation_end_date')->nullable();
            $table->date('leaving_date')->nullable();

            $table->enum('employment_status', [
                'onboarding',
                'active',
                'on_leave',
                'suspended',
                'notice_period',
                'left_company',
                'archived',
            ])->default('onboarding');

            $table->string('work_location')->nullable();
            $table->decimal('normal_weekly_hours', 5, 2)->nullable();

            $table->enum('pay_type', [
                'salary',
                'hourly',
            ])->nullable();

            $table->decimal('pay_rate', 10, 2)->nullable();
            $table->string('payroll_number')->nullable();

            // Emergency contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_alternative_phone')->nullable();
            $table->text('emergency_contact_address')->nullable();

            // Internal notes
            $table->text('employment_notes')->nullable();

            // Record information
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('manager_id')
                ->references('id')
                ->on('employees')
                ->nullOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('company_users')
                ->nullOnDelete();

            $table->foreign('updated_by')
                ->references('id')
                ->on('company_users')
                ->nullOnDelete();

            $table->index('employment_status');
            $table->index('department');
            $table->index('start_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
}