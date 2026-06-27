<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorBankAccountsTable extends Migration
{
  public function up()
  {
    Schema::create('vendor_bank_accounts', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('vendor_id');
      $table->string('account_holder_name');
      $table->string('bank_name');
      $table->string('account_number');
      $table->string('iban')->nullable();
      $table->string('branch_name')->nullable();
      $table->string('swift_code')->nullable();
      $table->string('routing_number')->nullable();
      $table->text('notes')->nullable();
      $table->boolean('is_active')->default(false);
      $table->timestamps();

      $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
      $table->index(['vendor_id', 'is_active']);
    });
  }

  public function down()
  {
    Schema::dropIfExists('vendor_bank_accounts');
  }
}
