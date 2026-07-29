@extends('company.layouts.app')

@section('title', 'Add Contract Template')

@section('content')

@include(
    'company.hr.contract-templates.partials.styles'
)

<div class="contract-template-header">

    <div>
        <h1>Add Contract Template</h1>

        <p>
            Create an editable employment contract template.
        </p>
    </div>

    <a href="{{ route(
            'company.hr.contract-templates.index'
        ) }}"
       class="contract-template-back">

        <i class="fas fa-arrow-left"></i>
        Contract Templates
    </a>

</div>

<div class="contract-template-card">

    <form method="POST"
          action="{{ route(
              'company.hr.contract-templates.store'
          ) }}">

        @csrf

        @include(
            'company.hr.contract-templates._form'
        )

        <div class="contract-template-actions">
            <button type="submit"
                    class="contract-template-save">
                Create Template
            </button>
        </div>

    </form>

</div>

@endsection