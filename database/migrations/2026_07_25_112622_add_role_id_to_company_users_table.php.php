<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleIdToCompanyUsersTable extends Migration
{
    public function up()
    {
        Schema::table('company_users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')
                ->nullable()
                ->after('id');

            $table->foreign('role_id')
                ->references('id')
                ->on('company_roles')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('company_users', function (Blueprint $table) {
            $table->dropForeign([
                'role_id',
            ]);

            $table->dropColumn('role_id');
        });
    }
}