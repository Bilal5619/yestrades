<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerInvoiceFileToInvoicesTable extends Migration
{
  public function up()
  {
    Schema::table('invoices', function (Blueprint $table) {
      $table->string('customer_invoice_file')->nullable()->after('invoice_file');
    });
  }

  public function down()
  {
    Schema::table('invoices', function (Blueprint $table) {
      $table->dropColumn('customer_invoice_file');
    });
  }
}
