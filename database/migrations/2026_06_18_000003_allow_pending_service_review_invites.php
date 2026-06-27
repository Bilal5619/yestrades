<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    if (!Schema::hasTable('service_reviews')) {
      return;
    }

    if (Schema::hasColumn('service_reviews', 'rating')) {
      DB::statement('ALTER TABLE service_reviews MODIFY rating INT NULL');
    }

    if (Schema::hasColumn('service_reviews', 'comment')) {
      DB::statement('ALTER TABLE service_reviews MODIFY comment TEXT NULL');
    }
  }

  public function down()
  {
    if (!Schema::hasTable('service_reviews')) {
      return;
    }

    if (Schema::hasColumn('service_reviews', 'rating')) {
      DB::statement('UPDATE service_reviews SET rating = 0 WHERE rating IS NULL');
      DB::statement('ALTER TABLE service_reviews MODIFY rating INT NOT NULL');
    }

    if (Schema::hasColumn('service_reviews', 'comment')) {
      DB::statement("UPDATE service_reviews SET comment = '' WHERE comment IS NULL");
      DB::statement('ALTER TABLE service_reviews MODIFY comment TEXT NOT NULL');
    }
  }
};
