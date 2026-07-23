<p>Hi {{ $application->first_name }},</p>

@php
  $brandName = $websiteInfo->website_title ?? config('app.name');
@endphp

<p>Good news - your interview {{ $application->job_title_snapshot ? 'for ' . $application->job_title_snapshot . ' ' : '' }}at {{ $brandName }} has been scheduled.</p>

<p>
  <strong>Date:</strong> {{ $application->interview_scheduled_at->format('l, d F Y') }}<br>
  <strong>Time:</strong> {{ $application->interview_scheduled_at->format('H:i') }}
</p>

<p>Your application reference is {{ $application->reference_number }}. If this time doesn't work for you, please reply to this email and we'll be happy to reschedule.</p>

<p>Kind regards,<br>{{ $brandName }} Recruitment Team</p>
