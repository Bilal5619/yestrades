<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('services')) {
            return;
        }

        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'approval_status')) {
                // pending | approved | rejected. Existing rows default to approved
                // so the live storefront is unaffected; new vendor-created services
                // are explicitly set to pending in the controller.
                $table->string('approval_status')->default('approved');
            }
            if (!Schema::hasColumn('services', 'service_rejection_reason')) {
                $table->text('service_rejection_reason')->nullable();
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('services')) {
            return;
        }

        Schema::table('services', function (Blueprint $table) {
            foreach (['approval_status', 'service_rejection_reason'] as $column) {
                if (Schema::hasColumn('services', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
