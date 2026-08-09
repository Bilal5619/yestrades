<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddAppointmentStatusToEmployeeJobsTable extends Migration
{
    public function up()
    {
        /*
        |--------------------------------------------------------------------------
        | Add appointment status
        |--------------------------------------------------------------------------
        */

        if (
            !Schema::hasColumn(
                'employee_jobs',
                'appointment_status'
            )
        ) {
            Schema::table(
                'employee_jobs',
                function (Blueprint $table) {

                    $table
                        ->string(
                            'appointment_status'
                        )
                        ->default(
                            'not_confirmed'
                        )
                        ->after(
                            'site_postcode'
                        );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Allow jobs without a confirmed date/time
        |--------------------------------------------------------------------------
        |
        | Raw SQL avoids the Doctrine DBAL requirement.
        |
        */

        DB::statement(
            'ALTER TABLE employee_jobs
             MODIFY scheduled_date DATE NULL'
        );

        DB::statement(
            'ALTER TABLE employee_jobs
             MODIFY scheduled_start_time TIME NULL'
        );
    }

    public function down()
    {
        /*
        |--------------------------------------------------------------------------
        | Restore scheduled date
        |--------------------------------------------------------------------------
        |
        | Do not make scheduled_start_time required because it was already
        | nullable before this migration.
        |
        */

        DB::statement(
            'ALTER TABLE employee_jobs
             MODIFY scheduled_date DATE NOT NULL'
        );

        if (
            Schema::hasColumn(
                'employee_jobs',
                'appointment_status'
            )
        ) {
            Schema::table(
                'employee_jobs',
                function (Blueprint $table) {

                    $table->dropColumn(
                        'appointment_status'
                    );
                }
            );
        }
    }
}