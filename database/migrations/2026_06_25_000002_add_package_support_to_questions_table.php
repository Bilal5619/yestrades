<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'service_package_id')) {
                $table->unsignedBigInteger('service_package_id')->nullable()->after('service_id');
                $table->index('service_package_id');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE questions MODIFY service_id BIGINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'service_package_id')) {
                $table->dropIndex(['service_package_id']);
                $table->dropColumn('service_package_id');
            }
        });
    }
};
