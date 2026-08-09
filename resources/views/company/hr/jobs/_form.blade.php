@php($job = $job ?? null)

<div class="jobsheet-grid">

    {{-- CLIENT --}}
    <div class="jobsheet-field">
        <label>Client / Account</label>

        <select name="client_id" id="client_id">
            <option value="">Private / No Client Account</option>

            @foreach ($clients as $client)
                <option
                    value="{{ $client->id }}"
                    @selected(
                        old(
                            'client_id',
                            optional($job)->client_id
                        ) == $client->id
                    )
                >
                    {{ $client->name }}
                </option>
            @endforeach
        </select>

        @error('client_id')
            <div class="jobsheet-error">
                {{ $message }}
            </div>
        @enderror
        @if(
    auth('company')
        ->user()
        ->hasPermission(
            'jobs.manage'
        )
)

    <div style="
        margin-top:6px;
        font-size:12px;
    ">

        <a
            href="{{
                route(
                    'company.clients.create'
                )
            }}"
            style="
                color:#6fa8ee;
                text-decoration:none;
            "
        >
            + Add New Client / Partner
        </a>

    </div>

@endif
    </div>


    {{-- PROPERTY --}}
    <div class="jobsheet-field">
        <label>Client Property</label>

        <select
            name="client_property_id"
            id="client_property_id"
        >
            <option value="">
                Enter address manually
            </option>

            @foreach ($properties as $property)
                <option
                    value="{{ $property->id }}"
                    data-client="{{ $property->client_id }}"
                    @selected(
                        old(
                            'client_property_id',
                            optional($job)->client_property_id
                        ) == $property->id
                    )
                >
                    {{ optional($property->client)->name }}
                    —
                    {{ $property->full_address }}
                </option>
            @endforeach
        </select>

        @error('client_property_id')
            <div class="jobsheet-error">
                {{ $message }}
            </div>
        @enderror
        <div
    id="add-property-link"
    style="
        margin-top:6px;
        font-size:12px;
        display:none;
    "
>

    <a
        href="#"
        style="
            color:#6fa8ee;
            text-decoration:none;
        "
    >
        + Add Property for Selected Client
    </a>

</div>
    </div>


    {{-- TITLE --}}
    <div class="jobsheet-field">
        <label>Job / Service</label>

        <input
            type="text"
            name="title"
            value="{{ old('title', optional($job)->title) }}"
            placeholder="e.g. CP12 Gas Safety Certificate"
            required
        >

        @error('title')
            <div class="jobsheet-error">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- FIELD ENGINEER --}}
    <div class="jobsheet-field">
        <label>Assign Field Engineer</label>

        <select name="employee_id">
            <option value="">Unassigned</option>

            @foreach ($employees as $employee)
                <option
                    value="{{ $employee->id }}"
                    @selected(
                        old(
                            'employee_id',
                            optional($job)->employee_id
                        ) == $employee->id
                    )
                >
                    {{ $employee->full_name }}
                </option>
            @endforeach
        </select>

        @error('employee_id')
            <div class="jobsheet-error">
                {{ $message }}
            </div>
        @enderror
    </div>
<div class="jobsheet-field">

    <label>
        Appointment Status
    </label>

    <select
        name="appointment_status"
        id="appointment_status"
        required
    >

        <option
            value="not_confirmed"
            @selected(
                old(
                    'appointment_status',
                    optional($job)
                        ->appointment_status
                        ?? 'not_confirmed'
                )
                === 'not_confirmed'
            )
        >
            Date Not Confirmed
        </option>


        <option
            value="awaiting_customer"
            @selected(
                old(
                    'appointment_status',
                    optional($job)
                        ->appointment_status
                        ?? 'not_confirmed'
                )
                === 'awaiting_customer'
            )
        >
            Awaiting Customer / Tenant
        </option>


        <option
            value="proposed"
            @selected(
                old(
                    'appointment_status',
                    optional($job)
                        ->appointment_status
                        ?? 'not_confirmed'
                )
                === 'proposed'
            )
        >
            Date Proposed
        </option>


        <option
            value="confirmed"
            @selected(
                old(
                    'appointment_status',
                    optional($job)
                        ->appointment_status
                        ?? 'not_confirmed'
                )
                === 'confirmed'
            )
        >
            Confirmed
        </option>

    </select>

</div>

    <div class="jobsheet-field">

    <label>
        Scheduled Date
    </label>

    <input
        type="date"
        name="scheduled_date"
        id="scheduled_date"
        value="{{
            old(
                'scheduled_date',
                optional($job)
                    ->scheduled_date
                    ?->toDateString()
            )
        }}"
    >

    <small
        style="
            color:#aab4ca;
            font-size:11px;
        "
    >
        Leave blank if the date
        has not been confirmed.
    </small>

    @error('scheduled_date')
        <div class="jobsheet-error">
            {{ $message }}
        </div>
    @enderror

</div>
    {{-- TIME --}}
    <div class="jobsheet-field">
        <label>Start Time</label>

        <input
            type="time"
            name="scheduled_start_time"
            value="{{
                old(
                    'scheduled_start_time',
                    optional($job)->scheduled_start_time
                )
            }}"
        >
    </div>


    {{-- DURATION --}}
    <div class="jobsheet-field">
        <label>Expected Duration (minutes)</label>

        <input
            type="number"
            min="1"
            name="expected_duration_minutes"
            value="{{
                old(
                    'expected_duration_minutes',
                    optional($job)->expected_duration_minutes
                )
            }}"
        >
    </div>


    {{-- PRIORITY --}}
    <div class="jobsheet-field">
        <label>Priority</label>

        <select name="priority">
            @foreach (
                \App\Models\Company\EmployeeJob::PRIORITIES
                as $priority
            )
                <option
                    value="{{ $priority }}"
                    @selected(
                        old(
                            'priority',
                            optional($job)->priority ?? 'medium'
                        ) === $priority
                    )
                >
                    {{ ucfirst($priority) }}
                </option>
            @endforeach
        </select>
    </div>


    {{-- STATUS --}}
    <div class="jobsheet-field">
        <label>Job Status</label>

        <select name="status">
            @foreach (
                \App\Models\Company\EmployeeJob::STATUSES
                as $status
            )
                <option
                    value="{{ $status }}"
                    @selected(
                        old(
                            'status',
                            optional($job)->status ?? 'assigned'
                        ) === $status
                    )
                >
                    {{ ucwords(str_replace('_', ' ', $status)) }}
                </option>
            @endforeach
        </select>
    </div>


    {{-- CUSTOMER --}}
    <div class="jobsheet-field">
        <label>Site Contact Name</label>

        <input
            type="text"
            name="customer_name"
            value="{{
                old(
                    'customer_name',
                    optional($job)->customer_name
                )
            }}"
        >
    </div>


    <div class="jobsheet-field">
        <label>Site Contact Phone</label>

        <input
            type="text"
            name="customer_phone"
            value="{{
                old(
                    'customer_phone',
                    optional($job)->customer_phone
                )
            }}"
        >
    </div>


    <div class="jobsheet-field">
        <label>Site Contact Email</label>

        <input
            type="email"
            name="customer_email"
            value="{{
                old(
                    'customer_email',
                    optional($job)->customer_email
                )
            }}"
        >
    </div>


    <div class="jobsheet-field">
        <label>Site Postcode</label>

        <input
            type="text"
            name="site_postcode"
            value="{{
                old(
                    'site_postcode',
                    optional($job)->site_postcode
                )
            }}"
        >
    </div>


    <div class="jobsheet-field jobsheet-field-full">
        <label>Site Address</label>

        <input
            type="text"
            name="site_address"
            value="{{
                old(
                    'site_address',
                    optional($job)->site_address
                )
            }}"
        >
    </div>


    {{-- PAYMENT --}}
    <div class="jobsheet-field jobsheet-field-full">
        <hr>
        <h3>Payment / Invoice</h3>
    </div>


    <div class="jobsheet-field">
        <label>Payment Required?</label>

        <select name="payment_required">
            <option
                value="0"
                @selected(
                    !old(
                        'payment_required',
                        optional($job)->payment_required
                    )
                )
            >
                No
            </option>

            <option
                value="1"
                @selected(
                    old(
                        'payment_required',
                        optional($job)->payment_required
                    )
                )
            >
                Yes
            </option>
        </select>
    </div>


    <div class="jobsheet-field">
        <label>Agreed Amount (£)</label>

        <input
            type="number"
            step="0.01"
            min="0"
            name="agreed_amount"
            value="{{
                old(
                    'agreed_amount',
                    optional($job)->agreed_amount
                )
            }}"
        >
    </div>


    <div class="jobsheet-field">
        <label>Payment Method</label>

        <select name="payment_method">
            <option value="">Select</option>

            @foreach (
                \App\Models\Company\EmployeeJob::PAYMENT_METHODS
                as $method
            )
                <option
                    value="{{ $method }}"
                    @selected(
                        old(
                            'payment_method',
                            optional($job)->payment_method
                        ) === $method
                    )
                >
                    {{ ucwords(str_replace('_', ' ', $method)) }}
                </option>
            @endforeach
        </select>
    </div>


    <div class="jobsheet-field">
        <label>Payment Status</label>

        <select name="payment_status">
            @foreach (
                \App\Models\Company\EmployeeJob::PAYMENT_STATUSES
                as $status
            )
                <option
                    value="{{ $status }}"
                    @selected(
                        old(
                            'payment_status',
                            optional($job)->payment_status
                                ?? 'not_required'
                        ) === $status
                    )
                >
                    {{ ucwords(str_replace('_', ' ', $status)) }}
                </option>
            @endforeach
        </select>
    </div>


    <div class="jobsheet-field">
        <label>Invoice Number</label>

        <input
            type="text"
            name="invoice_number"
            value="{{
                old(
                    'invoice_number',
                    optional($job)->invoice_number
                )
            }}"
        >
    </div>


    {{-- CERTIFICATE --}}
    <div class="jobsheet-field jobsheet-field-full">
        <hr>
        <h3>Certificate</h3>
    </div>


    <div class="jobsheet-field">
        <label>Certificate Required?</label>

        <select name="certificate_required">

            <option
                value="0"
                @selected(
                    !old(
                        'certificate_required',
                        optional($job)->certificate_required
                    )
                )
            >
                No
            </option>

            <option
                value="1"
                @selected(
                    old(
                        'certificate_required',
                        optional($job)->certificate_required
                    )
                )
            >
                Yes
            </option>

        </select>
    </div>


    <div class="jobsheet-field">
        <label>Certificate Type</label>

        <select name="certificate_type">
            <option value="">Select</option>

            @foreach (
                \App\Models\Company\EmployeeJob::CERTIFICATE_TYPES
                as $type
            )
                <option
                    value="{{ $type }}"
                    @selected(
                        old(
                            'certificate_type',
                            optional($job)->certificate_type
                        ) === $type
                    )
                >
                    {{ strtoupper(
                        str_replace('_', ' ', $type)
                    ) }}
                </option>
            @endforeach
        </select>
    </div>


    <div class="jobsheet-field">
        <label>Certificate Status</label>

        <select name="certificate_status">
            @foreach (
                \App\Models\Company\EmployeeJob::CERTIFICATE_STATUSES
                as $status
            )
                <option
                    value="{{ $status }}"
                    @selected(
                        old(
                            'certificate_status',
                            optional($job)->certificate_status
                                ?? 'not_required'
                        ) === $status
                    )
                >
                    {{ ucwords(
                        str_replace('_', ' ', $status)
                    ) }}
                </option>
            @endforeach
        </select>
    </div>


    {{-- DESCRIPTION --}}
    <div class="jobsheet-field jobsheet-field-full">
        <label>Job Description / Instructions</label>

        <textarea name="description">{{
            old(
                'description',
                optional($job)->description
            )
        }}</textarea>
    </div>


    <div class="jobsheet-field jobsheet-field-full">
        <label>Internal Office Notes</label>

        <textarea name="internal_notes">{{
            old(
                'internal_notes',
                optional($job)->internal_notes
            )
        }}</textarea>
    </div>


    @if($job)
        <div class="jobsheet-field jobsheet-field-full">
            <label>Engineer Work Summary</label>

            <textarea name="work_summary">{{
                old(
                    'work_summary',
                    optional($job)->work_summary
                )
            }}</textarea>
        </div>


        <div class="jobsheet-field jobsheet-field-full">
            <label>Completion / Office Notes</label>

            <textarea name="completion_notes">{{
                old(
                    'completion_notes',
                    optional($job)->completion_notes
                )
            }}</textarea>
        </div>
    @endif

</div>

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const clientSelect =
            document.getElementById(
                'client_id'
            );

        const propertySelect =
            document.getElementById(
                'client_property_id'
            );

        const addPropertyLink =
            document.getElementById(
                'add-property-link'
            );


        if (
            !clientSelect
            ||
            !propertySelect
        ) {
            return;
        }


        const propertyOptions =
            Array.from(
                propertySelect.options
            )
            .filter(
                option =>
                    option.value
            )
            .map(
                option => ({
                    value:
                        option.value,

                    text:
                        option.text,

                    client:
                        option.dataset.client
                        || ''
                })
            );


        const selectedProperty =
            @json(
                old(
                    'client_property_id',
                    optional($job)
                        ->client_property_id
                )
            );


        function refreshProperties() {

            const clientId =
                clientSelect.value;


            const currentProperty =
                propertySelect.value
                ||
                selectedProperty
                ||
                '';


            propertySelect.innerHTML =
                '';


            const manualOption =
                document.createElement(
                    'option'
                );


            manualOption.value = '';

            manualOption.textContent =
                'Enter address manually';


            propertySelect.appendChild(
                manualOption
            );


            if (!clientId) {

                if (
                    addPropertyLink
                ) {
                    addPropertyLink
                        .style
                        .display =
                            'none';
                }

                return;
            }


            propertyOptions
                .filter(
                    item =>
                        String(
                            item.client
                        )
                        ===
                        String(
                            clientId
                        )
                )
                .forEach(
                    item => {

                        const option =
                            document
                                .createElement(
                                    'option'
                                );


                        option.value =
                            item.value;


                        option.textContent =
                            item.text;


                        option.dataset.client =
                            item.client;


                        if (
                            String(
                                item.value
                            )
                            ===
                            String(
                                currentProperty
                            )
                        ) {
                            option.selected =
                                true;
                        }


                        propertySelect
                            .appendChild(
                                option
                            );

                    }
                );


            if (
                addPropertyLink
            ) {

                addPropertyLink
                    .style
                    .display =
                        'block';


                const link =
                    addPropertyLink
                        .querySelector(
                            'a'
                        );


                link.href =
                    "{{
                        url(
                            '/company-own/clients'
                        )
                    }}/"
                    +
                    clientId
                    +
                    "/properties/create";
            }

        }


        clientSelect
            .addEventListener(
                'change',
                function () {

                    propertySelect.value =
                        '';

                    refreshProperties();

                }
            );


        refreshProperties();

    }
);
</script>