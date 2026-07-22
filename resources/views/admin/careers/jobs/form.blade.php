@extends('admin.layout')
@section('content')
  <div class="page-header"><h4 class="page-title">{{ $job->exists ? 'Edit Job' : 'Add Job' }}</h4><ul class="breadcrumbs"><li class="nav-home"><a href="{{ route('admin.dashboard') }}"><i class="flaticon-home"></i></a></li><li class="separator"><i class="flaticon-right-arrow"></i></li><li class="nav-item"><a href="#">Careers</a></li></ul></div>
  <div class="card"><div class="card-header"><div class="card-title d-inline-block">{{ $job->exists ? 'Edit Job' : 'Add Job' }}</div><a class="btn btn-info btn-sm float-right" href="{{ route('admin.careers.jobs.index') }}"><i class="fas fa-backward"></i> Back</a></div><div class="card-body">
    @if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ $job->exists ? route('admin.careers.jobs.update',$job) : route('admin.careers.jobs.store') }}">@csrf
      <div class="row">
        <div class="col-md-6 form-group"><label>Title *</label><input class="form-control" name="title" value="{{ old('title',$job->title) }}" required></div>
        <div class="col-md-6 form-group"><label>Slug *</label><input class="form-control" name="slug" value="{{ old('slug',$job->slug) }}" placeholder="auto-generate-friendly-slug" required></div>
        <div class="col-md-4 form-group"><label>Department *</label><input class="form-control" name="department" value="{{ old('department',$job->department) }}" required></div>
        <div class="col-md-4 form-group"><label>Location *</label><input class="form-control" name="location" value="{{ old('location',$job->location) }}" required></div>
        <div class="col-md-4 form-group"><label>Salary text</label><input class="form-control" name="salary_text" value="{{ old('salary_text',$job->salary_text) }}"></div>
        <div class="col-md-3 form-group"><label>Employment type *</label><select class="form-control" name="employment_type" required>@foreach(\App\Models\Careers\CareerJob::EMPLOYMENT_TYPES as $option)<option value="{{ $option }}" @selected(old('employment_type',$job->employment_type)===$option)>{{ $option }}</option>@endforeach</select></div>
        <div class="col-md-3 form-group"><label>Workplace type *</label><select class="form-control" name="workplace_type" required>@foreach(\App\Models\Careers\CareerJob::WORKPLACE_TYPES as $option)<option value="{{ $option }}" @selected(old('workplace_type',$job->workplace_type)===$option)>{{ $option }}</option>@endforeach</select></div>
        <div class="col-md-2 form-group"><label>Positions</label><input class="form-control" type="number" name="positions_available" value="{{ old('positions_available',$job->positions_available) }}"></div>
        <div class="col-md-2 form-group"><label>Display order</label><input class="form-control" type="number" name="display_order" value="{{ old('display_order',$job->display_order) }}"></div>
        <div class="col-md-2 form-group"><label>Status *</label><select class="form-control" name="status">@foreach(['draft','published','closed','archived'] as $status)<option value="{{ $status }}" @selected(old('status',$job->status)===$status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <div class="col-md-4 form-group"><label>Closing date</label><input class="form-control" type="date" name="closing_date" value="{{ old('closing_date', optional($job->closing_date)->format('Y-m-d')) }}"></div>
        <div class="col-md-4 form-group"><label>Application notification email</label><input class="form-control" type="email" name="application_email" value="{{ old('application_email',$job->application_email) }}"></div>
        <div class="col-md-4 form-group pt-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="featured" value="1" id="featured" @checked(old('featured',$job->featured))><label class="form-check-label" for="featured">Featured</label></div></div>
        <div class="col-12 form-group"><label>Short summary *</label><textarea class="form-control" name="summary" rows="3" required>{{ old('summary',$job->summary) }}</textarea></div>
        @foreach(['description'=>'Full description *','responsibilities'=>'Responsibilities','requirements'=>'Requirements','preferred_skills'=>'Preferred skills','benefits'=>'Benefits'] as $field => $label)
          <div class="col-12 form-group"><label>{{ $label }}</label><textarea class="form-control summernote" name="{{ $field }}" rows="6">{{ old($field,$job->{$field}) }}</textarea></div>
        @endforeach
        <div class="col-12 form-group"><label>Equal opportunities statement</label><textarea class="form-control" name="equal_opportunity_statement" rows="3">{{ old('equal_opportunity_statement',$job->equal_opportunity_statement) }}</textarea></div>
      </div>
      <hr><h5>Custom Application Questions</h5><p class="text-muted">Use one choice per line for single or multiple choice questions.</p>
      <div id="question-list">
        @php $questions = old('questions', $job->questions->map(fn($q)=>['question'=>$q->question,'help_text'=>$q->help_text,'question_type'=>$q->question_type,'options'=>implode("\n", $q->options ?? []),'is_required'=>$q->is_required,'display_order'=>$q->display_order])->toArray() ?: [[]]); @endphp
        @foreach($questions as $index => $question)
          <div class="border rounded p-3 mb-3"><div class="row"><div class="col-md-5 form-group"><label>Question</label><input class="form-control" name="questions[{{ $index }}][question]" value="{{ $question['question'] ?? '' }}"></div><div class="col-md-3 form-group"><label>Type</label><select class="form-control" name="questions[{{ $index }}][question_type]">@foreach(\App\Models\Careers\CareerJobQuestion::TYPES as $type)<option value="{{ $type }}" @selected(($question['question_type'] ?? 'short_text') === $type)>{{ ucwords(str_replace('_',' ', $type)) }}</option>@endforeach</select></div><div class="col-md-2 form-group"><label>Order</label><input class="form-control" type="number" name="questions[{{ $index }}][display_order]" value="{{ $question['display_order'] ?? $index }}"></div><div class="col-md-2 form-group pt-4"><label><input type="checkbox" name="questions[{{ $index }}][is_required]" value="1" @checked(!empty($question['is_required']))> Required</label></div><div class="col-md-6 form-group"><label>Help text</label><input class="form-control" name="questions[{{ $index }}][help_text]" value="{{ $question['help_text'] ?? '' }}"></div><div class="col-md-6 form-group"><label>Choice options</label><textarea class="form-control" name="questions[{{ $index }}][options]" rows="2">{{ $question['options'] ?? '' }}</textarea></div></div></div>
        @endforeach
      </div>
      <button class="btn btn-primary" type="submit">Save Job</button>
    </form>
  </div></div>
@endsection
@section('script')
<script>document.querySelector('input[name="title"]').addEventListener('blur',function(){var slug=document.querySelector('input[name="slug"]');if(!slug.value){slug.value=this.value.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');}});</script>
@endsection
