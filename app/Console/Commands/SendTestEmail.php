<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use App\Models\BasicSettings\Basic;

class SendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:test-email {email? : The email address to send test email to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify mail configuration from database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email') ?? $this->ask('What email address should receive the test email?');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address: ' . $email);
            return 1;
        }

        // Get SMTP settings from database
        $info = Basic::select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
            ->first();

        //log database smtp settings for debugging
        $this->line('Fetched mail settings from database:');
        $this->line('SMTP Status: ' . ($info->smtp_status == 1 ? 'Enabled' : 'Disabled'));
        $this->line('SMTP Host: ' . $info->smtp_host);
        $this->line('SMTP Port: ' . $info->smtp_port);
        $this->line('From Email: ' . $info->from_mail);
        $this->line('From Name: ' . $info->from_name);
        $this->line('');
        if (!$info) {
            $this->error('✗ Unable to fetch mail settings from database');
            return 1;
        }

        if ($info->smtp_status != 1) {
            $this->error('✗ SMTP is disabled in database settings');
            return 1;
        }

        $this->info('Configuring mail with database settings...');
        $this->line('Host: ' . $info->smtp_host);
        $this->line('Port: ' . $info->smtp_port);
        $this->line('From: ' . $info->from_mail . ' (' . $info->from_name . ')');
        $this->line('');
        $this->info('Sending test email to: ' . $email);

        try {
            // Configure mail driver with database settings
            $smtpConfig = [
                'transport' => 'smtp',
                'host' => $info->smtp_host,
                'port' => $info->smtp_port,
                'encryption' => $info->encryption,
                'username' => $info->smtp_username,
                'password' => $info->smtp_password,
                'timeout' => null,
                'auth_mode' => null,
            ];
            Config::set('mail.mailers.smtp', $smtpConfig);

            Mail::send([], [], function ($message) use ($email, $info) {
                $message->to($email)
                        ->subject('Test Email from ' . $info->website_title)
                        ->from($info->from_mail, $info->from_name)
                        ->html('This is a test email sent from ' . $info->website_title . ' application.<br><br>If you received this, your mail configuration is working correctly!', 'text/html');
            });

            $this->info('✓ Test email sent successfully to ' . $email);
            return 0;
        } catch (\Exception $e) {
            $this->error('✗ Failed to send email: ' . $e->getMessage());
            return 1;
        }
    }
}
