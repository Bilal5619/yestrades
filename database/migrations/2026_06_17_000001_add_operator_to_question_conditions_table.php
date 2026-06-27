<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('question_conditions') || Schema::hasColumn('question_conditions', 'operator')) {
            return;
        }

        Schema::table('question_conditions', function (Blueprint $table) {
            $table->string('operator')->nullable()->after('condition_value');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('question_conditions') || !Schema::hasColumn('question_conditions', 'operator')) {
            return;
        }

        Schema::table('question_conditions', function (Blueprint $table) {
            $table->dropColumn('operator');
        });
    }
};
