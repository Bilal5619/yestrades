<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyPortalFieldsToCompanyUsersTable extends Migration
{
    public function up()
    {
        Schema::table('company_users', function (Blueprint $table) {
            if (!Schema::hasColumn('company_users', 'first_name')) {
                $table->string('first_name')->nullable();
            }

            if (!Schema::hasColumn('company_users', 'last_name')) {
                $table->string('last_name')->nullable();
            }

            if (!Schema::hasColumn('company_users', 'role')) {
                $table->string('role')->default('employee');
            }

            if (!Schema::hasColumn('company_users', 'status')) {
                $table->boolean('status')->default(true);
            }

            if (!Schema::hasColumn('company_users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }

            if (!Schema::hasColumn('company_users', 'remember_token')) {
                $table->rememberToken();
            }
        });
    }

    public function down()
    {
        Schema::table('company_users', function (Blueprint $table) {
            $columns = [
                'first_name',
                'last_name',
                'role',
                'status',
                'last_login_at',
                'remember_token',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('company_users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}