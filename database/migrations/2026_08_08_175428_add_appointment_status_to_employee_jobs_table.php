<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAppointmentStatusToEmployeeJobsTable extends Migration
{
    public function up()
    {
        Schema::table('employee_jobs', function (Blueprint $table) {

            $table->string('appointment_status')
                ->default('not_confirmed')
                ->after('site_postcode');

            $table->date('scheduled_date')
                ->nullable()
                ->change();

            $table->time('scheduled_start_time')
                ->nullable()
                ->change();
        });
    }

    public function down()
    {
        Schema::table('employee_jobs', function (Blueprint $table) {

            $table->dropColumn(
                'appointment_status'
            );

            $table->date('scheduled_date')
                ->nullable(false)
                ->change();
        });
    }
}