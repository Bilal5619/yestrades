@extends('company.layouts.app')

@section('title', 'Clients')

@section('content')

@include(
    'company.clients.partials.styles'
)

<div class="client-header">

    <div>
        <h1>Clients / Accounts</h1>

        <p>
            Manage regular customers,
            companies, contractors and
            property partners.
        </p>
    </div>

    @if(
        auth('company')
            ->user()
            ->hasPermission(
                'jobs.manage'
            )
    )

        <a
            href="{{
                route(
                    'company.clients.create'
                )
            }}"
            class="client-save"
        >
            <i class="fas fa-plus"></i>
            Add Client
        </a>

    @endif

</div>


<div class="client-card">

    <form
        method="GET"
        class="client-search"
    >

        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="
                Search client,
                phone,
                email,
                postcode...
            "
        >

        <select name="status">

            <option value="">
                All Statuses
            </option>

            <option
                value="active"
                @selected(
                    request('status')
                        === 'active'
                )
            >
                Active
            </option>

            <option
                value="inactive"
                @selected(
                    request('status')
                        === 'inactive'
                )
            >
                Inactive
            </option>

        </select>

        <button
            class="client-save"
            type="submit"
        >
            Search
        </button>

    </form>


    <div style="
        overflow-x:auto;
    ">

        <table class="client-table">

            <thead>
                <tr>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Contact</th>
                    <th>Properties</th>
                    <th>Jobs</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                @forelse(
                    $clients as $client
                )

                    <tr>

                        <td>

                            <a
                                href="{{
                                    route(
                                        'company.clients.show',
                                        $client
                                    )
                                }}"
                            >
                                {{ $client->name }}
                            </a>

                            @if(
                                $client->postcode
                            )
                                <small>
                                    {{
                                        $client
                                            ->postcode
                                    }}
                                </small>
                            @endif

                        </td>


                        <td>
                            {{
                                ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $client
                                            ->client_type
                                    )
                                )
                            }}
                        </td>


                        <td>

                            {{
                                $client
                                    ->contact_name
                                    ?: '—'
                            }}

                            @if(
                                $client->phone
                            )
                                <small>
                                    {{
                                        $client
                                            ->phone
                                    }}
                                </small>
                            @endif

                        </td>


                        <td>
                            {{
                                $client
                                    ->properties_count
                            }}
                        </td>


                        <td>
                            {{
                                $client
                                    ->jobs_count
                            }}
                        </td>


                        <td>

                            <span class="
                                client-status
                                {{
                                    $client
                                        ->is_active
                                        ? ''
                                        : 'inactive'
                                }}
                            ">

                                {{
                                    $client
                                        ->is_active
                                        ? 'Active'
                                        : 'Inactive'
                                }}

                            </span>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6">
                            No clients added yet.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    <div style="
        margin-top:20px;
    ">
        {{ $clients->links() }}
    </div>

</div>

@endsection