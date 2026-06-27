<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJobCertificateToServiceBookingsTable extends Migration
{
    public function up()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->string('job_certificate')->nullable()->after('invoice');
        });
    }

    public function down()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->dropColumn('job_certificate');
        });
    }
}
