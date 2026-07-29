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
    Schema::create('employee_onboardings', function (Blueprint $table) {
        $table->id();

        $table->foreignId('employee_id')
            ->unique()
            ->constrained('employees')
            ->cascadeOnDelete();

        $table->string('status')
            ->default('pending');

        $table->unsignedTinyInteger('progress_percentage')
            ->default(0);

        $table->foreignId('assigned_to')
            ->nullable()
            ->constrained('company_users')
            ->nullOnDelete();

        $table->foreignId('created_by')
            ->nullable()
            ->constrained('company_users')
            ->nullOnDelete();

        $table->timestamp('started_at')
            ->nullable();

        $table->timestamp('completed_at')
            ->nullable();

        $table->text('completion_notes')
            ->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_onboardings');
    }
};
