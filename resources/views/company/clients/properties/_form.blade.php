@php(
    $property =
        $property ?? null
)

<div class="client-grid">


    <div class="client-field">

        <label>
            Property / Site Name
        </label>

        <input
            type="text"
            name="property_name"
            placeholder="
                e.g. Flat 2,
                Plot 8,
                Main Office
            "
            value="{{
                old(
                    'property_name',
                    optional(
                        $property
                    )->property_name
                )
            }}"
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
                        optional(
                            $property
                        )->is_active ?? 1
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
                        optional(
                            $property
                        )->is_active ?? 1
                    ) === '0'
                )
            >
                Archived
            </option>

        </select>

    </div>


    <div class="client-field">

        <label>
            Address Line 1 *
        </label>

        <input
            type="text"
            name="address_line_1"
            required
            value="{{
                old(
                    'address_line_1',
                    optional(
                        $property
                    )->address_line_1
                )
            }}"
        >

        @error(
            'address_line_1'
        )

            <div class="client-error">
                {{ $message }}
            </div>

        @enderror

    </div>


    <div class="client-field">

        <label>
            Address Line 2
        </label>

        <input
            type="text"
            name="address_line_2"
            value="{{
                old(
                    'address_line_2',
                    optional(
                        $property
                    )->address_line_2
                )
            }}"
        >

    </div>


    <div class="client-field">

        <label>City</label>

        <input
            type="text"
            name="city"
            value="{{
                old(
                    'city',
                    optional(
                        $property
                    )->city
                )
            }}"
        >

    </div>


    <div class="client-field">

        <label>County</label>

        <input
            type="text"
            name="county"
            value="{{
                old(
                    'county',
                    optional(
                        $property
                    )->county
                )
            }}"
        >

    </div>


    <div class="client-field">

        <label>Postcode</label>

        <input
            type="text"
            name="postcode"
            value="{{
                old(
                    'postcode',
                    optional(
                        $property
                    )->postcode
                )
            }}"
        >

    </div>


    <div class="client-field">

        <label>
            Site Contact Name
        </label>

        <input
            type="text"
            name="contact_name"
            value="{{
                old(
                    'contact_name',
                    optional(
                        $property
                    )->contact_name
                )
            }}"
        >

    </div>


    <div class="client-field">

        <label>
            Site Contact Phone
        </label>

        <input
            type="text"
            name="contact_phone"
            value="{{
                old(
                    'contact_phone',
                    optional(
                        $property
                    )->contact_phone
                )
            }}"
        >

    </div>


    <div class="client-field">

        <label>
            Site Contact Email
        </label>

        <input
            type="email"
            name="contact_email"
            value="{{
                old(
                    'contact_email',
                    optional(
                        $property
                    )->contact_email
                )
            }}"
        >

    </div>


    <div class="
        client-field
        client-field-full
    ">

        <label>
            Access Notes
        </label>

        <textarea
            name="access_notes"
            placeholder="
                Tenant contact,
                key location,
                parking,
                access instructions...
            "
        >{{
            old(
                'access_notes',
                optional(
                    $property
                )->access_notes
            )
        }}</textarea>

    </div>

</div>