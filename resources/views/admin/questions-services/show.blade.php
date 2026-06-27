@extends('admin.layout')

@section('content')

    <div class="page-header">
        <h4 class="page-title">Question Details</h4>

        <div class="ml-auto">
            <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary btn-sm">
                Back
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <!-- QUESTION TITLE -->
            <div class="mb-3">
                <h5>Question</h5>
                <p>{{ $question->title }}</p>
            </div>

            <!-- QUESTION TYPE -->
            <div class="mb-3">
                <h5>Type</h5>
                <span class="badge badge-info">
                    {{ ucfirst($question->type) }}
                </span>
            </div>

            @if($question->type === 'quantity')
                <div class="mb-3">
                    <h5>Quantity Settings</h5>
                    <div class="mb-2">
                        <span class="badge badge-primary">
                            Unit Price: £{{ number_format((float) ($question->quantity_unit_price ?? 0), 2) }}
                        </span>
                    </div>
                    <div class="mb-2">
                        <span class="badge badge-secondary">
                            Min: {{ $question->quantity_min ?? 'Default (1)' }}
                        </span>
                        <span class="badge badge-secondary">
                            Max: {{ $question->quantity_max ?? 'No limit' }}
                        </span>
                        <span class="badge badge-secondary">
                            Default: {{ $question->quantity_default ?? ($question->quantity_min ?? 1) }}
                        </span>
                    </div>
                </div>
            @endif

            <!-- OPTIONS -->
            <div class="mb-3">
                <h5>Options</h5>

                @if(($options[$question->id] ?? collect())->isNotEmpty())
                    @foreach($options[$question->id] as $opt)
                        <div class="mb-2">
                            <span class="badge badge-secondary">
                                {{ $opt->option_text }}
                            </span>
                            @if($opt->price)
                                <span class="badge badge-primary">
                                    £{{ number_format($opt->price, 2) }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                @elseif($question->type === 'quantity')
                    <p class="text-muted">Quantity questions use unit pricing instead of preset options.</p>
                @else
                    <p class="text-muted">No options available</p>
                @endif

            </div>

        </div>
    </div>

@endsection
