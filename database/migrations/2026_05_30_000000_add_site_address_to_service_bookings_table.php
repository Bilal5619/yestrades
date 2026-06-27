<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->tinyInteger('billing_same')->default(1)->after('customer_country');
            $table->string('site_name')->nullable()->after('billing_same');
            $table->string('site_phone')->nullable()->after('site_name');
            $table->string('site_email')->nullable()->after('site_phone');
            $table->string('site_address')->nullable()->after('site_email');
            $table->string('site_zip_code')->nullable()->after('site_address');
            $table->string('site_country')->nullable()->after('site_zip_code');
        });
    }

    public function down()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->dropColumn(['billing_same', 'site_name', 'site_phone', 'site_email', 'site_address', 'site_zip_code', 'site_country']);
        });
    }
};
