<p>Hi {{ $employee->first_name }},</p>

@php
  $brandName = $websiteInfo->website_title ?? config('app.name');
@endphp

<p>An account has been set up for you on the {{ $brandName }} staff app. Use the link below to set your password and activate your account.</p>

<p><a href="{{ $activationUrl }}">Activate my account</a></p>

<p>This link will expire in 7 days. If it expires, please ask HR to send you a new one.</p>

<p>If you weren't expecting this, you can safely ignore this email.</p>

<p>Kind regards,<br>{{ $brandName }} HR Team</p>
