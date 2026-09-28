<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Certificate expiry tracking: the date a job certificate runs out, plus a log
 * of the reminder emails already sent so the daily job never emails twice for
 * the same certificate.
 */
return new class extends Migration
{
  public function up()
  {
    Schema::table('service_bookings', function (Blueprint $table) {
      // The column already exists on some installs (added outside migrations).
      if (!Schema::hasColumn('service_bookings', 'job_certificate_expiry')) {
        $table->date('job_certificate_expiry')->nullable()->after('job_certificate');
      }
    });

    if (!Schema::hasTable('certificate_expiry_reminders')) {
      Schema::create('certificate_expiry_reminders', function (Blueprint $table) {
        $table->id();
        $table->string('source', 40);          // job_certificate | vendor_document | employee_training | employee_document
        $table->unsignedBigInteger('record_id');
        $table->date('expiry_date');
        $table->string('stage', 20);           // upcoming | expired
        $table->timestamp('sent_at')->nullable();
        $table->timestamps();
        $table->unique(['source', 'record_id', 'expiry_date', 'stage'], 'cert_reminder_unique');
      });
    }
  }

  public function down()
  {
    Schema::dropIfExists('certificate_expiry_reminders');
  }
};
