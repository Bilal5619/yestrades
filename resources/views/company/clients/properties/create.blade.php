@extends('company.layouts.app')

@section(
    'title',
    'Add Property'
)

@section('content')

@include(
    'company.clients.partials.styles'
)

<div class="client-header">

    <div>

        <h1>Add Property</h1>

        <p>
            Client:
            <strong>
                {{ $client->name }}
            </strong>
        </p>

    </div>


    <a
        href="{{
            route(
                'company.clients.show',
                $client
            )
        }}"
        class="client-back"
    >
        <i class="fas fa-arrow-left"></i>

        Back to Client
    </a>

</div>


<div class="client-card">

    <form
        method="POST"
        action="{{
            route(
                'company.clients.properties.store',
                $client
            )
        }}"
    >

        @csrf


        @include(
            'company.clients.properties._form'
        )


        <div class="client-actions">

            <button
                type="submit"
                class="client-save"
            >

                <i class="fas fa-save"></i>

                Save Property

            </button>

        </div>

    </form>

</div>

@endsection