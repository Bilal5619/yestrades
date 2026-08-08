@php($client = $client ?? null)

<div class="client-grid">

    <div class="client-field">
        <label>
            Client / Company Name *
        </label>

        <input
            type="text"
            name="name"
            required
            value="{{ old(
                'name',
                optional($client)->name
            ) }}"
            placeholder="e.g. RE/MAX"
        >

        @error('name')
            <div class="client-error">
                {{ $message }}
            </div>
        @enderror
    </div>


    <div class="client-field">
    <label>Client Type *</label>

    <select
        name="client_type"
        required
    >
        <option
            value="company"
            @selected(
                old(
                    'client_type',
                    optional($client)->client_type ?? 'company'
                ) === 'company'
            )
        >
            Company
        </option>

        <option
            value="letting_agent"
            @selected(
                old(
                    'client_type',
                    optional($client)->client_type ?? 'company'
                ) === 'letting_agent'
            )
        >
            Letting Agent
        </option>

        <option
            value="estate_agent"
            @selected(
                old(
                    'client_type',
                    optional($client)->client_type ?? 'company'
                ) === 'estate_agent'
            )
        >
            Estate Agent
        </option>

        <option
            value="contractor"
            @selected(
                old(
                    'client_type',
                    optional($client)->client_type ?? 'company'
                ) === 'contractor'
            )
        >
            Contractor / Construction Company
        </option>

        <option
            value="landlord"
            @selected(
                old(
                    'client_type',
                    optional($client)->client_type ?? 'company'
                ) === 'landlord'
            )
        >
            Landlord
        </option>

        <option
            value="property_manager"
            @selected(
                old(
                    'client_type',
                    optional($client)->client_type ?? 'company'
                ) === 'property_manager'
            )
        >
            Property Manager
        </option>

        <option
            value="private_regular"
            @selected(
                old(
                    'client_type',
                    optional($client)->client_type ?? 'company'
                ) === 'private_regular'
            )
        >
            Regular Private Client
        </option>

        <option
            value="other"
            @selected(
                old(
                    'client_type',
                    optional($client)->client_type ?? 'company'
                ) === 'other'
            )
        >
            Other
        </option>
    </select>

    @error('client_type')
        <div class="client-error">
            {{ $message }}
        </div>
    @enderror
</div>

    <div class="client-field">
        <label>Main Contact Name</label>

        <input
            type="text"
            name="contact_name"
            value="{{ old(
                'contact_name',
                optional($client)
                    ->contact_name
            ) }}"
        >
    </div>


    <div class="client-field">
        <label>Phone</label>

        <input
            type="text"
            name="phone"
            value="{{ old(
                'phone',
                optional($client)->phone
            ) }}"
        >
    </div>


    <div class="client-field">
        <label>Email</label>

        <input
            type="email"
            name="email"
            value="{{ old(
                'email',
                optional($client)->email
            ) }}"
        >
    </div>


    <div class="client-field">
        <label>Payment Terms</label>

        <input
            type="text"
            name="payment_terms"
            placeholder="e.g. 30 days"
            value="{{ old(
                'payment_terms',
                optional($client)
                    ->payment_terms
            ) }}"
        >
    </div>


    <div class="client-field">
        <label>Address Line 1</label>

        <input
            type="text"
            name="address_line_1"
            value="{{ old(
                'address_line_1',
                optional($client)
                    ->address_line_1
            ) }}"
        >
    </div>


    <div class="client-field">
        <label>Address Line 2</label>

        <input
            type="text"
            name="address_line_2"
            value="{{ old(
                'address_line_2',
                optional($client)
                    ->address_line_2
            ) }}"
        >
    </div>


    <div class="client-field">
        <label>City</label>

        <input
            type="text"
            name="city"
            value="{{ old(
                'city',
                optional($client)->city
            ) }}"
        >
    </div>


    <div class="client-field">
        <label>County</label>

        <input
            type="text"
            name="county"
            value="{{ old(
                'county',
                optional($client)->county
            ) }}"
        >
    </div>


    <div class="client-field">
        <label>Postcode</label>

        <input
            type="text"
            name="postcode"
            value="{{ old(
                'postcode',
                optional($client)->postcode
            ) }}"
        >
    </div>


    <div class="client-field">
        <label>Status</label>

        <select name="is_active">

            <option
                value="1"
                @selected(
                    (string) old(
                        'is_active',
                        optional($client)
                            ->is_active ?? 1
                    ) === '1'
                )
            >
                Active
            </option>

            <option
                value="0"
                @selected(
                    (string) old(
                        'is_active',
                        optional($client)
                            ->is_active ?? 1
                    ) === '0'
                )
            >
                Inactive / Archived
            </option>

        </select>
    </div>


    <div class="
        client-field
        client-field-full
    ">
        <label>Internal Notes</label>

        <textarea
            name="notes"
        >{{ old(
            'notes',
            optional($client)->notes
        ) }}</textarea>
    </div>

</div>