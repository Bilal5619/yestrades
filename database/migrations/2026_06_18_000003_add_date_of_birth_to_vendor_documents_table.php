<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    if (!Schema::hasTable('vendor_documents') || Schema::hasColumn('vendor_documents', 'date_of_birth')) {
      return;
    }

    Schema::table('vendor_documents', function (Blueprint $table) {
      $column = $table->date('date_of_birth')->nullable();

      if (Schema::hasColumn('vendor_documents', 'expiry_date')) {
        $column->after('expiry_date');
      }
    });
  }

  public function down(): void
  {
    if (!Schema::hasTable('vendor_documents') || !Schema::hasColumn('vendor_documents', 'date_of_birth')) {
      return;
    }

    Schema::table('vendor_documents', function (Blueprint $table) {
      $table->dropColumn('date_of_birth');
    });
  }
};
