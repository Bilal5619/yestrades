<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>{{ $subjectLine }}</title>
</head>
<body style="margin: 0; padding: 0; background: #f4f6f8; font-family: Arial, sans-serif; color: #222;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: #f4f6f8; padding: 24px 0;">
    <tr>
      <td align="center">
        <table role="presentation" width="620" cellspacing="0" cellpadding="0" style="max-width: 620px; width: 100%; background: #ffffff; border-radius: 8px; overflow: hidden;">
          <tr>
            <td style="padding: 28px 32px 18px; text-align: center; border-bottom: 1px solid #edf0f3;">
              @if (!empty($websiteInfo->logo))
                <img src="{{ asset('assets/img/' . $websiteInfo->logo) }}" alt="{{ $websiteInfo->website_title ?? config('app.name') }}" style="max-height: 54px; max-width: 180px; margin-bottom: 10px;">
              @endif
              <div style="font-size: 24px; font-weight: 700; color: #111827;">{{ $websiteInfo->website_title ?? config('app.name') }}</div>
            </td>
          </tr>
          <tr>
            <td style="padding: 28px 32px;">
              <h1 style="margin: 0 0 18px; font-size: 22px; line-height: 1.35; color: #111827;">{{ $subjectLine }}</h1>
              <div style="font-size: 15px; line-height: 1.7; color: #343a40;">
                {!! $body !!}
              </div>
            </td>
          </tr>
          <tr>
            <td style="padding: 18px 32px; background: #f9fafb; border-top: 1px solid #edf0f3; font-size: 13px; color: #6b7280; text-align: center;">
              This email was sent by {{ $websiteInfo->website_title ?? config('app.name') }} admin.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
