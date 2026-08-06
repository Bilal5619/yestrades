<p>Hi {{ $employee->first_name }},</p>

@php
  $brandName = $websiteInfo->website_title ?? config('app.name');
@endphp

<p>An account has been set up for you on the {{ $brandName }} staff app. Follow the steps below to get set up.</p>

<p><strong>Step 1 — Install the app</strong><br>
<a href="{{ asset('downloads/yestrades-staff-app.apk') }}">Download the {{ $brandName }} Staff App (Android)</a></p>

<p><strong>Step 2 — Activate your account</strong><br>
<a href="{{ $activationUrl }}">Activate my account</a> and set your password.</p>

<p>Once activated, open the app and log in with your email and the password you just set.</p>

<p>This activation link will expire in 7 days. If it expires, please ask HR to send you a new one.</p>

<p>If you weren't expecting this, you can safely ignore this email.</p>

<p>Kind regards,<br>{{ $brandName }} HR Team</p>
