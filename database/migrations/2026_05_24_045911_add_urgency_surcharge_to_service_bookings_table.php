<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->decimal('urgency_surcharge_amount', 10, 2)->nullable()->after('customer_paid');
            $table->string('urgency_surcharge_period')->nullable()->after('urgency_surcharge_amount');
        });
    }

    public function down()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->dropColumn(['urgency_surcharge_amount', 'urgency_surcharge_period']);
        });
    }
};
