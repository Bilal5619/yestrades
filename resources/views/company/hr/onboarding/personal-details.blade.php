@extends('company.layouts.app')

@section('title', 'Personal Details & Bank Details')

@section('content')

<style>
    .personal-details-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .personal-details-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .personal-details-header p {
        margin: 0;
        color: #aab4ca;
    }

    .personal-details-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 15px;
        border-radius: 7px;
        background: #3a4967;
        color: #ffffff;
        text-decoration: none;
    }

    .personal-details-card {
        padding: 24px;
        margin-bottom: 22px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .personal-details-section-title {
        margin: 0 0 6px;
        font-size: 20px;
    }

    .personal-details-section-description {
        margin: 0 0 20px;
        color: #aab4ca;
        font-size: 13px;
    }

    .personal-details-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .personal-details-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .personal-details-field-full {
        grid-column: 1 / -1;
    }

    .personal-details-field label {
        color: #c9d2e5;
        font-size: 13px;
        font-weight: 600;
    }

    .personal-details-field input,
    .personal-details-field select,
    .personal-details-field textarea {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
        box-sizing: border-box;
    }

    .personal-details-field textarea {
        min-height: 100px;
        resize: vertical;
    }

    .personal-details-error {
        color: #ff8a92;
        font-size: 12px;
    }

    .personal-details-warning {
        padding: 14px 16px;
        margin-bottom: 20px;
        border: 1px solid rgba(255, 179, 71, 0.25);
        border-radius: 8px;
        background: rgba(255, 179, 71, 0.08);
        color: #ffc46f;
        font-size: 13px;
        line-height: 1.6;
    }

    .personal-details-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
    }

    .personal-details-save {
        min-height: 42px;
        padding: 0 18px;
        border: 0;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    @media (max-width: 750px) {
        .personal-details-grid {
            grid-template-columns: 1fr;
        }

        .personal-details-field-full {
            grid-column: auto;
        }

        .personal-details-header {
            flex-direction: column;
        }
    }
</style>

@php
    $details = $onboarding->personalDetails;
    $employee = $onboarding->employee;

    $employeeFullName = trim(
        ($employee->first_name ?? '')
        . ' '
        . ($employee->last_name ?? '')
    );

    $employeeEmail =
        $employee->personal_email
        ?? $employee->email
        ?? null;

    $employeePhone =
        $employee->phone
        ?? $employee->phone_number
        ?? null;

    $employeeAddress =
        $employee->address
        ?? $employee->home_address
        ?? null;

    $emergencyName =
        $employee->emergency_contact_name
        ?? $employee->emergency_name
        ?? null;

    $emergencyRelationship =
        $employee->emergency_contact_relationship
        ?? $employee->emergency_relationship
        ?? null;

    $emergencyPhone =
        $employee->emergency_contact_phone
        ?? $employee->emergency_phone
        ?? null;

    $niNumber =
        $employee->national_insurance_number
        ?? $employee->ni_number
        ?? null;
@endphp

<div class="personal-details-header">

    <div>
        <h1>Personal Details & Bank Details</h1>

        <p>
            Review and verify personal and payment information for
            {{ $employeeFullName }}.
        </p>
    </div>

    <a href="{{ route(
            'company.hr.onboarding.show',
            $onboarding
        ) }}"
       class="personal-details-back">

        <i class="fas fa-arrow-left"></i>
        Employee Onboarding
    </a>

</div>

@if ($errors->any())
    <div class="personal-details-warning">
        Some information could not be saved. Check the highlighted fields
        and try again.
    </div>
@endif

<form method="POST"
      action="{{ route(
          'company.hr.onboarding.personal-details.update',
          $onboarding
      ) }}">

    @csrf
    @method('PUT')

    <div class="personal-details-card">

        <h2 class="personal-details-section-title">
            Personal Information
        </h2>

        <p class="personal-details-section-description">
            Confirm the employee’s identity and contact details.
        </p>

        <div class="personal-details-grid">

            <div class="personal-details-field">
                <label>Full Name *</label>

                <input type="text"
                       name="full_name"
                       value="{{ old(
                           'full_name',
                           optional($details)->full_name
                               ?? $employeeFullName
                       ) }}"
                       required>

                @error('full_name')
                    <div class="personal-details-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="personal-details-field">
                <label>Date of Birth</label>

                <input type="date"
                       name="date_of_birth"
                       value="{{ old(
                           'date_of_birth',
                           optional(
                               optional($details)->date_of_birth
                           )->format('Y-m-d')
                               ?? optional(
                                   $employee->date_of_birth
                               )->format('Y-m-d')
                       ) }}">

                @error('date_of_birth')
                    <div class="personal-details-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="personal-details-field">
                <label>Personal Email</label>

                <input type="email"
                       name="personal_email"
                       value="{{ old(
                           'personal_email',
                           optional($details)->personal_email
                               ?? $employeeEmail
                       ) }}">

                @error('personal_email')
                    <div class="personal-details-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="personal-details-field">
                <label>Phone Number</label>

                <input type="text"
                       name="phone"
                       value="{{ old(
                           'phone',
                           optional($details)->phone
                               ?? $employeePhone
                       ) }}">

                @error('phone')
                    <div class="personal-details-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="
                personal-details-field
                personal-details-field-full
            ">
                <label>Home Address</label>

                <textarea name="home_address">{{ old(
                    'home_address',
                    optional($details)->home_address
                        ?? $employeeAddress
                ) }}</textarea>

                @error('home_address')
                    <div class="personal-details-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="personal-details-field">
                <label>National Insurance Number</label>

                <input type="text"
                       name="national_insurance_number"
                       value="{{ old(
                           'national_insurance_number',
                           optional(
                               $details
                           )->national_insurance_number
                               ?? $niNumber
                       ) }}">

                @error('national_insurance_number')
                    <div class="personal-details-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

        </div>

    </div>

    <div class="personal-details-card">

        <h2 class="personal-details-section-title">
            Emergency Contact
        </h2>

        <p class="personal-details-section-description">
            Confirm who the company should contact in an emergency.
        </p>

        <div class="personal-details-grid">

            <div class="personal-details-field">
                <label>Contact Name</label>

                <input type="text"
                       name="emergency_contact_name"
                       value="{{ old(
                           'emergency_contact_name',
                           optional(
                               $details
                           )->emergency_contact_name
                               ?? $emergencyName
                       ) }}">

                @error('emergency_contact_name')
                    <div class="personal-details-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="personal-details-field">
                <label>Relationship</label>

                <input type="text"
                       name="emergency_contact_relationship"
                       value="{{ old(
                           'emergency_contact_relationship',
                           optional(
                               $details
                           )->emergency_contact_relationship
                               ?? $emergencyRelationship
                       ) }}">

                @error('emergency_contact_relationship')
                    <div class="personal-details-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="personal-details-field">
                <label>Emergency Phone Number</label>

                <input type="text"
                       name="emergency_contact_phone"
                       value="{{ old(
                           'emergency_contact_phone',
                           optional(
                               $details
                           )->emergency_contact_phone
                               ?? $emergencyPhone
                       ) }}">

                @error('emergency_contact_phone')
                    <div class="personal-details-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

        </div>

    </div>

    <div class="personal-details-card">

        <h2 class="personal-details-section-title">
            Bank Details
        </h2>

        <p class="personal-details-section-description">
            This is sensitive information and should only be available to
            authorised HR, payroll staff and the company owner.
        </p>

        <div class="personal-details-grid">

            <div class="personal-details-field">
                <label>Bank Name</label>

                <input type="text"
                       name="bank_name"
                       value="{{ old(
                           'bank_name',
                           optional($details)->bank_name
                               ?? $employee->bank_name
                       ) }}">

                @error('bank_name')
                    <div class="personal-details-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="personal-details-field">
                <label>Account Holder Name</label>

                <input type="text"
                       name="account_holder_name"
                       value="{{ old(
                           'account_holder_name',
                           optional($details)->account_holder_name
                               ?? $employee->account_holder_name
                               ?? $employeeFullName
                       ) }}">

                @error('account_holder_name')
                    <div class="personal-details-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="personal-details-field">
                <label>Sort Code</label>

                <input type="text"
                       name="sort_code"
                       placeholder="Example: 12-34-56"
                       value="{{ old(
                           'sort_code',
                           optional($details)->sort_code
                               ?? $employee->sort_code
                       ) }}">

                @error('sort_code')
                    <div class="personal-details-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="personal-details-field">
                <label>Account Number</label>

                <input type="text"
                       name="account_number"
                       value="{{ old(
                           'account_number',
                           optional($details)->account_number
                               ?? $employee->account_number
                       ) }}">

                @error('account_number')
                    <div class="personal-details-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

        </div>

    </div>

    <div class="personal-details-card">

        <h2 class="personal-details-section-title">
            Verification
        </h2>

        <p class="personal-details-section-description">
            Mark the section as verified only after checking the information.
        </p>

        <div class="personal-details-grid">

            <div class="personal-details-field">
                <label>Details Status *</label>

                <select name="details_verified" required>

                    <option value="0"
                        {{ (string) old(
                            'details_verified',
                            optional($details)->details_verified ?? 0
                        ) === '0'
                            ? 'selected'
                            : ''
                        }}>
                        Not Verified
                    </option>

                    <option value="1"
                        {{ (string) old(
                            'details_verified',
                            optional($details)->details_verified ?? 0
                        ) === '1'
                            ? 'selected'
                            : ''
                        }}>
                        Verified
                    </option>

                </select>

                @error('details_verified')
                    <div class="personal-details-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="
                personal-details-field
                personal-details-field-full
            ">
                <label>Notes</label>

                <textarea name="notes"
                          placeholder="Add any verification notes">{{ old(
                    'notes',
                    optional($details)->notes
                ) }}</textarea>

                @error('notes')
                    <div class="personal-details-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

        </div>

        @if (
            auth('company')->user()
                ->hasPermission('onboarding.manage')
        )
            <div class="personal-details-actions">

                <button type="submit"
                        class="personal-details-save">
                    Save Personal Details
                </button>

            </div>
        @endif

    </div>

</form>

@endsection