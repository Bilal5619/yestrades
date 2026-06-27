<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_booking_id');
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('invoice_number');
            $table->date('invoice_date');
            $table->string('service_title')->nullable();
            $table->string('vendor_name')->nullable();
            $table->decimal('amount', 16, 2)->default(0);
            $table->decimal('vat', 16, 2)->nullable();
            $table->decimal('total_amount', 16, 2)->default(0);
            $table->string('invoice_file')->nullable();
            $table->text('bank_details')->nullable();
            $table->text('notes')->nullable();
            // not_submitted | submitted | approved | rejected
            $table->string('invoice_status')->default('submitted');
            // unpaid | paid
            $table->string('payment_status')->default('unpaid');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('payment_due_date')->nullable();
            $table->timestamp('paid_date')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamps();

            $table->index('service_booking_id');
            $table->index('vendor_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
