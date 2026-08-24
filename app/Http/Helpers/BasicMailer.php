<?php

namespace App\Http\Helpers;

use App\Models\BasicSettings\Basic;
use Config;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class BasicMailer
{
  public static function sendMail($data): bool
  {
    // get the website title & mail's smtp information from db
    $info = Basic::select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
      ->first();

    // if smtp status == 1, then set some value for PHPMailer
    if ($info->smtp_status == 1) {
      try {
        $smtp = [
          'transport' => 'smtp',
          'host' => trim((string) $info->smtp_host),
          'port' => (int) $info->smtp_port,
          'encryption' => trim((string) $info->encryption),
          'username' => trim((string) $info->smtp_username),
          'password' => trim((string) $info->smtp_password),
          'timeout' => null,
          'auth_mode' => null,
        ];
        Config::set('mail.mailers.smtp', $smtp);
        Config::set('mail.from.address', trim((string) $info->from_mail));
        Config::set('mail.from.name', trim((string) $info->from_name));
        Mail::purge('smtp');
      } catch (\Exception $e) {
        Session::flash('error', $e->getMessage());
        return false;
      }
    }

    if ($info->smtp_status == 1) {
      try {
        Mail::send([], [], function (Message $message) use ($data, $info) {
          $fromMail = trim((string) $info->from_mail);
          $fromName = trim((string) $info->from_name);
          $brandedBody = view('emails.layout', [
            'content' => $data['body'],
            'subject' => $data['subject'] ?? null,
          ])->render();
          $message->to($data['recipient'])
            ->subject($data['subject'])
            ->from($fromMail, $fromName)
            ->html($brandedBody, 'text/html');

          if (array_key_exists('invoice', $data)) {
            $message->attach($data['invoice'], [
              'as' => 'Invoice',
              'mime' => 'application/pdf',
            ]);
          }

          if (array_key_exists('certificate', $data)) {
            $certPath = $data['certificate'];
            $ext = strtolower(pathinfo($certPath, PATHINFO_EXTENSION));
            $mimeMap = ['pdf' => 'application/pdf', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg'];
            $mime = $mimeMap[$ext] ?? 'application/octet-stream';
            $message->attach($certPath, [
              'as' => 'Job_Certificate.' . $ext,
              'mime' => $mime,
            ]);
          }
        });
        if (array_key_exists('sessionMessage', $data)) {
          Session::flash('success', $data['sessionMessage']);
        }
        return true;
      } catch (\Exception $e) {
        Log::warning('Mail could not be sent.', [
          'recipient' => $data['recipient'] ?? null,
          'subject' => $data['subject'] ?? null,
          'smtp_host' => trim((string) ($info->smtp_host ?? '')),
          'smtp_port' => $info->smtp_port ?? null,
          'encryption' => trim((string) ($info->encryption ?? '')),
          'smtp_username' => trim((string) ($info->smtp_username ?? '')),
          'from_mail' => trim((string) ($info->from_mail ?? '')),
          'error' => $e->getMessage(),
        ]);
        Session::flash('warning', 'Mail could not be sent. Mailer Error: ' . $e->getMessage());
        return false;
      }
    }

    Session::flash('warning', 'Mail could not be sent because SMTP is disabled.');
    return false;
  }
}
