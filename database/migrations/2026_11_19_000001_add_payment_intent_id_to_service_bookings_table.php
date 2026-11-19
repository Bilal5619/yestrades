<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Stores the Stripe PaymentIntent id for wallet (Apple Pay / Google Pay)
// bookings so a retried finalize request can be deduplicated instead of
// creating a second booking for the same captured payment.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('service_bookings', 'payment_intent_id')) {
                $table->string('payment_intent_id')->nullable()->unique()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('service_bookings', 'payment_intent_id')) {
                $table->dropUnique(['payment_intent_id']);
                $table->dropColumn('payment_intent_id');
            }
        });
    }
};
