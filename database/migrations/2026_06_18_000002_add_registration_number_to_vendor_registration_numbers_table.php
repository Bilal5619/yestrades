<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    if (!Schema::hasTable('vendor_registration_numbers') || Schema::hasColumn('vendor_registration_numbers', 'registration_number')) {
      return;
    }

    Schema::table('vendor_registration_numbers', function (Blueprint $table) {
      $column = $table->string('registration_number')->nullable();

      if (Schema::hasColumn('vendor_registration_numbers', 'category')) {
        $column->after('category');
      }
    });
  }

  public function down(): void
  {
    if (!Schema::hasTable('vendor_registration_numbers') || !Schema::hasColumn('vendor_registration_numbers', 'registration_number')) {
      return;
    }

    Schema::table('vendor_registration_numbers', function (Blueprint $table) {
      $table->dropColumn('registration_number');
    });
  }
};
