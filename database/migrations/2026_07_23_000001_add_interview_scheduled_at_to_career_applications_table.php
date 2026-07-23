<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::table('career_applications', function (Blueprint $table) {
      $table->timestamp('interview_scheduled_at')->nullable()->after('status');
    });
  }

  public function down()
  {
    Schema::table('career_applications', function (Blueprint $table) {
      $table->dropColumn('interview_scheduled_at');
    });
  }
};
