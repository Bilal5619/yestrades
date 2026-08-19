<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employee_absences', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->string('absence_type', 50);

            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->decimal('days_absent', 6, 2)
                ->nullable();

            $table->string('status', 30)
                ->default('open');

            $table->text('reason')
                ->nullable();

            $table->string('evidence_path', 500)
                ->nullable();

            $table->date('return_to_work_date')
                ->nullable();

            $table->boolean('return_to_work_completed')
                ->default(false);

            $table->text('return_to_work_notes')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('company_users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('company_users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'employee_id',
                'start_date',
            ]);

            $table->index('absence_type');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_absences');
    }
};