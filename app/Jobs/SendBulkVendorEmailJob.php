<?php

namespace App\Jobs;

use App\Models\BasicSettings\Basic;
use App\Models\BulkVendorEmailRecipient;
use Config;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBulkVendorEmailJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public $tries = 1;

  protected $recipientId;

  public function __construct(int $recipientId)
  {
    $this->recipientId = $recipientId;
  }

  public function handle()
  {
    $recipient = BulkVendorEmailRecipient::with(['bulkEmail', 'vendor.vendor_info'])->find($this->recipientId);

    if (!$recipient || !$recipient->bulkEmail || empty($recipient->email)) {
      return;
    }

    try {
      $info = $this->configureMailer();
      $bulkEmail = $recipient->bulkEmail;
      $vendorName = optional(optional($recipient->vendor)->vendor_info)->name
        ?: optional($recipient->vendor)->username
        ?: 'Vendor';

      Mail::send('emails.bulk-vendor-email', [
        'subjectLine' => $bulkEmail->subject,
        'body' => $bulkEmail->message,
        'vendorName' => $vendorName,
        'websiteInfo' => $info,
      ], function (Message $message) use ($recipient, $bulkEmail, $info) {
        $message->to($recipient->email)
          ->subject($bulkEmail->subject)
          ->from($info->from_mail, $info->from_name);
      });

      $recipient->update([
        'status' => 'sent',
        'error_message' => null,
        'sent_at' => now(),
      ]);
    } catch (\Throwable $e) {
      $recipient->update([
        'status' => 'failed',
        'error_message' => mb_substr($e->getMessage(), 0, 1000),
      ]);
    }
  }

  protected function configureMailer()
  {
    $info = Basic::select(
      'website_title',
      'logo',
      'smtp_status',
      'smtp_host',
      'smtp_port',
      'encryption',
      'smtp_username',
      'smtp_password',
      'from_mail',
      'from_name'
    )->first();

    if (!$info) {
      $info = (object) [
        'website_title' => config('app.name', '11Done'),
        'logo' => null,
        'smtp_status' => 0,
        'from_mail' => config('mail.from.address'),
        'from_name' => config('mail.from.name', config('app.name', '11Done')),
      ];
    }

    if ((int) $info->smtp_status === 1) {
      Config::set('mail.mailers.smtp', [
        'transport' => 'smtp',
        'host' => $info->smtp_host,
        'port' => $info->smtp_port,
        'encryption' => $info->encryption,
        'username' => $info->smtp_username,
        'password' => $info->smtp_password,
        'timeout' => null,
        'auth_mode' => null,
      ]);
    }

    $info->from_mail = $info->from_mail ?: config('mail.from.address');
    $info->from_name = $info->from_name ?: config('mail.from.name', $info->website_title ?? config('app.name', '11Done'));
    $info->website_title = $info->website_title ?: '11Done';

    return $info;
  }
}
