<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SeedQuoteMailTemplates extends Migration
{
    private const TYPES = [
        'quote_request_submitted_customer',
        'quote_request_submitted_admin',
        'quote_ready_customer',
        'quote_accepted_customer',
        'quote_declined_admin',
    ];

    public function up()
    {
        // mail_templates rows are not managed by any seeder in this codebase —
        // the existing rows live only in the DB. Insert ours the same way, but
        // via a migration so it's reproducible/version-controlled. Editable
        // afterwards from Admin > Basic Settings > Mail Templates like any other row.
        DB::table('mail_templates')->insertOrIgnore([
            [
                'mail_type' => 'quote_request_submitted_customer',
                'mail_subject' => 'We received your quote request',
                'mail_body' => '<p>Hi {customer_name},</p>'
                    . '<p>Thanks for requesting a quote. We have received your request and our team will review it shortly.</p>'
                    . '<p>Reference: #{reference_number}<br />Service: {service_title}</p>'
                    . '<p>{questions_answers_summary}</p>'
                    . '<p>We will be in touch with next steps.</p>'
                    . '<p>Best regards.<br />{website_title}</p>',
            ],
            [
                'mail_type' => 'quote_request_submitted_admin',
                'mail_subject' => 'New quote request received',
                'mail_body' => '<p>A new quote request has been submitted.</p>'
                    . '<p>Reference: #{reference_number}<br />Service: {service_title}</p>'
                    . '<p>Customer: {customer_name}<br />Email: {customer_email}<br />Phone: {customer_phone}</p>'
                    . '<p><a href="{quote_admin_link}">View this request</a></p>',
            ],
            [
                'mail_type' => 'quote_ready_customer',
                'mail_subject' => 'Your quote is ready',
                'mail_body' => '<p>Hi {customer_name},</p>'
                    . '<p>Your quote for {service_title} is ready.</p>'
                    . '<p>Reference: #{reference_number}</p>'
                    . '<p>{price_breakdown}</p>'
                    . '<p>This quote is valid until {expiry_date}.</p>'
                    . '<p><a href="{accept_link}">Accept quote</a> &nbsp; <a href="{decline_link}">Decline quote</a></p>'
                    . '<p>Best regards.<br />{website_title}</p>',
            ],
            [
                'mail_type' => 'quote_accepted_customer',
                'mail_subject' => 'Your quote has been accepted',
                'mail_body' => '<p>Hi {customer_name},</p>'
                    . '<p>Thanks for accepting your quote (Reference #{reference_number}).</p>'
                    . '<p>Total: {total_price}</p>'
                    . '<p>Please complete checkout to confirm your booking.</p>'
                    . '<p>Best regards.<br />{website_title}</p>',
            ],
            [
                'mail_type' => 'quote_declined_admin',
                'mail_subject' => 'Quote declined by customer',
                'mail_body' => '<p>The customer declined the following quote.</p>'
                    . '<p>Reference: #{reference_number}<br />Service: {service_title}<br />Customer: {customer_name}</p>'
                    . '<p>Reason: {decline_reason}</p>',
            ],
        ]);
    }

    public function down()
    {
        DB::table('mail_templates')->whereIn('mail_type', self::TYPES)->delete();
    }
}
