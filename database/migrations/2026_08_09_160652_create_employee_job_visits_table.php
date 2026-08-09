<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeJobVisitsTable extends Migration
{
    public function up()
    {
        Schema::create(
            'employee_job_visits',
            function (Blueprint $table) {

                $table->id();

                $table->unsignedBigInteger(
                    'job_id'
                );

                $table->unsignedBigInteger(
                    'employee_id'
                )->nullable();

                $table->date(
                    'visit_date'
                );

                $table->time(
                    'start_time'
                )->nullable();

                $table->unsignedInteger(
                    'expected_duration_minutes'
                )->nullable();

                $table->string(
                    'status'
                )->default('scheduled');

                $table->timestamp(
                    'started_at'
                )->nullable();

                $table->timestamp(
                    'finished_at'
                )->nullable();

                $table->text(
                    'notes'
                )->nullable();

                $table->unsignedBigInteger(
                    'created_by'
                )->nullable();

                $table->unsignedBigInteger(
                    'updated_by'
                )->nullable();

                $table->timestamps();

                $table->foreign('job_id')
                    ->references('id')
                    ->on('employee_jobs')
                    ->cascadeOnDelete();

                $table->foreign('employee_id')
                    ->references('id')
                    ->on('employees')
                    ->nullOnDelete();
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists(
            'employee_job_visits'
        );
    }
}