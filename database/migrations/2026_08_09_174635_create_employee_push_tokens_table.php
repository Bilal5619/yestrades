<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_push_tokens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->text('expo_push_token');

            $table->string('platform', 20)->nullable();

            $table->timestamp('last_used_at')->nullable();

            $table->timestamps();

            $table->unique('expo_push_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_push_tokens');
    }
};