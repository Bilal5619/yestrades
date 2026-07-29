<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table(
            'employee_onboarding_personal_details',
            function (Blueprint $table) {
                $table->unique(
                    'employee_onboarding_id',
                    'eopd_onboarding_unique'
                );

                $table->foreign(
                    'employee_onboarding_id',
                    'eopd_onboarding_fk'
                )
                    ->references('id')
                    ->on('employee_onboardings')
                    ->cascadeOnDelete();
            }
        );
    }

    public function down()
    {
        Schema::table(
            'employee_onboarding_personal_details',
            function (Blueprint $table) {
                $table->dropForeign('eopd_onboarding_fk');
                $table->dropUnique('eopd_onboarding_unique');
                $table->dropColumn('employee_onboarding_id');
            }
        );
    }
};