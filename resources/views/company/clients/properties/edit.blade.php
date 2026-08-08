@extends('company.layouts.app')

@section(
    'title',
    'Edit Property'
)

@section('content')

@include(
    'company.clients.partials.styles'
)

<div class="client-header">

    <div>

        <h1>Edit Property</h1>

        <p>
            {{ $client->name }}
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
        Back to Client
    </a>

</div>


<div class="client-card">

    <form
        method="POST"
        action="{{
            route(
                'company.clients.properties.update',
                [
                    $client,
                    $property
                ]
            )
        }}"
    >

        @csrf
        @method('PUT')


        @include(
            'company.clients.properties._form'
        )


        <div
            class="client-actions"
            style="
                justify-content:
                    space-between;
            "
        >

            <button
                type="submit"
                form="
                    property-delete-form
                "
                class="
                    client-button
                    client-button-danger
                "
            >
                Remove / Archive
            </button>


            <button
                type="submit"
                class="client-save"
            >
                Save Changes
            </button>

        </div>

    </form>


    <form
        id="property-delete-form"
        method="POST"
        action="{{
            route(
                'company.clients.properties.destroy',
                [
                    $client,
                    $property
                ]
            )
        }}"
        style="display:none;"
        onsubmit="
            return confirm(
                'Remove this property? If it has job history it will be archived instead.'
            );
        "
    >

        @csrf
        @method('DELETE')

    </form>

</div>

@endsection