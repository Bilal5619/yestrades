<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(
            'employee_training_records',
            function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('employee_id');

                $table->unsignedBigInteger(
                    'training_course_id'
                );

                $table->string('provider_name', 180)
                    ->nullable();

                $table->date('scheduled_date')
                    ->nullable();

                $table->timestamp('started_at')
                    ->nullable();

                $table->timestamp('completed_at')
                    ->nullable();

                $table->date('expiry_date')
                    ->nullable();

                $table->enum('status', [
                    'not_started',
                    'scheduled',
                    'in_progress',
                    'completed',
                    'expired',
                    'cancelled',
                ])->default('not_started');

                $table->string('certificate_path')
                    ->nullable();

                $table->string('certificate_number', 150)
                    ->nullable();

                $table->decimal('cost', 10, 2)
                    ->nullable();

                $table->text('notes')
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

                $table->foreign('training_course_id')
                    ->references('id')
                    ->on('training_courses')
                    ->restrictOnDelete();

                $table->foreign('created_by')
                    ->references('id')
                    ->on('company_users')
                    ->nullOnDelete();

                $table->foreign('updated_by')
                    ->references('id')
                    ->on('company_users')
                    ->nullOnDelete();

                $table->index('employee_id');
                $table->index('training_course_id');
                $table->index('status');
                $table->index('scheduled_date');
                $table->index('expiry_date');
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists(
            'employee_training_records'
        );
    }
};