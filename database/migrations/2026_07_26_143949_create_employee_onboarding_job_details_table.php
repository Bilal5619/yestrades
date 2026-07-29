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
        'employee_onboarding_job_details',
        function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_onboarding_id')
                ->unique()
                ->constrained('employee_onboardings')
                ->cascadeOnDelete();

            $table->string('job_title');
            $table->string('department')->nullable();

            $table->foreignId('manager_id')
                ->nullable()
                ->constrained('company_users')
                ->nullOnDelete();

            $table->date('start_date');

            $table->string('employment_type');

            $table->string('work_location')
                ->nullable();

            $table->decimal('weekly_hours', 5, 2)
                ->nullable();

            $table->string('pay_type');

            $table->decimal('salary', 12, 2)
                ->nullable();

            $table->decimal('hourly_rate', 10, 2)
                ->nullable();

            $table->decimal('holiday_allowance', 6, 2)
                ->nullable();

            $table->string('notice_period')
                ->nullable();

            $table->boolean('probation_required')
                ->default(false);

            $table->unsignedSmallInteger('probation_months')
                ->nullable();

            $table->foreignId('confirmed_by')
                ->nullable()
                ->constrained('company_users')
                ->nullOnDelete();

            $table->timestamp('confirmed_at')
                ->nullable();

            $table->text('notes')
                ->nullable();

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
        Schema::dropIfExists('employee_onboarding_job_details');
    }
};
