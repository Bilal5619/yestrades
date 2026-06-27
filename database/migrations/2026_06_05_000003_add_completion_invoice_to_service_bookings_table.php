<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompletionInvoiceToServiceBookingsTable extends Migration
{
    public function up()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->string('completion_invoice')->nullable()->after('job_certificate');
        });
    }

    public function down()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->dropColumn('completion_invoice');
        });
    }
}
