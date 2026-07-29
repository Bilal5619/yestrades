<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(
            'employee_onboarding_inductions',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('employee_onboarding_id')
                    ->unique()
                    ->constrained('employee_onboardings')
                    ->cascadeOnDelete();

                $table->date('meeting_date');

                $table->time('meeting_time')
                    ->nullable();

                $table->string('location')
                    ->nullable();

                $table->foreignId('manager_id')
                    ->nullable()
                    ->constrained('company_users')
                    ->nullOnDelete();

                $table->text('topics_discussed')
                    ->nullable();

                $table->text('meeting_notes')
                    ->nullable();

                $table->boolean('employee_confirmed')
                    ->default(false);

                $table->boolean('meeting_completed')
                    ->default(false);

                $table->foreignId('completed_by')
                    ->nullable()
                    ->constrained('company_users')
                    ->nullOnDelete();

                $table->timestamp('completed_at')
                    ->nullable();

                $table->timestamps();
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists(
            'employee_onboarding_inductions'
        );
    }
};