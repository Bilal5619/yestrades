<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('basic_settings') && !Schema::hasColumn('basic_settings', 'postal_code_default_price')) {
            Schema::table('basic_settings', function (Blueprint $table) {
                $table->decimal('postal_code_default_price', 20, 2)->nullable()->after('base_currency_rate');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('basic_settings') && Schema::hasColumn('basic_settings', 'postal_code_default_price')) {
            Schema::table('basic_settings', function (Blueprint $table) {
                $table->dropColumn('postal_code_default_price');
            });
        }
    }
};
