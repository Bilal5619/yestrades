<p>A new careers application has been received.</p>
<ul>
  <li><strong>Applicant:</strong> {{ $application->applicant_name }}</li>
  <li><strong>Job:</strong> {{ $application->job_title_snapshot ?: 'General CV Submission' }}</li>
  <li><strong>Reference:</strong> {{ $application->reference_number }}</li>
  <li><strong>Email:</strong> {{ $application->email }}</li>
  <li><strong>Phone:</strong> {{ $application->phone }}</li>
  <li><strong>Submitted:</strong> {{ optional($application->submitted_at)->format('d M Y H:i') }}</li>
</ul>
<p><a href="{{ route('admin.careers.applications.show', $application) }}">View application securely in admin</a></p>
