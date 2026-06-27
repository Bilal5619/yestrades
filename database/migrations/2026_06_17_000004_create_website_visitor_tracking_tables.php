<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    if (!Schema::hasTable('website_visitors')) {
      Schema::create('website_visitors', function (Blueprint $table) {
        $table->id();
        $table->string('visitor_uuid')->unique();
        $table->unsignedBigInteger('user_id')->nullable()->index();
        $table->string('ip_address', 45)->nullable();
        $table->string('country')->nullable();
        $table->string('city')->nullable();
        $table->string('device_type')->nullable();
        $table->string('browser')->nullable();
        $table->string('os')->nullable();
        $table->text('referrer')->nullable();
        $table->string('utm_source')->nullable();
        $table->string('utm_medium')->nullable();
        $table->string('utm_campaign')->nullable();
        $table->timestamp('first_visited_at')->nullable();
        $table->timestamp('last_activity_at')->nullable();
        $table->timestamps();

        $table->index('last_activity_at');
      });
    }

    if (!Schema::hasTable('visitor_page_views')) {
      Schema::create('visitor_page_views', function (Blueprint $table) {
        $table->id();
        $table->string('visitor_uuid')->index();
        $table->unsignedBigInteger('user_id')->nullable()->index();
        $table->text('page_url');
        $table->string('page_title')->nullable();
        $table->text('referrer')->nullable();
        $table->timestamp('visited_at')->nullable();
        $table->timestamps();

        $table->index('visited_at');
      });
    }
  }

  public function down(): void
  {
    Schema::dropIfExists('visitor_page_views');
    Schema::dropIfExists('website_visitors');
  }
};
