<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyUsersTable extends Migration
{
    public function up()
    {
        Schema::create('company_users', function (Blueprint $table) {
            $table->id();

            $table->string('first_name');
            $table->string('last_name')->nullable();

            $table->string('email')->unique();
            $table->string('password');

            $table->string('role')->default('employee');

            $table->boolean('status')->default(true);

            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_users');
    }
}