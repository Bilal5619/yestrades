<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(
            'employee_onboarding_payrolls',
            function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger(
                    'employee_onboarding_id'
                );

                $table->unique(
                    'employee_onboarding_id',
                    'eop_payroll_onboarding_unique'
                );

                $table->foreign(
                    'employee_onboarding_id',
                    'eop_payroll_onboarding_fk'
                )
                    ->references('id')
                    ->on('employee_onboardings')
                    ->cascadeOnDelete();

                $table->string('payroll_number')
                    ->nullable();

                $table->string('pay_type');

                $table->decimal('pay_rate', 12, 2);

                $table->string('pay_frequency');

                $table->date('first_payment_date')
                    ->nullable();

                $table->boolean('bank_details_verified')
                    ->default(false);

                $table->boolean('tax_information_received')
                    ->default(false);

                $table->string('pension_status')
                    ->nullable();

                $table->boolean('payroll_setup_completed')
                    ->default(false);

                $table->unsignedBigInteger('completed_by')
                    ->nullable();

                $table->foreign(
                    'completed_by',
                    'eop_payroll_completed_by_fk'
                )
                    ->references('id')
                    ->on('company_users')
                    ->nullOnDelete();

                $table->timestamp('completed_at')
                    ->nullable();

                $table->text('notes')
                    ->nullable();

                $table->timestamps();
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists(
            'employee_onboarding_payrolls'
        );
    }
};