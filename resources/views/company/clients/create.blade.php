@extends('company.layouts.app')

@section('title', 'Add Client')

@section('content')

@include(
    'company.clients.partials.styles'
)

<div class="client-header">

    <div>
        <h1>Add Client / Account</h1>

        <p>
            Add a regular business client,
            contractor, letting agent or
            landlord.
        </p>
    </div>

    <a
        href="{{
            route(
                'company.clients.index'
            )
        }}"
        class="client-back"
    >
        <i class="fas fa-arrow-left"></i>
        Clients
    </a>

</div>


<div class="client-card">

    <form
        method="POST"
        action="{{
            route(
                'company.clients.store'
            )
        }}"
    >

        @csrf

        @include(
            'company.clients._form'
        )

        <div class="client-actions">

            <button
                type="submit"
                class="client-save"
            >
                <i class="fas fa-save"></i>
                Save Client
            </button>

        </div>

    </form>

</div>

@endsection