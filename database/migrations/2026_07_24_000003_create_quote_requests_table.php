<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuoteRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();

            // No hard FK to `services` (mirrors `questions.service_id`'s loose
            // convention) — quote requests are historical business records that
            // should survive independently of the service definition lifecycle.
            $table->unsignedBigInteger('service_id')->index();
            $table->unsignedBigInteger('vendor_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();

            // Customer/contact details (guest or logged-in).
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email');
            $table->string('customer_postcode');
            $table->text('customer_address');
            $table->string('preferred_contact_method')->nullable();
            $table->date('preferred_date')->nullable();
            $table->text('description')->nullable();

            $table->string('status')->default('new_request')->index();
            $table->unsignedBigInteger('assigned_staff_id')->nullable()->index();

            // Admin-entered pricing (server-calculated total, never client-trusted).
            $table->decimal('labour_cost', 10, 2)->nullable();
            $table->decimal('material_cost', 10, 2)->nullable();
            $table->decimal('callout_charge', 10, 2)->nullable();
            $table->decimal('additional_charges', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('vat_amount', 10, 2)->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('expected_duration')->nullable();

            // customer_notes is shown to the customer when the quote is sent;
            // internal_notes must NEVER be exposed to a customer-facing view/email.
            $table->text('customer_notes')->nullable();
            $table->text('internal_notes')->nullable();

            $table->timestamp('quote_sent_at')->nullable();
            $table->timestamp('customer_responded_at')->nullable();

            // Single-use accept/decline token (mirrors service_bookings.tenant_slot_token_hash in 11done).
            $table->string('response_token_hash', 64)->nullable()->unique();
            $table->timestamp('response_token_expires_at')->nullable()->index();
            $table->timestamp('response_token_used_at')->nullable();

            $table->unsignedBigInteger('converted_booking_id')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quote_requests');
    }
}
