@extends('company.layouts.app')

@section('title', 'Edit Contract Template')

@section('content')

@include(
    'company.hr.contract-templates.partials.styles'
)

<div class="contract-template-header">

    <div>
        <h1>Edit Contract Template</h1>

        <p>
            Update the template content and settings.
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

    <div class="contract-template-version">
        Current version:
        {{ $contractTemplate->version }}
    </div>

    <form method="POST"
          action="{{ route(
              'company.hr.contract-templates.update',
              $contractTemplate
          ) }}">

        @csrf
        @method('PUT')

        @include(
            'company.hr.contract-templates._form',
            [
                'contractTemplate' =>
                    $contractTemplate,
            ]
        )

        <div class="contract-template-actions">
            <button type="submit"
                    class="contract-template-save">
                Save Changes
            </button>
        </div>

    </form>

</div>

@endsection