@php($job = $job ?? null)

<div class="jobsheet-grid">
    <div class="jobsheet-field">
        <label>Job Title</label>
        <input type="text" name="title" value="{{ old('title', optional($job)->title) }}" required>
        @error('title')<div class="jobsheet-error">{{ $message }}</div>@enderror
    </div>

    <div class="jobsheet-field">
        <label>Assign to Employee</label>
        <select name="employee_id">
            <option value="">Unassigned</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}" @selected(old('employee_id', optional($job)->employee_id) == $employee->id)>
                    {{ $employee->full_name }}
                </option>
            @endforeach
        </select>
        @error('employee_id')<div class="jobsheet-error">{{ $message }}</div>@enderror
    </div>

    <div class="jobsheet-field">
        <label>Scheduled Date</label>
        <input type="date" name="scheduled_date" value="{{ old('scheduled_date', optional($job)->scheduled_date?->toDateString()) }}" required>
        @error('scheduled_date')<div class="jobsheet-error">{{ $message }}</div>@enderror
    </div>

    <div class="jobsheet-field">
        <label>Start Time</label>
        <input type="time" name="scheduled_start_time" value="{{ old('scheduled_start_time', optional($job)->scheduled_start_time) }}">
        @error('scheduled_start_time')<div class="jobsheet-error">{{ $message }}</div>@enderror
    </div>

    <div class="jobsheet-field">
        <label>Expected Duration (minutes)</label>
        <input type="number" min="1" name="expected_duration_minutes" value="{{ old('expected_duration_minutes', optional($job)->expected_duration_minutes) }}">
        @error('expected_duration_minutes')<div class="jobsheet-error">{{ $message }}</div>@enderror
    </div>

    <div class="jobsheet-field">
        <label>Priority</label>
        <select name="priority">
            @foreach (\App\Models\Company\EmployeeJob::PRIORITIES as $priority)
                <option value="{{ $priority }}" @selected(old('priority', optional($job)->priority ?? 'medium') === $priority)>{{ ucfirst($priority) }}</option>
            @endforeach
        </select>
        @error('priority')<div class="jobsheet-error">{{ $message }}</div>@enderror
    </div>

    <div class="jobsheet-field">
        <label>Status</label>
        <select name="status">
            @foreach (\App\Models\Company\EmployeeJob::STATUSES as $status)
                <option value="{{ $status }}" @selected(old('status', optional($job)->status ?? 'assigned') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
            @endforeach
        </select>
        @error('status')<div class="jobsheet-error">{{ $message }}</div>@enderror
    </div>

    <div class="jobsheet-field">
        <label>Customer Name</label>
        <input type="text" name="customer_name" value="{{ old('customer_name', optional($job)->customer_name) }}">
        @error('customer_name')<div class="jobsheet-error">{{ $message }}</div>@enderror
    </div>

    <div class="jobsheet-field">
        <label>Customer Phone</label>
        <input type="text" name="customer_phone" value="{{ old('customer_phone', optional($job)->customer_phone) }}">
        @error('customer_phone')<div class="jobsheet-error">{{ $message }}</div>@enderror
    </div>

    <div class="jobsheet-field">
        <label>Site Postcode</label>
        <input type="text" name="site_postcode" value="{{ old('site_postcode', optional($job)->site_postcode) }}">
        @error('site_postcode')<div class="jobsheet-error">{{ $message }}</div>@enderror
    </div>

    <div class="jobsheet-field jobsheet-field-full">
        <label>Site Address</label>
        <input type="text" name="site_address" value="{{ old('site_address', optional($job)->site_address) }}">
        @error('site_address')<div class="jobsheet-error">{{ $message }}</div>@enderror
    </div>

    <div class="jobsheet-field jobsheet-field-full">
        <label>Job Description / Instructions</label>
        <textarea name="description">{{ old('description', optional($job)->description) }}</textarea>
        @error('description')<div class="jobsheet-error">{{ $message }}</div>@enderror
    </div>

    <div class="jobsheet-field jobsheet-field-full">
        <label>Internal Notes (not shown to customer)</label>
        <textarea name="internal_notes">{{ old('internal_notes', optional($job)->internal_notes) }}</textarea>
        @error('internal_notes')<div class="jobsheet-error">{{ $message }}</div>@enderror
    </div>
</div>
