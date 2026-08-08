@extends('company.layouts.app')

@section('title', 'Edit Client')

@section('content')

@include(
    'company.clients.partials.styles'
)

<div class="client-header">

    <div>
        <h1>
            Edit {{ $client->name }}
        </h1>
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
        Back
    </a>

</div>


<div class="client-card">

    <form
        method="POST"
        action="{{
            route(
                'company.clients.update',
                $client
            )
        }}"
    >

        @csrf
        @method('PUT')

        @include(
            'company.clients._form'
        )

        <div class="client-actions">

            <button
                type="submit"
                class="client-save"
            >
                Save Changes
            </button>

        </div>

    </form>

</div>

@endsection