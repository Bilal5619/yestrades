@extends('company.layouts.app')
@section('title', 'Edit Employee')
@section('content')

<style>
    .employee-create-header {
        margin-bottom: 24px;
    }

    .employee-create-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .employee-create-header p {
        margin: 0;
        color: #aab4ca;
    }

    .employee-form-section {
        margin-bottom: 22px;
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .employee-form-section h2 {
        margin: 0 0 20px;
        padding-bottom: 13px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        font-size: 19px;
    }

    .employee-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .employee-form-grid-three {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .employee-form-group {
        margin-bottom: 18px;
    }

    .employee-form-group label {
        display: block;
        margin-bottom: 7px;
        font-size: 14px;
    }

    .employee-required {
        color: #ff7c86;
    }

    .employee-form-control {
        width: 100%;
        min-height: 45px;
        padding: 11px 13px;
        border: 1px solid #3a4967;
        border-radius: 6px;
        background: #172036;
        color: #ffffff;
        outline: none;
    }

    .employee-form-control:focus {
        border-color: #2878dc;
    }

    textarea.employee-form-control {
        min-height: 110px;
        resize: vertical;
    }

    .employee-form-error {
        display: block;
        margin-top: 6px;
        color: #ff7c86;
        font-size: 13px;
    }

    .employee-form-note {
        margin-top: 6px;
        color: #aab4ca;
        font-size: 12px;
    }

    .employee-error-summary {
        margin-bottom: 20px;
        padding: 15px 17px;
        border-radius: 7px;
        background: rgba(236, 87, 98, 0.15);
        color: #ff929a;
    }

    .employee-form-actions {
        display: flex;
        gap: 12px;
        padding-bottom: 20px;
    }

    .employee-save-button,
    .employee-cancel-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 45px;
        padding: 0 19px;
        border: 0;
        border-radius: 7px;
        text-decoration: none;
        cursor: pointer;
    }

    .employee-save-button {
        background: #2878dc;
        color: #ffffff;
    }

    .employee-cancel-button {
        background: #3a4967;
        color: #ffffff;
    }

    @media (max-width: 900px) {
        .employee-form-grid,
        .employee-form-grid-three {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="employee-create-header">
   <h1>Edit Employee</h1>

    <p>
    Update personal, employment and contact details for
    {{ $employee->full_name }}.
</p>
</div>

@if ($errors->any())
    <div class="employee-error-summary">
        <strong>Please correct the form errors.</strong>

        <div style="margin-top:6px;">
            {{ $errors->first() }}
        </div>
    </div>
@endif

<form method="POST"
      action="{{ route(
          'company.hr.employees.update',
          $employee
      ) }}"
      enctype="multipart/form-data">

    @csrf
    @method('PUT')

    {{-- Personal information --}}
    <div class="employee-form-section">
        <h2>Personal Information</h2>

        <div class="employee-form-grid employee-form-grid-three">

            <div class="employee-form-group">
                <label for="employee_number">
                    Employee ID
                    <span class="employee-required">*</span>
                </label>

                <input
                    id="employee_number"
                    type="text"
                    name="employee_number"
                  value="{{ old(
                    'employee_number',
                    $employee->employee_number
                ) }}"
                    class="employee-form-control"
                    placeholder="Example: YT-0001"
                    required
                >

                @error('employee_number')
                    <span class="employee-form-error">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="employee-form-group">
                <label for="first_name">
                    First name
                    <span class="employee-required">*</span>
                </label>

                <input
                    id="first_name"
                    type="text"
                    name="first_name"
                    value="{{ old('first_name', $employee->first_name) }}"
                    class="employee-form-control"
                    required
                >

                @error('first_name')
                    <span class="employee-form-error">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="employee-form-group">
                <label for="middle_name">
                    Middle name
                </label>

                <input
                    id="middle_name"
                    type="text"
                    name="middle_name"
                    value="{{ old('middle_name', $employee->middle_name) }}"
                    class="employee-form-control"
                >
            </div>

            <div class="employee-form-group">
                <label for="last_name">
                    Last name
                    <span class="employee-required">*</span>
                </label>

                <input
                    id="last_name"
                    type="text"
                    name="last_name"
                    value="{{ old('last_name', $employee->last_name) }}"
                    class="employee-form-control"
                    required
                >

                @error('last_name')
                    <span class="employee-form-error">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="employee-form-group">
                <label for="date_of_birth">
                    Date of birth
                </label>

                <input
                    id="date_of_birth"
                    type="date"
                    name="date_of_birth"
                    value="{{ old('date_of_birth', optional($employee->date_of_birth)->format('Y-m-d')) }}"
                    class="employee-form-control"
                >

                @error('date_of_birth')
                    <span class="employee-form-error">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        <div class="employee-form-group">
            <label for="gender">
                Gender
            </label>

            <select
                id="gender"
                name="gender"
                class="employee-form-control"
            >
                <option value="">Select gender</option>

                <option value="male"
                    {{ old('gender', $employee->gender) === 'male'
                        ? 'selected'
                        : ''
                    }}>
                    Male
                </option>

                <option value="female"
                    {{ old('gender', $employee->gender) === 'female'
                        ? 'selected'
                        : ''
                    }}>
                    Female
                </option>

                <option value="other"
                    {{ old('gender', $employee->gender) === 'other'
                        ? 'selected'
                        : ''
                    }}>
                    Other
                </option>

                <option value="prefer_not_to_say"
                    {{ old(
                        'gender',
                        $employee->gender
                    ) === 'prefer_not_to_say'
                        ? 'selected'
                        : ''
                    }}>
                    Prefer not to say
                </option>
            </select>

            @error('gender')
                <span class="employee-form-error">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <div class="employee-form-group">
            <label for="national_insurance_number">
                National Insurance number
            </label>

            <input
                id="national_insurance_number"
                type="text"
                name="national_insurance_number"
                value="{{ old(
                    'national_insurance_number',
                    $employee->national_insurance_number
                ) }}"
                class="employee-form-control"
                placeholder="Example: QQ123456C"
            >

            @error('national_insurance_number')
                <span class="employee-form-error">
                    {{ $message }}
                </span>
            @enderror
        </div>

            <div class="employee-form-group">
                <label for="profile_photo">
                    Profile photograph
                </label>
                @if ($employee->profile_photo)
                    <div style="margin-bottom:12px;">
                        <img
                            src="{{ asset(
                                'storage/' . $employee->profile_photo
                            ) }}"
                            alt="{{ $employee->full_name }}"
                            style="
                                width:80px;
                                height:80px;
                                border-radius:50%;
                                object-fit:cover;
                            "
                        >
                    </div>
                @endif
                <input
                    id="profile_photo"
                    type="file"
                    name="profile_photo"
                    class="employee-form-control"
                    accept=".jpg,.jpeg,.png,.webp"
                >

                <div class="employee-form-note">
                    JPG, PNG or WebP. Maximum size 3 MB.
                </div>

                @error('profile_photo')
                    <span class="employee-form-error">
                        {{ $message }}
                    </span>
                @enderror
            </div>

        </div>
    </div>

    {{-- Contact details --}}
    <div class="employee-form-section">
        <h2>Contact Details</h2>

        <div class="employee-form-grid">

            <div class="employee-form-group">
                <label for="personal_email">
                    Personal email
                </label>

                <input
                    id="personal_email"
                    type="email"
                    name="personal_email"
                   value="{{ old('personal_email', $employee->personal_email) }}"
                    class="employee-form-control"
                >
            </div>

            <div class="employee-form-group">
                <label for="work_email">
                    Work email
                </label>

                <input
                    id="work_email"
                    type="email"
                    name="work_email"
                    value="{{ old('work_email', $employee->work_email) }}"
                    class="employee-form-control"
                >

                @error('work_email')
                    <span class="employee-form-error">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="employee-form-group">
                <label for="phone">
                    Phone number
                </label>

                <input
                    id="phone"
                    type="text"
                    name="phone"
                    value="{{ old('phone', $employee->phone) }}"
                    class="employee-form-control"
                >
            </div>

            <div class="employee-form-group">
                <label for="alternative_phone">
                    Alternative phone
                </label>

                <input
                    id="alternative_phone"
                    type="text"
                    name="alternative_phone"
                    value="{{ old('alternative_phone', $employee->alternative_phone) }}"
                    class="employee-form-control"
                >
            </div>

        </div>
    </div>

    {{-- Address --}}
    <div class="employee-form-section">
        <h2>Home Address</h2>

        <div class="employee-form-grid">

            <div class="employee-form-group">
                <label for="address_line_1">
                    Address line 1
                </label>

                <input
                    id="address_line_1"
                    type="text"
                    name="address_line_1"
                    value="{{ old('address_line_1', $employee->address_line_1) }}"
                    class="employee-form-control"
                >
            </div>

            <div class="employee-form-group">
                <label for="address_line_2">
                    Address line 2
                </label>

                <input
                    id="address_line_2"
                    type="text"
                    name="address_line_2"
                    value="{{ old('address_line_2', $employee->address_line_2) }}"
                    class="employee-form-control"
                >
            </div>

            <div class="employee-form-group">
                <label for="city">
                    Town or city
                </label>

                <input
                    id="city"
                    type="text"
                    name="city"
                    value="{{ old('city', $employee->city) }}"
                    class="employee-form-control"
                >
            </div>

            <div class="employee-form-group">
                <label for="county">
                    County
                </label>

                <input
                    id="county"
                    type="text"
                    name="county"
                    value="{{ old('county', $employee->county) }}"
                    class="employee-form-control"
                >
            </div>

            <div class="employee-form-group">
                <label for="postcode">
                    Postcode
                </label>

                <input
                    id="postcode"
                    type="text"
                    name="postcode"
                    value="{{ old('postcode', $employee->postcode) }}"
                    class="employee-form-control"
                >
            </div>

            <div class="employee-form-group">
                <label for="country">
                    Country
                </label>

                <input
                    id="country"
                    type="text"
                    name="country"
                    value="{{ old('country', $employee->country) }}"
                    class="employee-form-control"
                >
            </div>

        </div>
    </div>

    {{-- Employment details --}}
    <div class="employee-form-section">
        <h2>Employment Details</h2>

        <div class="employee-form-grid employee-form-grid-three">

            <div class="employee-form-group">
                <label for="job_title">
                    Job title
                    <span class="employee-required">*</span>
                </label>

                <input
                    id="job_title"
                    type="text"
                    name="job_title"
                   value="{{ old('job_title', $employee->job_title) }}"
                    class="employee-form-control"
                    required
                >

                @error('job_title')
                    <span class="employee-form-error">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="employee-form-group">
                <label for="department">
                    Department
                    <span class="employee-required">*</span>
                </label>

                <input
                    id="department"
                    type="text"
                    name="department"
                    value="{{ old('department', $employee->department) }}"
                    class="employee-form-control"
                    placeholder="Example: IT, HR or Operations"
                    required
                >

                @error('department')
                    <span class="employee-form-error">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="employee-form-group">
                <label for="manager_id">
                    Manager
                </label>

                <select
                    id="manager_id"
                    name="manager_id"
                    class="employee-form-control"
                >
                    <option value="">No manager assigned</option>

                    @foreach ($managers as $manager)
                        <option
                            value="{{ $manager->id }}"
                            {{ (string) old(
                                'manager_id',
                                $employee->manager_id
                            ) === (string) $manager->id
                                ? 'selected'
                                : ''
                            }}
                        >
                            {{ $manager->full_name }}
                            — {{ $manager->job_title ?: 'Employee' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="employee-form-group">
                <label for="employment_type">
                    Employment type
                    <span class="employee-required">*</span>
                </label>

                <select
                    id="employment_type"
                    name="employment_type"
                    class="employee-form-control"
                    required
                >
                    <option value="">Select type</option>
                    <option value="full_time"
                        {{ old(
                        'employment_type',
                        $employee->employment_type
                    ) === 'full_time' ? 'selected' : '' }}>
                        Full-time
                    </option>
                    <option value="part_time"
                                        {{ old(
                    'employment_type',
                    $employee->employment_type
                ) === 'part_time' ? 'selected' : '' }}>
                        Part-time
                    </option>
                    <option value="temporary"
                        {{ old(
                        'employment_type',
                        $employee->employment_type
                    ) === 'temporary' ? 'selected' : '' }}>
                        Temporary
                    </option>
                    <option value="contractor"
                        {{ old(
                        'employment_type',
                        $employee->employment_type
                    ) === 'contractor' ? 'selected' : '' }}>
                        Contractor
                    </option>
                    <option value="apprentice"
                        {{ old(
                            'employment_type',
                            $employee->employment_type
                        ) === 'apprentice' ? 'selected' : '' }}>
                        Apprentice
                    </option>
                    <option value="zero_hours"
                        {{ old(
                        'employment_type',
                        $employee->employment_type
                    ) === 'zero_hours' ? 'selected' : '' }}>
                        Zero-hours
                    </option>
                </select>

                @error('employment_type')
                    <span class="employee-form-error">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="employee-form-group">
                <label for="employment_status">
                    Employment status
                    <span class="employee-required">*</span>
                </label>

                <select
                    id="employment_status"
                    name="employment_status"
                    class="employee-form-control"
                    required
                >
                    <option value="onboarding"
                        {{ old(
                    'employment_status',
                    $employee->employment_status
                ) === 'onboarding' ? 'selected' : '' }}>
                        Onboarding
                    </option>

                    <option value="active"
                        {{ old(
                    'employment_status',
                    $employee->employment_status
                ) === 'active' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="on_leave"
                        {{ old(
                    'employment_status',
                    $employee->employment_status
                ) === 'on_leave' ? 'selected' : '' }}>
                        On leave
                    </option>

                    <option value="suspended"
                        {{ old(
                    'employment_status',
                    $employee->employment_status
                ) === 'suspended' ? 'selected' : '' }}>
                        Suspended
                    </option>

                    <option value="notice_period"
                        {{ old(
                    'employment_status',
                    $employee->employment_status
                ) === 'notice_period' ? 'selected' : '' }}>
                        Notice period
                    </option>

                    <option value="left_company"
                    {{ old(
                        'employment_status',
                        $employee->employment_status
                    ) === 'left_company' ? 'selected' : '' }}>
                    Left company
                </option>

                <option value="archived"
                    {{ old(
                        'employment_status',
                        $employee->employment_status
                    ) === 'archived' ? 'selected' : '' }}>
                    Archived
                </option>
                </select>
            </div>

            <div class="employee-form-group">
                <label for="start_date">
                    Start date
                    <span class="employee-required">*</span>
                </label>

                <input
                    id="start_date"
                    type="date"
                    name="start_date"
                    value="{{ old('start_date', optional($employee->start_date)->format('Y-m-d')) }}"
                    class="employee-form-control"
                    required
                >

                @error('start_date')
                    <span class="employee-form-error">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="employee-form-group">
                <label for="probation_end_date">
                    Probation end date
                </label>

                <input
                    id="probation_end_date"
                    type="date"
                    name="probation_end_date"
                   value="{{ old('probation_end_date', optional($employee->probation_end_date)->format('Y-m-d')) }}"
                    class="employee-form-control"
                >
            </div>

            <div class="employee-form-group">
                <label for="work_location">
                    Work location
                </label>

                <input
                    id="work_location"
                    type="text"
                    name="work_location"
                   value="{{ old('work_location', $employee->work_location) }}"
                    class="employee-form-control"
                    placeholder="Office, remote or site location"
                >
            </div>

            <div class="employee-form-group">
                <label for="normal_weekly_hours">
                    Normal weekly hours
                </label>

                <input
                    id="normal_weekly_hours"
                    type="number"
                    step="0.01"
                    min="0"
                    max="168"
                    name="normal_weekly_hours"
                    value="{{ old('normal_weekly_hours', $employee->normal_weekly_hours) }}"
                    class="employee-form-control"
                >
            </div>
            <div class="employee-form-group">
                <label for="leaving_date">
                    Leaving date
                </label>

                <input
                    id="leaving_date"
                    type="date"
                    name="leaving_date"
                    value="{{ old(
                        'leaving_date',
                        optional($employee->leaving_date)->format('Y-m-d')
                    ) }}"
                    class="employee-form-control"
                >

                @error('leaving_date')
                    <span class="employee-form-error">
                        {{ $message }}
                    </span>
                @enderror
            </div>

        </div>
    </div>
<div class="form-group">
    <label for="is_sponsored_worker">
        Sponsored worker
    </label>

    <select
        name="is_sponsored_worker"
        id="is_sponsored_worker"
        class="form-control"
    >
        <option value="0"
            {{ old(
                'is_sponsored_worker',
                isset($employee)
                    ? (int) $employee->is_sponsored_worker
                    : 0
            ) === 0 ? 'selected' : '' }}
        >
            No
        </option>

        <option value="1"
            {{ old(
                'is_sponsored_worker',
                isset($employee)
                    ? (int) $employee->is_sponsored_worker
                    : 0
            ) === 1 ? 'selected' : '' }}
        >
            Yes
        </option>
    </select>

    <small>
        Select Yes if this employee is sponsored under the
        company sponsor licence.
    </small>
</div>
    {{-- Pay details --}}
    <div class="employee-form-section">
        <h2>Pay and Payroll</h2>

        <div class="employee-form-note"
             style="margin-bottom:18px;">
            This is sensitive information. Access will be controlled
            by payroll permissions later.
        </div>

        <div class="employee-form-grid employee-form-grid-three">

            <div class="employee-form-group">
                <label for="pay_type">
                    Pay type
                </label>

                <select
                    id="pay_type"
                    name="pay_type"
                    class="employee-form-control"
                >
                    <option value="">Select pay type</option>
                    <option value="salary"
                        {{ old('pay_type', $employee->pay_type) === 'salary'
                            ? 'selected'
                            : ''
                        }}>
                        Annual salary
                    </option>
                    <option value="hourly"
                        {{ old('pay_type', $employee->pay_type) === 'hourly'
                        ? 'selected'
                        : ''
                    }}>
                        Hourly rate
                    </option>
                </select>
            </div>

            <div class="employee-form-group">
                <label for="pay_rate">
                    Salary or hourly amount
                </label>

                <input
                    id="pay_rate"
                    type="number"
                    name="pay_rate"
                    step="0.01"
                    min="0"
                   value="{{ old('pay_rate', $employee->pay_rate) }}"
                    class="employee-form-control"
                >
            </div>

            <div class="employee-form-group">
                <label for="payroll_number">
                    Payroll number
                </label>

                <input
                    id="payroll_number"
                    type="text"
                    name="payroll_number"
                    value="{{ old('payroll_number', $employee->payroll_number) }}"
                    class="employee-form-control"
                >
            </div>

        </div>
    </div>

    {{-- Emergency contact --}}
    <div class="employee-form-section">
        <h2>Emergency Contact</h2>

        <div class="employee-form-grid">

            <div class="employee-form-group">
                <label for="emergency_contact_name">
                    Contact name
                </label>

                <input
                    id="emergency_contact_name"
                    type="text"
                    name="emergency_contact_name"
                   value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}"
                    class="employee-form-control"
                >
            </div>

            <div class="employee-form-group">
                <label for="emergency_contact_relationship">
                    Relationship
                </label>

                <input
                    id="emergency_contact_relationship"
                    type="text"
                    name="emergency_contact_relationship"
                   value="{{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) }}"
                    class="employee-form-control"
                >
            </div>

            <div class="employee-form-group">
                <label for="emergency_contact_phone">
                    Phone number
                </label>

                <input
                    id="emergency_contact_phone"
                    type="text"
                    name="emergency_contact_phone"
                   value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}"
                    class="employee-form-control"
                >
            </div>

            <div class="employee-form-group">
                <label for="emergency_contact_alternative_phone">
                    Alternative phone
                </label>

                <input
                    id="emergency_contact_alternative_phone"
                    type="text"
                    name="emergency_contact_alternative_phone"
                    value="{{ old(
                        'emergency_contact_alternative_phone'
                    ) }}"
                    class="employee-form-control"
                >
            </div>

        </div>

        <div class="employee-form-group">
            <label for="emergency_contact_address">
                Contact address
            </label>

            <textarea
                id="emergency_contact_address"
                name="emergency_contact_address"
                class="employee-form-control"
            >{{ old(
    'emergency_contact_address',
    $employee->emergency_contact_address
) }}</textarea>
        </div>
    </div>

    {{-- Notes --}}
    <div class="employee-form-section">
        <h2>Employment Notes</h2>

        <div class="employee-form-group">
            <label for="employment_notes">
                Internal notes
            </label>

            <textarea
                id="employment_notes"
                name="employment_notes"
                class="employee-form-control"
                placeholder="Add relevant internal employment notes."
            >{{ old(
    'employment_notes',
    $employee->employment_notes
) }}</textarea>
        </div>
    </div>

    <div class="employee-form-actions">
        <button type="submit"
                class="employee-save-button">
            <i class="fas fa-save"
               style="margin-right:8px;"></i>
            Save Changes
        </button>

        <a href="{{ route(
        'company.hr.employees.show',
        $employee
            ) }}"
        class="employee-cancel-button">
            Cancel
        </a>
    </div>

</form>

@endsection