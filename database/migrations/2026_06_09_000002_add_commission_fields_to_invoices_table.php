<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCommissionFieldsToInvoicesTable extends Migration
{
  public function up()
  {
    Schema::table('invoices', function (Blueprint $table) {
      $table->decimal('customer_paid_amount', 16, 2)->default(0)->after('total_amount');
      $table->decimal('commission_percentage', 5, 2)->default(20)->after('customer_paid_amount');
      $table->decimal('commission_amount', 16, 2)->default(0)->after('commission_percentage');
      $table->decimal('vendor_payout_amount', 16, 2)->default(0)->after('commission_amount');
    });

    DB::table('invoices')->update([
      'customer_paid_amount' => DB::raw('amount'),
      'commission_percentage' => 20,
      'commission_amount' => DB::raw('ROUND(amount * 20 / 100, 2)'),
      'vendor_payout_amount' => DB::raw('ROUND(amount - (amount * 20 / 100), 2)'),
    ]);
  }

  public function down()
  {
    Schema::table('invoices', function (Blueprint $table) {
      $table->dropColumn([
        'customer_paid_amount',
        'commission_percentage',
        'commission_amount',
        'vendor_payout_amount',
      ]);
    });
  }
}
