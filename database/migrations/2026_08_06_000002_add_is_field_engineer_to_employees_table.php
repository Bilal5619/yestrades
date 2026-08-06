<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  // Standalone flag, fully independent of the vendor-side Staff table/business
  // Admin's Staff Management. That system stays untouched — this is purely
  // an Employee/HR-side attribute for the mobile app's clock-in logic.
  public function up()
  {
    Schema::table('employees', function (Blueprint $table) {
      $table->boolean('is_field_engineer')->default(false)->after('staff_id');
    });
  }

  public function down()
  {
    Schema::table('employees', function (Blueprint $table) {
      $table->dropColumn('is_field_engineer');
    });
  }
};
