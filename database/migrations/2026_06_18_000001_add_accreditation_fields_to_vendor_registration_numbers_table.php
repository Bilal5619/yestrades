<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    if (!Schema::hasTable('vendor_registration_numbers')) {
      return;
    }

    Schema::table('vendor_registration_numbers', function (Blueprint $table) {
      if (!Schema::hasColumn('vendor_registration_numbers', 'category')) {
        $table->string('category')->nullable()->after('vendor_id');
      }
      if (!Schema::hasColumn('vendor_registration_numbers', 'registration_number')) {
        $table->string('registration_number')->nullable()->after('category');
      }
      if (!Schema::hasColumn('vendor_registration_numbers', 'registration_authority')) {
        $table->string('registration_authority')->nullable()->after('number');
      }
      if (!Schema::hasColumn('vendor_registration_numbers', 'expiry_date')) {
        $table->date('expiry_date')->nullable()->after('registration_authority');
      }
    });
  }

  public function down(): void
  {
    if (!Schema::hasTable('vendor_registration_numbers')) {
      return;
    }

    Schema::table('vendor_registration_numbers', function (Blueprint $table) {
      foreach (['expiry_date', 'registration_authority', 'registration_number', 'category'] as $column) {
        if (Schema::hasColumn('vendor_registration_numbers', $column)) {
          $table->dropColumn($column);
        }
      }
    });
  }
};
