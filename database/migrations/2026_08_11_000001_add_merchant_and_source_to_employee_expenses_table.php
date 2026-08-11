<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_expenses', function (Blueprint $table) {
            // "From where" the expense was made (merchant / vendor / location),
            // as distinct from the free-text description.
            $table->string('merchant')->nullable()->after('category');

            // Whether this was submitted by the employee themselves from the
            // mobile app, or recorded on their behalf by an admin.
            $table->enum('source', ['admin', 'employee_app'])->default('admin')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('employee_expenses', function (Blueprint $table) {
            $table->dropColumn(['merchant', 'source']);
        });
    }
};
