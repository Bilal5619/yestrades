<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('postal_codes')) {
            return;
        }

        Schema::table('postal_codes', function (Blueprint $table) {
            if (!Schema::hasColumn('postal_codes', 'normalized_postal_code')) {
                $table->string('normalized_postal_code')->nullable()->after('postal_code');
            }

            if (!Schema::hasColumn('postal_codes', 'service_id')) {
                $table->unsignedBigInteger('service_id')->nullable()->after('price');
                $table->index('service_id');
            }

            if (!Schema::hasColumn('postal_codes', 'service_package_id')) {
                $table->unsignedBigInteger('service_package_id')->nullable()->after('service_id');
                $table->index('service_package_id');
            }

            if (!Schema::hasColumn('postal_codes', 'service_category_id')) {
                $table->unsignedBigInteger('service_category_id')->nullable()->after('service_package_id');
                $table->index('service_category_id');
            }
        });

        DB::table('postal_codes')
            ->orderBy('id')
            ->select('id', 'postal_code')
            ->get()
            ->each(function ($rule) {
                DB::table('postal_codes')
                    ->where('id', $rule->id)
                    ->update([
                        'normalized_postal_code' => strtoupper(preg_replace('/\s+/', '', trim((string) $rule->postal_code))),
                    ]);
            });

        try {
            Schema::table('postal_codes', function (Blueprint $table) {
                $table->dropUnique('postal_codes_postal_code_unique');
            });
        } catch (\Throwable $exception) {
            // Older installs may not have the original unique index name.
        }
    }

    public function down()
    {
        if (!Schema::hasTable('postal_codes')) {
            return;
        }

        Schema::table('postal_codes', function (Blueprint $table) {
            if (Schema::hasColumn('postal_codes', 'service_category_id')) {
                $table->dropIndex(['service_category_id']);
                $table->dropColumn('service_category_id');
            }

            if (Schema::hasColumn('postal_codes', 'service_package_id')) {
                $table->dropIndex(['service_package_id']);
                $table->dropColumn('service_package_id');
            }

            if (Schema::hasColumn('postal_codes', 'service_id')) {
                $table->dropIndex(['service_id']);
                $table->dropColumn('service_id');
            }

            if (Schema::hasColumn('postal_codes', 'normalized_postal_code')) {
                $table->dropColumn('normalized_postal_code');
            }
        });
    }
};
