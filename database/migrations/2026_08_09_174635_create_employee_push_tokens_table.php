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

            // Expo push tokens look like "ExponentPushToken[xxxxxxxxxxxx]" —
            // always well under 255 chars. Needs to be a bounded string
            // (not text/blob) so the unique index below is valid in MySQL.
            $table->string('expo_push_token', 255);

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