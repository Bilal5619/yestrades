<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vat_sales', function (Blueprint $table) {
            $table->id();

            $table->string('customer_name')->nullable();
            $table->string('reference')->nullable();
            $table->string('source')->nullable();

            $table->decimal('gross_amount', 12, 2);
            $table->decimal('net_amount', 12, 2);
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->decimal('vat_amount', 12, 2)->default(0);

            $table->date('sale_date');

            $table->string('payment_status')->default('paid');

            $table->string('description')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vat_sales');
    }
};