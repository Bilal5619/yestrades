@extends('admin.layout')

@section('content')

  <div class="page-header">
    <h4 class="page-title">Quote Questions</h4>

    <div class="ml-auto">
      <a href="{{ route('admin.quote_questions.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Add Quote Question
      </a>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body py-2">
      <form method="GET" action="{{ route('admin.quote_questions.index') }}" class="form-inline">
        <label class="mr-2">Filter by Service:</label>
        <select name="service_id" class="form-control form-control-sm mr-2">
          <option value="">-- All Quote Services --</option>
          @foreach($serviceNames as $svc)
            <option value="{{ $svc->service_id }}" {{ $selectedService == $svc->service_id ? 'selected' : '' }}>
              {{ $svc->name }}
            </option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-sm btn-secondary mr-1">Filter</button>
        <a href="{{ route('admin.quote_questions.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
      </form>
    </div>
  </div>

  <div class="card">

    <div class="card-header">
      <h4 class="card-title">All Quote Questions</h4>
    </div>

    <div class="card-body">

      @if($questions->count() == 0)
        <h4 class="text-center text-muted">No quote questions found</h4>
      @else

        <div class="table-responsive">
          <table class="table table-striped table-hover">

            <thead>
              <tr>
                <th>#</th>
                <th>Question</th>
                <th>Type</th>
                <th>Service</th>
                <th>Required</th>
                <th>Options</th>
                <th>Actions</th>
              </tr>
            </thead>

            <tbody>

              @foreach($questions as $key => $q)
                <tr>
                  <td>{{ $key + 1 }}</td>
                  <td>{{ $q->title }}</td>
                  <td><span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $q->type)) }}</span></td>
                  <td>
                    @php $svcName = $serviceNames->firstWhere('service_id', $q->service_id); @endphp
                    {{ $svcName ? $svcName->name : '—' }}
                  </td>
                  <td>
                    @if($q->is_required)
                      <span class="badge badge-danger">Required</span>
                    @else
                      <span class="badge badge-secondary">Optional</span>
                    @endif
                  </td>
                  <td>
                    @forelse($options[$q->id] ?? [] as $option)
                      <span class="badge badge-secondary mb-1">{{ $option->option_text }}</span>
                    @empty
                      <span class="text-muted">—</span>
                    @endforelse
                  </td>
                  <td>
                    <a href="{{ route('admin.quote_questions.edit', $q->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <a href="{{ route('admin.quote_questions.show', $q->id) }}" class="btn btn-info btn-sm">View</a>
                    <form action="{{ route('admin.quote_questions.destroy', $q->id) }}" method="POST" style="display:inline-block">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                  </td>
                </tr>
              @endforeach

            </tbody>

          </table>
        </div>

      @endif

    </div>
  </div>

@endsection
