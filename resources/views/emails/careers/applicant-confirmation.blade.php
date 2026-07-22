<p>Hi {{ $application->first_name }},</p>

@php
  $brandName = $websiteInfo->website_title ?? config('app.name');
@endphp

@if ($application->application_type === 'general')
  <p>Thank you for sending your CV to {{ $brandName }}.</p>
@else
  <p>Thank you for applying for {{ $application->job_title_snapshot }} at {{ $brandName }}.</p>
@endif

<p>We have received your application successfully. Your application reference is {{ $application->reference_number }}.</p>
<p>Our team will review your application and contact you if your experience matches the requirements of the position.</p>
<p>Please do not send duplicate applications for the same vacancy.</p>

<p>Kind regards,<br>{{ $brandName }} Recruitment Team</p>
