<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('vendors')) {
            return;
        }

        Schema::table('vendors', function (Blueprint $table) {
            if (!Schema::hasColumn('vendors', 'verification_status')) {
                // not_submitted | submitted | approved | rejected
                $table->string('verification_status')->default('not_submitted')->after('status');
            }
            if (!Schema::hasColumn('vendors', 'verification_rejection_reason')) {
                $table->text('verification_rejection_reason')->nullable()->after('verification_status');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('vendors')) {
            return;
        }

        Schema::table('vendors', function (Blueprint $table) {
            foreach (['verification_status', 'verification_rejection_reason'] as $column) {
                if (Schema::hasColumn('vendors', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
