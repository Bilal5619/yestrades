<?php
// Shared branded shell every outgoing email is rendered through.
// $content   - the existing admin-editable email body (plain HTML paragraphs)
// $subject   - the email subject, shown as a small heading above the content
$basic = \App\Models\BasicSettings\Basic::select('website_title', 'logo', 'primary_color', 'address', 'contact_number', 'from_mail')->first();
$brandColor = '#' . ltrim($basic->primary_color ?? '146a5a', '#');
$siteName = $basic->website_title ?? config('app.name');
$logoUrl = !empty($basic->logo) ? asset('assets/img/' . $basic->logo) : null;
$siteUrl = url('/');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $subject ?? $siteName }}</title>
<style>
  a { color: {{ $brandColor }}; }
  @media (max-width: 480px) {
    .email-container { width: 100% !important; }
    .email-padding { padding-left: 20px !important; padding-right: 20px !important; }
  }
</style>
</head>
<body style="margin:0; padding:0; background-color:#f3f5f5; font-family:Arial, Helvetica, sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f5f5; padding:32px 16px;">
    <tr>
      <td align="center">
        <table role="presentation" class="email-container" width="600" cellpadding="0" cellspacing="0" style="width:600px; max-width:100%; background-color:#ffffff; border-radius:10px; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,0.08);">
          <tr>
            <td style="background-color:{{ $brandColor }}; padding:24px 32px; text-align:center;">
              @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $siteName }}" style="max-height:44px; max-width:220px;">
              @else
                <span style="font-size:20px; font-weight:700; color:#ffffff; letter-spacing:0.3px;">{{ $siteName }}</span>
              @endif
            </td>
          </tr>
          <tr>
            <td class="email-padding" style="padding:32px; color:#253a34; font-size:15px; line-height:1.6;">
              @if (!empty($subject))
                <h1 style="margin:0 0 16px; font-size:19px; font-weight:700; color:#0f172a;">{{ $subject }}</h1>
              @endif
              <div style="color:#3f5560;">
                {!! $content !!}
              </div>
            </td>
          </tr>
          <tr>
            <td class="email-padding" style="padding:20px 32px; background-color:#f8f9fa; border-top:1px solid #e9ecef; font-size:12px; color:#7b8a9b; text-align:center;">
              <p style="margin:0 0 4px;">
                <a href="{{ $siteUrl }}" style="color:{{ $brandColor }}; text-decoration:none; font-weight:600;">{{ $siteName }}</a>
              </p>
              @if (!empty($basic->address) || !empty($basic->contact_number))
                <p style="margin:0 0 4px;">
                  {{ $basic->address ?? '' }}{{ !empty($basic->address) && !empty($basic->contact_number) ? ' · ' : '' }}{{ $basic->contact_number ?? '' }}
                </p>
              @endif
              <p style="margin:0;">This is an automated message, please do not reply directly to this email.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
