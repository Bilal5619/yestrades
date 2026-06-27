@extends('admin.layout')

@section('content')

  <div class="page-header">
    <h4 class="page-title">Service Options (Questions)</h4>

    <div class="ml-auto">
      <a href="{{ route('admin.questions.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Add Question
      </a>
    </div>
  </div>

  <!-- Target Filter -->
  <div class="card mb-3">
    <div class="card-body py-2">
      <form method="GET" action="{{ route('admin.questions.index') }}" class="form-inline">
        <label class="mr-2">Filter by Service:</label>
        <select name="service_id" class="form-control form-control-sm mr-2">
          <option value="">-- All Services --</option>
          @foreach($services as $svc)
            <option value="{{ $svc->service_id }}" {{ $selectedService == $svc->service_id ? 'selected' : '' }}>
              {{ $svc->name }}
            </option>
          @endforeach
        </select>
        <label class="mr-2">or Package:</label>
        <select name="service_package_id" class="form-control form-control-sm mr-2">
          <option value="">-- All Packages --</option>
          @foreach($packages as $package)
            <option value="{{ $package->id }}" {{ $selectedPackage == $package->id ? 'selected' : '' }}>
              {{ $package->title }}
            </option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-sm btn-secondary mr-1">Filter</button>
        <a href="{{ route('admin.questions.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
      </form>
    </div>
  </div>

  <div class="card">

    <div class="card-header">
      <h4 class="card-title">All Questions</h4>
    </div>

    <div class="card-body">

      @if($questions->count() == 0)
        <h4 class="text-center text-muted">No Questions Found</h4>
      @else

        <div class="table-responsive">
          <table class="table table-striped table-hover">

            <thead>
              <tr>
                <th>#</th>
                <th>Question</th>
                <th>Type</th>
                <th>Applies To</th>
                <th>Options</th>
                <th>Actions</th>
              </tr>
            </thead>

            <tbody>

              @foreach($questions as $key => $q)

                      <tr>
                        <td>{{ $key + 1 }}</td>

                        <td>{{ $q->title }}</td>

                        <td>
                          <span class="badge badge-info">
                            {{ ucfirst($q->type) }}
                          </span>
                        </td>

                        <td>
                          @php
                $svcName = $services->firstWhere('service_id', $q->service_id);
                $package = $packages->firstWhere('id', $q->service_package_id);
                          @endphp
                          @if($package)
                            <span class="badge badge-primary">Package</span>
                            {{ $package->title }}
                          @else
                          {{ $svcName ? $svcName->name : '—' }}
                          @endif
                        </td>

                        <td>
                            @if($q->type === 'quantity')
                              <div class="mb-1">
                                <span class="badge badge-primary">
                                  £{{ number_format((float) ($q->quantity_unit_price ?? 0), 2) }}/unit
                                </span>
                              </div>
                              <div class="small text-muted">
                                Min: {{ $q->quantity_min ?? 1 }} | Max: {{ $q->quantity_max ?? '∞' }} | Default: {{ $q->quantity_default ?? ($q->quantity_min ?? 1) }}
                              </div>
                            @else
                            @forelse($options[$q->id] ?? [] as $option)
                              <div class="mb-1">
                                  <span class="badge badge-secondary">{{ $option->option_text }}</span>
                                  @if($option->price)
                                    <span class="badge badge-primary">£{{ number_format($option->price, 2) }}</span>
                                  @endif
                              </div>
                            @empty
                                <span class="text-muted">No options</span>
                            @endforelse
                            @endif
                        </td>

                        <td>

                          <a href="{{ route('admin.questions.edit', $q->id) }}"
                             class="btn btn-warning btn-sm">
                             Edit
                          </a>
                            <a href="{{ route('admin.questions.show', $q->id) }}" class="btn btn-info btn-sm">
                                View
                            </a>
                          <form action="{{ route('admin.questions.destroy', $q->id) }}"
                                method="POST"
                                style="display:inline-block">

                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure?')">
                              Delete
                            </button>

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
