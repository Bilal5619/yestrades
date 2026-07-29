@extends('company.layouts.app')

@section('title', 'Upload Payslip')

@section('content')

<style>
    .payslip-form-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .payslip-form-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .payslip-form-header p {
        margin: 0;
        color: #aab4ca;
    }

    .payslip-form-back {
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

    .payslip-form-card {
        max-width: 950px;
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .payslip-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .payslip-form-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .payslip-form-field-full {
        grid-column: 1 / -1;
    }

    .payslip-form-field label {
        color: #c9d2e5;
        font-size: 13px;
        font-weight: 600;
    }

    .payslip-form-field input,
    .payslip-form-field select,
    .payslip-form-field textarea {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
        box-sizing: border-box;
    }

    .payslip-form-field textarea {
        min-height: 110px;
        resize: vertical;
    }

    .payslip-form-error {
        color: #ff8a92;
        font-size: 12px;
    }

    .payslip-form-warning {
        padding: 14px 16px;
        margin-bottom: 20px;
        border-radius: 8px;
        background: rgba(255,179,71,0.08);
        color: #ffc46f;
        font-size: 13px;
    }

    .payslip-form-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 24px;
    }

    .payslip-form-save {
        min-height: 42px;
        padding: 0 18px;
        border: 0;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    @media (max-width: 700px) {
        .payslip-form-grid {
            grid-template-columns: 1fr;
        }

        .payslip-form-field-full {
            grid-column: auto;
        }

        .payslip-form-header {
            flex-direction: column;
        }
    }
</style>

<div class="payslip-form-header">

    <div>
        <h1>Upload Payslip</h1>

        <p>
            Upload a private PDF payslip for an employee.
        </p>
    </div>

    <a href="{{ route(
            'company.hr.payslips.index'
        ) }}"
       class="payslip-form-back">

        <i class="fas fa-arrow-left"></i>
        Payslip Management
    </a>

</div>

<div class="payslip-form-warning">
    Payslips contain sensitive payroll information. Only authorised
    users should upload or access these files.
</div>

<div class="payslip-form-card">

    <form method="POST"
          enctype="multipart/form-data"
          action="{{ route(
              'company.hr.payslips.store'
          ) }}">

        @csrf

        <div class="payslip-form-grid">

            <div class="
                payslip-form-field
                payslip-form-field-full
            ">
                <label>Employee *</label>

                <select name="employee_id" required>
                    <option value="">
                        Select employee
                    </option>

                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}"
                            {{ (string) old(
                                'employee_id',
                                optional($selectedEmployee)->id
                            ) === (string) $employee->id
                                ? 'selected'
                                : ''
                            }}>

                            {{ trim(
                                $employee->first_name
                                . ' '
                                . $employee->last_name
                            ) }}

                            — {{ $employee->employee_number }}
                        </option>
                    @endforeach
                </select>

                @error('employee_id')
                    <div class="payslip-form-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="payslip-form-field">
                <label>Pay Period *</label>

                <input type="text"
                       name="pay_period"
                       placeholder="Example: July 2026"
                       value="{{ old('pay_period') }}"
                       required>

                @error('pay_period')
                    <div class="payslip-form-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="payslip-form-field">
                <label>Payment Date *</label>

                <input type="date"
                       name="payment_date"
                       value="{{ old('payment_date') }}"
                       required>

                @error('payment_date')
                    <div class="payslip-form-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="
                payslip-form-field
                payslip-form-field-full
            ">
                <label>Payslip PDF *</label>

                <input type="file"
                       name="payslip_file"
                       accept=".pdf,application/pdf"
                       required>

                @error('payslip_file')
                    <div class="payslip-form-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="
                payslip-form-field
                payslip-form-field-full
            ">
                <label>Notes</label>

                <textarea name="notes"
                          placeholder="Optional internal notes">{{ old(
                    'notes'
                ) }}</textarea>

                @error('notes')
                    <div class="payslip-form-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

        </div>

        <div class="payslip-form-actions">
            <button type="submit"
                    class="payslip-form-save">

                <i class="fas fa-upload"></i>
                Upload Payslip
            </button>
        </div>

    </form>

</div>

@endsection