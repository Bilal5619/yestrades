<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::table('service_reviews', function (Blueprint $table) {
      if (!Schema::hasColumn('service_reviews', 'booking_id')) {
        $table->unsignedBigInteger('booking_id')->nullable()->after('id');
        $table->unique('booking_id', 'service_reviews_booking_id_unique');
      }

      if (!Schema::hasColumn('service_reviews', 'customer_email')) {
        $table->string('customer_email')->nullable()->after('user_id');
      }

      if (!Schema::hasColumn('service_reviews', 'status')) {
        $table->string('status')->default('pending')->after('rating');
        $table->index('status', 'service_reviews_status_index');
      }

      if (!Schema::hasColumn('service_reviews', 'token')) {
        $table->string('token', 80)->nullable()->after('status');
        $table->unique('token', 'service_reviews_token_unique');
      }

      if (!Schema::hasColumn('service_reviews', 'image')) {
        $table->string('image')->nullable()->after('comment');
      }

      if (!Schema::hasColumn('service_reviews', 'reviewed_at')) {
        $table->timestamp('reviewed_at')->nullable()->after('token');
      }
    });
  }

  public function down()
  {
    Schema::table('service_reviews', function (Blueprint $table) {
      if (Schema::hasColumn('service_reviews', 'booking_id')) {
        $table->dropUnique('service_reviews_booking_id_unique');
        $table->dropColumn('booking_id');
      }

      if (Schema::hasColumn('service_reviews', 'customer_email')) {
        $table->dropColumn('customer_email');
      }

      if (Schema::hasColumn('service_reviews', 'status')) {
        $table->dropIndex('service_reviews_status_index');
        $table->dropColumn('status');
      }

      if (Schema::hasColumn('service_reviews', 'token')) {
        $table->dropUnique('service_reviews_token_unique');
        $table->dropColumn('token');
      }

      if (Schema::hasColumn('service_reviews', 'image')) {
        $table->dropColumn('image');
      }

      if (Schema::hasColumn('service_reviews', 'reviewed_at')) {
        $table->dropColumn('reviewed_at');
      }
    });
  }
};
