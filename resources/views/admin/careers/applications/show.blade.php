@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">Application {{ $application->reference_number }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home"><a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a></li>
      <li class="separator"><i class="flaticon-right-arrow"></i></li>
      <li class="nav-item"><a href="{{ route('admin.careers.applications.index') }}">Applications</a></li>
    </ul>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <div class="card-title">{{ $application->applicant_name }}</div>
        </div>
        <div class="card-body">
          <p><strong>Reference:</strong> {{ $application->reference_number }}</p>
          <p><strong>Job:</strong> {{ $application->job_title_snapshot ?: 'General CV Submission' }}</p>
          <p><strong>Submitted:</strong> {{ optional($application->submitted_at)->format('d M Y H:i') }}</p>

          <div class="row">
            <div class="col-md-6">
              <p><strong>Email:</strong> {{ $application->email }}</p>
              <p><strong>Phone:</strong> {{ $application->phone }}</p>
              <p><strong>Location:</strong> {{ $application->city }}, {{ $application->postcode }}</p>
            </div>
            <div class="col-md-6">
              <p><strong>Right to work:</strong> {{ $application->right_to_work }}</p>
              <p><strong>Sponsorship:</strong> {{ $application->sponsorship_required }}</p>
              <p><strong>Assigned:</strong> {{ optional($application->assignee)->first_name ?: '-' }}</p>
            </div>
          </div>

          @if ($application->linkedin_url)
            <p><strong>LinkedIn:</strong> <a href="{{ $application->linkedin_url }}" target="_blank" rel="noopener">{{ $application->linkedin_url }}</a></p>
          @endif

          @if ($application->portfolio_url)
            <p><strong>Portfolio:</strong> <a href="{{ $application->portfolio_url }}" target="_blank" rel="noopener">{{ $application->portfolio_url }}</a></p>
          @endif

          @if ($application->areas_of_interest)
            <p><strong>Areas of interest:</strong> {{ $application->areas_of_interest }}</p>
          @endif

          @if ($application->cover_letter_text)
            <h5>Cover letter</h5>
            <p style="white-space: pre-line;">{{ $application->cover_letter_text }}</p>
          @endif

          @if ($application->answers->count())
            <h5>Custom question answers</h5>
            @foreach ($application->answers as $answer)
              <p>
                <strong>{{ $answer->question_snapshot }}</strong><br>
                {{ $answer->answer_text ?: implode(', ', $answer->answer_json ?? []) }}
              </p>
            @endforeach
          @endif

          <h5>Documents</h5>
          <p>
            <a class="btn btn-primary btn-sm" href="{{ route('admin.careers.applications.download-cv', $application) }}">Download CV</a>
            <span class="text-muted">{{ $application->cv_original_name }}</span>
          </p>
          @if ($application->cover_letter_path)
            <p>
              <a class="btn btn-secondary btn-sm" href="{{ route('admin.careers.applications.download-cover-letter', $application) }}">Download Cover Letter</a>
              <span class="text-muted">{{ $application->cover_letter_original_name }}</span>
            </p>
          @endif
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="card-title">Activity History</div>
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            @forelse ($application->activities as $activity)
              <li class="list-group-item">
                <strong>{{ ucwords(str_replace('_', ' ', $activity->type)) }}</strong>
                @if ($activity->previous_status || $activity->new_status)
                  ({{ $activity->previous_status }} -> {{ $activity->new_status }})
                @endif
                <br>
                <small>{{ optional($activity->admin)->first_name ?: 'System' }} | {{ $activity->created_at->format('d M Y H:i') }}</small>
                @if ($activity->note)
                  <p class="mb-0">{{ $activity->note }}</p>
                @endif
              </li>
            @empty
              <li class="list-group-item">No activity yet.</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <div class="card-title">Actions</div>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.careers.applications.update-status', $application) }}" class="mb-3">
            @csrf
            <label>Status</label>
            <select class="form-control mb-2" name="status">
              @foreach (\App\Models\Careers\CareerApplication::STATUSES as $status)
                <option value="{{ $status }}" @selected($application->status === $status)>{{ ucfirst($status) }}</option>
              @endforeach
            </select>
            <textarea class="form-control mb-2" name="note" rows="2" placeholder="Optional reason or note"></textarea>
            <button class="btn btn-primary btn-sm">Update Status</button>
          </form>

          <form method="POST" action="{{ route('admin.careers.applications.add-note', $application) }}" class="mb-3">
            @csrf
            <label>Private note</label>
            <textarea class="form-control mb-2" name="note" rows="3" required></textarea>
            <button class="btn btn-info btn-sm">Add Note</button>
          </form>

          <form method="POST" action="{{ route('admin.careers.applications.send-email', $application) }}" class="mb-3">
            @csrf
            <label>Email subject</label>
            <input class="form-control mb-2" name="subject" value="Update about your {{ $websiteInfo->website_title ?? config('app.name') }} application">
            <label>Message</label>
            <textarea class="form-control mb-2" name="message" rows="5">Hi {{ $application->first_name }},&#10;&#10;Thank you for your application.&#10;&#10;Kind regards,&#10;{{ $websiteInfo->website_title ?? config('app.name') }} Recruitment Team</textarea>
            <button class="btn btn-success btn-sm">Send Email</button>
          </form>

          <div class="d-flex justify-content-between">
            @if ($previous)
              <a href="{{ route('admin.careers.applications.show', $previous) }}">Previous</a>
            @endif
            @if ($next)
              <a href="{{ route('admin.careers.applications.show', $next) }}">Next</a>
            @endif
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="card-title">Private Notes</div>
        </div>
        <div class="card-body">
          @forelse ($application->notes as $note)
            <p>
              <strong>{{ optional($note->admin)->first_name ?: 'Admin' }}</strong><br>
              {{ $note->note }}<br>
              <small>{{ $note->created_at->format('d M Y H:i') }}</small>
            </p>
          @empty
            <p>No private notes yet.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>
@endsection
