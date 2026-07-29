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
    Schema::create('onboarding_sections', function (Blueprint $table) {
        $table->id();

        $table->foreignId('employee_onboarding_id')
            ->constrained('employee_onboardings')
            ->cascadeOnDelete();

        $table->string('section_key');

        $table->string('title');

        $table->string('status')
            ->default('pending');

        $table->boolean('is_required')
            ->default(true);

        $table->unsignedTinyInteger('sort_order')
            ->default(0);

        $table->foreignId('completed_by')
            ->nullable()
            ->constrained('company_users')
            ->nullOnDelete();

        $table->timestamp('completed_at')
            ->nullable();

        $table->text('notes')
            ->nullable();

        $table->timestamps();

        $table->unique([
            'employee_onboarding_id',
            'section_key',
        ]);
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('onboarding_sections');
    }
};
