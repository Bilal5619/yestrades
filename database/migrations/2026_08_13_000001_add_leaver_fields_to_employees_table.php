<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'leaver_notice_date')) {
                $table->date('leaver_notice_date')->nullable()->after('leaving_date');
            }

            if (! Schema::hasColumn('employees', 'leaver_form_path')) {
                $table->text('leaver_form_path')->nullable()->after('leaver_notice_date');
            }

            if (! Schema::hasColumn('employees', 'leaver_form_original_name')) {
                $table->string('leaver_form_original_name')->nullable()->after('leaver_form_path');
            }

            if (! Schema::hasColumn('employees', 'leaver_form_uploaded_at')) {
                $table->timestamp('leaver_form_uploaded_at')->nullable()->after('leaver_form_original_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            foreach ([
                'leaver_form_uploaded_at',
                'leaver_form_original_name',
                'leaver_form_path',
                'leaver_notice_date',
            ] as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};