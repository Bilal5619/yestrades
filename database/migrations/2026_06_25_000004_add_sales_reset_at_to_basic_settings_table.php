<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSalesResetAtToBasicSettingsTable extends Migration
{
  public function up()
  {
    Schema::table('basic_settings', function (Blueprint $table) {
      if (!Schema::hasColumn('basic_settings', 'sales_reset_at')) {
        $table->timestamp('sales_reset_at')->nullable()->after('admin_profit');
      }
    });
  }

  public function down()
  {
    Schema::table('basic_settings', function (Blueprint $table) {
      if (Schema::hasColumn('basic_settings', 'sales_reset_at')) {
        $table->dropColumn('sales_reset_at');
      }
    });
  }
}
