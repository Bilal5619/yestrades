<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create(
        'employee_onboarding_personal_details',
        function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_onboarding_id')
                ->unique()
                ->constrained('employee_onboardings')
                ->cascadeOnDelete();

            $table->string('full_name');
            $table->date('date_of_birth')->nullable();
            $table->string('personal_email')->nullable();
            $table->string('phone')->nullable();

            $table->text('home_address')->nullable();

            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_phone')->nullable();

            $table->string('national_insurance_number')->nullable();

            $table->string('bank_name')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('sort_code')->nullable();
            $table->string('account_number')->nullable();

            $table->boolean('details_verified')
                ->default(false);

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('company_users')
                ->nullOnDelete();

            $table->timestamp('verified_at')
                ->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        }
    );
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_onboarding_personal_details');
    }
};
