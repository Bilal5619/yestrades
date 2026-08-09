@php($visit = $visit ?? null)

<div class="jobsheet-grid">

    <div class="jobsheet-field">

        <label>
            Field Engineer
        </label>

        <select name="employee_id">

            <option value="">
                Unassigned
            </option>

            @foreach(
                $employees as $employee
            )

                <option
                    value="{{ $employee->id }}"
                    @selected(
                        old(
                            'employee_id',
                            optional($visit)
                                ->employee_id
                        )
                        == $employee->id
                    )
                >

                    {{
                        $employee
                            ->full_name
                    }}

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
            Visit Date *
        </label>

        <input
            type="date"
            name="visit_date"
            required
            value="{{
                old(
                    'visit_date',
                    optional($visit)
                        ->visit_date
                        ?->toDateString()
                )
            }}"
        >

        @error('visit_date')
            <div class="jobsheet-error">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="jobsheet-field">

        <label>
            Start Time
        </label>

        <input
            type="time"
            name="start_time"
            value="{{
                old(
                    'start_time',
                    optional($visit)
                        ->start_time
                )
            }}"
        >

    </div>


    <div class="jobsheet-field">

        <label>
            Expected Duration
            (minutes)
        </label>

        <input
            type="number"
            min="1"
            name="
                expected_duration_minutes
            "
            value="{{
                old(
                    'expected_duration_minutes',
                    optional($visit)
                        ->expected_duration_minutes
                )
            }}"
        >

    </div>


    <div class="jobsheet-field">

        <label>
            Visit Status
        </label>

        <select name="status">

            @foreach(
                \App\Models\Company\EmployeeJobVisit::STATUSES
                as $status
            )

                <option
                    value="{{ $status }}"
                    @selected(
                        old(
                            'status',
                            optional($visit)
                                ->status
                                ?? 'scheduled'
                        )
                        === $status
                    )
                >

                    {{
                        ucwords(
                            str_replace(
                                '_',
                                ' ',
                                $status
                            )
                        )
                    }}

                </option>

            @endforeach

        </select>

    </div>


    <div
        class="
            jobsheet-field
            jobsheet-field-full
        "
    >

        <label>
            Visit Notes
        </label>

        <textarea
            name="notes"
        >{{
            old(
                'notes',
                optional($visit)
                    ->notes
            )
        }}</textarea>

    </div>

</div>