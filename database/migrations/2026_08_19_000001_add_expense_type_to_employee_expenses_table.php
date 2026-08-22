<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Distinguishes three kinds of claim on the same table:
    // - 'expense'  : normal spend on the company card (the original/default)
    // - 'return'   : money the employee is handing back (e.g. unused float,
    //                a refunded purchase) — shown in its own card since it's
    //                money coming back to the company, not going out
    // - 'personal' : employee spent their own money and needs reimbursing
    public function up(): void
    {
        Schema::table('employee_expenses', function (Blueprint $table) {
            $table->enum('expense_type', ['expense', 'return', 'personal'])->default('expense')->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('employee_expenses', function (Blueprint $table) {
            $table->dropColumn('expense_type');
        });
    }
};
