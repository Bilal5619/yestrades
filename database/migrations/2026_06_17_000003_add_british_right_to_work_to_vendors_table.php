<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    if (!Schema::hasTable('vendors') || Schema::hasColumn('vendors', 'right_to_work_is_british')) {
      return;
    }

    Schema::table('vendors', function (Blueprint $table) {
      $column = $table->boolean('right_to_work_is_british')->default(false);

      if (Schema::hasColumn('vendors', 'verification_rejection_reason')) {
        $column->after('verification_rejection_reason');
      }
    });
  }

  public function down(): void
  {
    if (!Schema::hasTable('vendors') || !Schema::hasColumn('vendors', 'right_to_work_is_british')) {
      return;
    }

    Schema::table('vendors', function (Blueprint $table) {
      $table->dropColumn('right_to_work_is_british');
    });
  }
};
