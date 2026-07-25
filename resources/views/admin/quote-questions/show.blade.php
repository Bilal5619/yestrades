@extends('admin.layout')

@section('content')

    <div class="page-header">
        <h4 class="page-title">Quote Question Details</h4>

        <div class="ml-auto">
            <a href="{{ route('admin.quote_questions.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="mb-3">
                <h5>Question</h5>
                <p>{{ $question->title }}</p>
            </div>

            <div class="mb-3">
                <h5>Type</h5>
                <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $question->type)) }}</span>
                @if($question->is_required)
                    <span class="badge badge-danger">Required</span>
                @endif
            </div>

            @if($question->type === 'quantity')
                <div class="mb-3">
                    <h5>Quantity Settings</h5>
                    <span class="badge badge-secondary">Min: {{ $question->quantity_min ?? 'Default (1)' }}</span>
                    <span class="badge badge-secondary">Max: {{ $question->quantity_max ?? 'No limit' }}</span>
                    <span class="badge badge-secondary">Default: {{ $question->quantity_default ?? ($question->quantity_min ?? 1) }}</span>
                </div>
            @endif

            <div class="mb-3">
                <h5>Options</h5>
                @if($question->options->isNotEmpty())
                    @foreach($question->options as $opt)
                        <div class="mb-2">
                            <span class="badge badge-secondary">{{ $opt->option_text }}</span>
                            @if($opt->description)
                                <small class="text-muted d-block">{{ $opt->description }}</small>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">No options available</p>
                @endif
            </div>

        </div>
    </div>

@endsection
