<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyRolePermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('company_role_permissions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');

            $table->timestamps();

            $table->foreign('role_id')
                ->references('id')
                ->on('company_roles')
                ->onDelete('cascade');

            $table->foreign('permission_id')
                ->references('id')
                ->on('company_permissions')
                ->onDelete('cascade');

            $table->unique([
                'role_id',
                'permission_id',
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_role_permissions');
    }
}