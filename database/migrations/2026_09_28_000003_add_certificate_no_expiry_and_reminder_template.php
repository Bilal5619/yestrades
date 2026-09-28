<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Brings certificate handling in line with 11Done: a certificate can be marked
 * as carrying no expiry at all (so it stops being chased), and the reminder
 * wording lives in an editable mail template.
 */
return new class extends Migration
{
  private const TYPE = 'certificate_expiry_reminder';

  public function up(): void
  {
    if (!Schema::hasColumn('service_bookings', 'job_certificate_has_no_expiry')) {
      Schema::table('service_bookings', function (Blueprint $table) {
        $table->boolean('job_certificate_has_no_expiry')->default(false)->after('job_certificate_expiry');
      });
    }

    DB::table('mail_templates')->insertOrIgnore([
      [
        'mail_type' => self::TYPE,
        'mail_subject' => 'Certificate expiring soon — {customer_name}',
        'mail_body' => '<p>A customer\'s certificate is coming up for renewal.</p>'
          . '<p><strong>Customer:</strong> {customer_name}<br>'
          . '<strong>Phone:</strong> {customer_phone}<br>'
          . '<strong>Email:</strong> {customer_email}<br>'
          . '<strong>Service:</strong> {service_title}<br>'
          . '<strong>Booking No.:</strong> #{booking_number}<br>'
          . '<strong>Certificate Expires:</strong> {expiry_date} ({days_remaining} days from now)</p>'
          . '<p>Call the customer to arrange a renewal booking, or wait for them to book online.</p>'
          . '<p>{booking_link}</p>',
      ],
    ]);
  }

  public function down(): void
  {
    if (Schema::hasColumn('service_bookings', 'job_certificate_has_no_expiry')) {
      Schema::table('service_bookings', function (Blueprint $table) {
        $table->dropColumn('job_certificate_has_no_expiry');
      });
    }

    DB::table('mail_templates')->where('mail_type', self::TYPE)->delete();
  }
};
