@php($shift = $shift ?? null)

<div class="shift-grid">
    <div class="shift-field">
        <label>Employee</label>
        <select name="employee_id" required>
            <option value="">Select employee</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}" @selected(old('employee_id', optional($shift)->employee_id) == $employee->id)>
                    {{ $employee->full_name }}
                </option>
            @endforeach
        </select>
        @error('employee_id')<div class="shift-error">{{ $message }}</div>@enderror
    </div>

    <div class="shift-field">
        <label>Shift Date</label>
        <input type="date" name="shift_date" value="{{ old('shift_date', optional($shift)->shift_date?->toDateString()) }}" required>
        @error('shift_date')<div class="shift-error">{{ $message }}</div>@enderror
    </div>

    <div class="shift-field">
        <label>Start Time</label>
        <input type="time" name="start_time" value="{{ old('start_time', optional($shift)->start_time) }}" required>
        @error('start_time')<div class="shift-error">{{ $message }}</div>@enderror
    </div>

    <div class="shift-field">
        <label>End Time</label>
        <input type="time" name="end_time" value="{{ old('end_time', optional($shift)->end_time) }}" required>
        @error('end_time')<div class="shift-error">{{ $message }}</div>@enderror
    </div>

    <div class="shift-field">
        <label>Break (minutes)</label>
        <input type="number" min="0" name="break_minutes" value="{{ old('break_minutes', optional($shift)->break_minutes ?? 0) }}">
        @error('break_minutes')<div class="shift-error">{{ $message }}</div>@enderror
    </div>

    <div class="shift-field">
        <label>Work Location</label>
        <input type="text" name="work_location" value="{{ old('work_location', optional($shift)->work_location) }}">
        @error('work_location')<div class="shift-error">{{ $message }}</div>@enderror
    </div>

    <div class="shift-field">
        <label>Status</label>
        <select name="status">
            @foreach (\App\Models\Company\EmployeeShift::STATUSES as $status)
                <option value="{{ $status }}" @selected(old('status', optional($shift)->status ?? 'scheduled') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('status')<div class="shift-error">{{ $message }}</div>@enderror
    </div>

    <div class="shift-field shift-field-full">
        <label>Notes</label>
        <textarea name="notes">{{ old('notes', optional($shift)->notes) }}</textarea>
        @error('notes')<div class="shift-error">{{ $message }}</div>@enderror
    </div>
</div>
