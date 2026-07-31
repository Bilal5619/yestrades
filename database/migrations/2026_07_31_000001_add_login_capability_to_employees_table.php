<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::table('employees', function (Blueprint $table) {
      $table->foreignId('staff_id')->nullable()->after('id')->constrained('staff')->nullOnDelete();
      $table->string('password')->nullable()->after('national_insurance_number');
      $table->rememberToken()->after('password');
      $table->string('activation_token', 64)->nullable()->unique()->after('remember_token');
      $table->timestamp('activation_token_expires_at')->nullable()->after('activation_token');
      $table->timestamp('activated_at')->nullable()->after('activation_token_expires_at');
    });
  }

  public function down()
  {
    Schema::table('employees', function (Blueprint $table) {
      $table->dropConstrainedForeignId('staff_id');
      $table->dropColumn(['password', 'remember_token', 'activation_token', 'activation_token_expires_at', 'activated_at']);
    });
  }
};
