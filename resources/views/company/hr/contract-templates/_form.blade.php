@php
    $template = $contractTemplate ?? null;
@endphp

<div class="contract-template-grid">

    <div class="contract-template-field">
        <label>Template Name *</label>

        <input type="text"
               name="name"
               value="{{ old(
                   'name',
                   optional($template)->name
               ) }}"
               required>

        @error('name')
            <div class="contract-template-error">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="contract-template-field">
        <label>Employment Type</label>

        <select name="employment_type">
            <option value="">All employment types</option>

            @foreach ([
                'full_time' => 'Full Time',
                'part_time' => 'Part Time',
                'temporary' => 'Temporary',
                'contractor' => 'Contractor',
                'apprentice' => 'Apprentice',
                'zero_hours' => 'Zero Hours',
            ] as $value => $label)

                <option value="{{ $value }}"
                    {{ old(
                        'employment_type',
                        optional($template)->employment_type
                    ) === $value
                        ? 'selected'
                        : ''
                    }}>
                    {{ $label }}
                </option>

            @endforeach
        </select>

        @error('employment_type')
            <div class="contract-template-error">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="
        contract-template-field
        contract-template-field-full
    ">
        <label>Description</label>

        <textarea name="description"
                  rows="3">{{ old(
            'description',
            optional($template)->description
        ) }}</textarea>

        @error('description')
            <div class="contract-template-error">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="
        contract-template-field
        contract-template-field-full
    ">
        <label>Contract Content *</label>

        <textarea name="content"
                  rows="24"
                  required>{{ old(
            'content',
            optional($template)->content
        ) }}</textarea>

        @error('content')
            <div class="contract-template-error">
                {{ $message }}
            </div>
        @enderror

        <div class="contract-template-help">
            Use HTML and placeholders such as
            @verbatim
                {{employee_full_name}},
                {{job_title}},
                {{start_date}},
                {{weekly_hours}},
                {{pay_rate}}
            @endverbatim
        </div>
    </div>

    <div class="contract-template-field">
        <label class="contract-template-checkbox">
            <input type="checkbox"
                   name="is_default"
                   value="1"
                   {{ old(
                       'is_default',
                       optional($template)->is_default
                   ) ? 'checked' : '' }}>

            Default template
        </label>
    </div>

    <div class="contract-template-field">
        <label class="contract-template-checkbox">
            <input type="checkbox"
                   name="is_active"
                   value="1"
                   {{ old(
                       'is_active',
                       optional($template)->exists
                           ? optional($template)->is_active
                           : true
                   ) ? 'checked' : '' }}>

            Active template
        </label>
    </div>

</div>