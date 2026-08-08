@extends('company.layouts.app')

@section('title', $client->name)

@section('content')

@include(
    'company.clients.partials.styles'
)

<div class="client-header">

    <div>
        <h1>{{ $client->name }}</h1>

        <p>
            {{
                ucwords(
                    str_replace(
                        '_',
                        ' ',
                        $client->client_type
                    )
                )
            }}
        </p>
    </div>


    <div style="
        display:flex;
        gap:10px;
        flex-wrap:wrap;
    ">

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
                        'company.clients.properties.create',
                        $client
                    )
                }}"
                class="client-save"
            >
                <i class="fas fa-house"></i>

                Add Property
            </a>


            <a
                href="{{
                    route(
                        'company.clients.edit',
                        $client
                    )
                }}"
                class="client-back"
            >
                <i class="fas fa-pen"></i>

                Edit Client
            </a>


            <a
                href="{{
                    route(
                        'company.hr.jobs.create',
                        [
                            'client_id'
                                => $client->id
                        ]
                    )
                }}"
                class="client-save"
            >
                <i class="fas fa-plus"></i>

                New Job
            </a>

        @endif

    </div>

</div>


{{-- STATISTICS --}}
<div class="client-stats">

    <div class="client-stat">

        <span>Total Jobs</span>

        <strong>
            {{ $stats['total_jobs'] }}
        </strong>

    </div>


    <div class="client-stat">

        <span>Open Jobs</span>

        <strong>
            {{ $stats['open_jobs'] }}
        </strong>

    </div>


    <div class="client-stat">

        <span>
            Outstanding Payment
        </span>

        <strong>
            £{{
                number_format(
                    $stats[
                        'outstanding_payment'
                    ],
                    2
                )
            }}
        </strong>

    </div>


    <div class="client-stat">

        <span>
            Certificates Pending
        </span>

        <strong>
            {{
                $stats[
                    'certificates_pending'
                ]
            }}
        </strong>

    </div>

</div>


{{-- ACCOUNT DETAILS --}}
<div class="client-card">

    <h3 style="margin-top:0;">
        Account Details
    </h3>


    <div class="client-detail-row">

        <div class="client-detail-label">
            Status
        </div>

        <div class="client-detail-value">

            <span class="
                client-status
                {{
                    $client->is_active
                        ? ''
                        : 'inactive'
                }}
            ">

                {{
                    $client->is_active
                        ? 'Active'
                        : 'Inactive'
                }}

            </span>

        </div>

    </div>


    <div class="client-detail-row">

        <div class="client-detail-label">
            Main Contact
        </div>

        <div class="client-detail-value">
            {{
                $client->contact_name
                    ?: '—'
            }}
        </div>

    </div>


    <div class="client-detail-row">

        <div class="client-detail-label">
            Phone
        </div>

        <div class="client-detail-value">
            {{
                $client->phone ?: '—'
            }}
        </div>

    </div>


    <div class="client-detail-row">

        <div class="client-detail-label">
            Email
        </div>

        <div class="client-detail-value">
            {{
                $client->email ?: '—'
            }}
        </div>

    </div>


    <div class="client-detail-row">

        <div class="client-detail-label">
            Billing / Office Address
        </div>

        <div class="client-detail-value">
            {{
                $client->full_address
                    ?: '—'
            }}
        </div>

    </div>


    <div class="client-detail-row">

        <div class="client-detail-label">
            Payment Terms
        </div>

        <div class="client-detail-value">
            {{
                $client->payment_terms
                    ?: '—'
            }}
        </div>

    </div>


    <div class="client-detail-row">

        <div class="client-detail-label">
            Internal Notes
        </div>

        <div
            class="client-detail-value"
            style="white-space:pre-line;"
        >
            {{
                $client->notes ?: '—'
            }}
        </div>

    </div>

</div>


{{-- PROPERTIES --}}
<div class="client-card">

    <div class="client-header">

        <div>

            <h3 style="margin:0 0 6px;">
                Properties / Sites
            </h3>

            <p>
                Saved properties for
                {{ $client->name }}.
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
                        'company.clients.properties.create',
                        $client
                    )
                }}"
                class="client-save"
            >
                + Add Property
            </a>

        @endif

    </div>


    <div style="overflow-x:auto;">

        <table class="client-table">

            <thead>
                <tr>
                    <th>Property</th>
                    <th>Address</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>


            <tbody>

                @forelse(
                    $client->properties
                    as $property
                )

                    <tr>

                        <td>
                            {{
                                $property
                                    ->property_name
                                    ?: 'Property'
                            }}
                        </td>


                        <td>
                            {{
                                $property
                                    ->full_address
                            }}
                        </td>


                        <td>

                            {{
                                $property
                                    ->contact_name
                                    ?: '—'
                            }}

                            @if(
                                $property
                                    ->contact_phone
                            )

                                <small>
                                    {{
                                        $property
                                            ->contact_phone
                                    }}
                                </small>

                            @endif

                        </td>


                        <td>

                            <span class="
                                client-status
                                {{
                                    $property
                                        ->is_active
                                        ? ''
                                        : 'inactive'
                                }}
                            ">

                                {{
                                    $property
                                        ->is_active
                                        ? 'Active'
                                        : 'Archived'
                                }}

                            </span>

                        </td>


                        <td>

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
                                            'company.clients.properties.edit',
                                            [
                                                $client,
                                                $property
                                            ]
                                        )
                                    }}"
                                >
                                    Edit
                                </a>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5">

                            No properties
                            saved yet.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>


{{-- JOB HISTORY --}}
<div class="client-card">

    <h3 style="margin-top:0;">
        Recent Jobs
    </h3>


    <div style="overflow-x:auto;">

        <table class="client-table">

            <thead>

                <tr>
                    <th>Reference</th>
                    <th>Date</th>
                    <th>Job</th>
                    <th>Property</th>
                    <th>Engineer</th>
                    <th>Payment</th>
                    <th>Certificate</th>
                </tr>

            </thead>


            <tbody>

                @forelse(
                    $client->jobs
                    as $job
                )

                    <tr>

                        <td>

                            <a
                                href="{{
                                    route(
                                        'company.hr.jobs.show',
                                        $job
                                    )
                                }}"
                            >
                                {{
                                    $job
                                        ->job_reference
                                        ?: 'Job #'
                                            .$job->id
                                }}
                            </a>

                        </td>


                        <td>
                            {{
                                optional(
                                    $job
                                        ->scheduled_date
                                )
                                ->format(
                                    'd M Y'
                                )
                            }}
                        </td>


                        <td>
                            {{ $job->title }}
                        </td>


                        <td>

                            {{
                                optional(
                                    $job->property
                                )
                                ->property_name
                                ?: $job
                                    ->site_postcode
                                ?: '—'
                            }}

                        </td>


                        <td>

                            {{
                                optional(
                                    $job->employee
                                )
                                ->full_name
                                ?: 'Unassigned'
                            }}

                        </td>


                        <td>

                            {{
                                ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $job
                                            ->payment_status
                                    )
                                )
                            }}

                        </td>


                        <td>

                            {{
                                ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $job
                                            ->certificate_status
                                    )
                                )
                            }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7">
                            No jobs for this
                            client yet.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection