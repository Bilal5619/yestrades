<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuoteRequestIdToServiceBookingsTable extends Migration
{
    public function up()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            // Traces a booking back to the quote request it was converted from
            // (set only when the booking originated from an accepted quote).
            $table->unsignedBigInteger('quote_request_id')->nullable()->after('service_package_id')->index();
        });
    }

    public function down()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->dropColumn('quote_request_id');
        });
    }
}
