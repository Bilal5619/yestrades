@extends('company.layouts.app')

@section('title', 'Payroll Setup & Payslips')

@section('content')

<style>
    .payroll-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .payroll-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .payroll-header p {
        margin: 0;
        color: #aab4ca;
    }

    .payroll-back {
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

    .payroll-card {
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .payroll-warning {
        padding: 14px 16px;
        margin-bottom: 20px;
        border: 1px solid rgba(255, 179, 71, 0.25);
        border-radius: 8px;
        background: rgba(255, 179, 71, 0.08);
        color: #ffc46f;
        font-size: 13px;
    }

    .payroll-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .payroll-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .payroll-field-full {
        grid-column: 1 / -1;
    }

    .payroll-field label {
        color: #c9d2e5;
        font-size: 13px;
        font-weight: 600;
    }

    .payroll-field input,
    .payroll-field select,
    .payroll-field textarea {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
        box-sizing: border-box;
    }

    .payroll-field textarea {
        min-height: 110px;
        resize: vertical;
    }

    .payroll-error {
        color: #ff8a92;
        font-size: 12px;
    }

    .payroll-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 24px;
    }

    .payroll-save {
        min-height: 42px;
        padding: 0 18px;
        border: 0;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    @media (max-width: 750px) {
        .payroll-grid {
            grid-template-columns: 1fr;
        }

        .payroll-field-full {
            grid-column: auto;
        }

        .payroll-header {
            flex-direction: column;
        }
    }
</style>

@php
    $payroll = $onboarding->payroll;
    $employee = $onboarding->employee;

    $employeePayType = old(
        'pay_type',
        optional($payroll)->pay_type
            ?? (
                in_array(
                    $employee->pay_type,
                    ['hourly', 'hourly_rate']
                )
                    ? 'hourly'
                    : 'salary'
            )
    );
@endphp

<div class="payroll-header">

    <div>
        <h1>Payroll Setup & Payslips</h1>

        <p>
            Confirm payroll information for
            {{ $employee->first_name }}
            {{ $employee->last_name }}.
        </p>
    </div>

    <a href="{{ route(
            'company.hr.onboarding.show',
            $onboarding
        ) }}"
       class="payroll-back">

        <i class="fas fa-arrow-left"></i>
        Employee Onboarding
    </a>

</div>

<div class="payroll-warning">
    Payroll and banking information is sensitive. Access should only be
    provided to authorised payroll staff, HR and the company owner.
</div>

<div class="payroll-card">

    <form method="POST"
          action="{{ route(
              'company.hr.onboarding.payroll.update',
              $onboarding
          ) }}">

        @csrf
        @method('PUT')

        <div class="payroll-grid">

            <div class="payroll-field">
                <label>Payroll Number</label>

                <input type="text"
                       name="payroll_number"
                       value="{{ old(
                           'payroll_number',
                           optional($payroll)->payroll_number
                               ?? $employee->payroll_number
                       ) }}">

                @error('payroll_number')
                    <div class="payroll-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="payroll-field">
                <label>Pay Type *</label>

                <select name="pay_type" required>

                    <option value="salary"
                        {{ $employeePayType === 'salary'
                            ? 'selected'
                            : ''
                        }}>
                        Annual Salary
                    </option>

                    <option value="hourly"
                        {{ $employeePayType === 'hourly'
                            ? 'selected'
                            : ''
                        }}>
                        Hourly Rate
                    </option>

                </select>

                @error('pay_type')
                    <div class="payroll-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="payroll-field">
                <label>Salary or Hourly Amount *</label>

                <input type="number"
                       step="0.01"
                       min="0"
                       name="pay_rate"
                       value="{{ old(
                           'pay_rate',
                           optional($payroll)->pay_rate
                               ?? $employee->pay_rate
                       ) }}"
                       required>

                @error('pay_rate')
                    <div class="payroll-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="payroll-field">
                <label>Pay Frequency *</label>

                <select name="pay_frequency" required>

                    @foreach ([
                        'weekly' => 'Weekly',
                        'fortnightly' => 'Fortnightly',
                        'four_weekly' => 'Every Four Weeks',
                        'monthly' => 'Monthly',
                    ] as $value => $label)

                        <option value="{{ $value }}"
                            {{ old(
                                'pay_frequency',
                                optional($payroll)->pay_frequency
                                    ?? 'monthly'
                            ) === $value
                                ? 'selected'
                                : ''
                            }}>
                            {{ $label }}
                        </option>

                    @endforeach

                </select>
            </div>

            <div class="payroll-field">
                <label>First Payment Date</label>

                <input type="date"
                       name="first_payment_date"
                       value="{{ old(
                           'first_payment_date',
                           optional(
                               optional($payroll)
                                   ->first_payment_date
                           )->format('Y-m-d')
                       ) }}">
            </div>

            <div class="payroll-field">
                <label>Bank Details Verified? *</label>

                <select name="bank_details_verified" required>
                    <option value="0"
                        {{ (string) old(
                            'bank_details_verified',
                            optional($payroll)
                                ->bank_details_verified ?? 0
                        ) === '0'
                            ? 'selected'
                            : ''
                        }}>
                        No
                    </option>

                    <option value="1"
                        {{ (string) old(
                            'bank_details_verified',
                            optional($payroll)
                                ->bank_details_verified ?? 0
                        ) === '1'
                            ? 'selected'
                            : ''
                        }}>
                        Yes
                    </option>
                </select>
            </div>

            <div class="payroll-field">
                <label>Tax Information Received? *</label>

                <select name="tax_information_received" required>
                    <option value="0"
                        {{ (string) old(
                            'tax_information_received',
                            optional($payroll)
                                ->tax_information_received ?? 0
                        ) === '0'
                            ? 'selected'
                            : ''
                        }}>
                        No
                    </option>

                    <option value="1"
                        {{ (string) old(
                            'tax_information_received',
                            optional($payroll)
                                ->tax_information_received ?? 0
                        ) === '1'
                            ? 'selected'
                            : ''
                        }}>
                        Yes
                    </option>
                </select>
            </div>

            <div class="payroll-field">
                <label>Pension Status</label>

                <select name="pension_status">
                    <option value="">Select status</option>

                    @foreach ([
                        'pending' => 'Pending',
                        'enrolled' => 'Enrolled',
                        'opted_out' => 'Opted Out',
                        'not_eligible' => 'Not Eligible',
                    ] as $value => $label)

                        <option value="{{ $value }}"
                            {{ old(
                                'pension_status',
                                optional($payroll)->pension_status
                            ) === $value
                                ? 'selected'
                                : ''
                            }}>
                            {{ $label }}
                        </option>

                    @endforeach
                </select>
            </div>

            <div class="payroll-field">
                <label>Payroll Setup Completed? *</label>

                <select name="payroll_setup_completed" required>
                    <option value="0"
                        {{ (string) old(
                            'payroll_setup_completed',
                            optional($payroll)
                                ->payroll_setup_completed ?? 0
                        ) === '0'
                            ? 'selected'
                            : ''
                        }}>
                        No
                    </option>

                    <option value="1"
                        {{ (string) old(
                            'payroll_setup_completed',
                            optional($payroll)
                                ->payroll_setup_completed ?? 0
                        ) === '1'
                            ? 'selected'
                            : ''
                        }}>
                        Yes
                    </option>
                </select>
            </div>

            <div class="
                payroll-field
                payroll-field-full
            ">
                <label>Notes</label>

                <textarea name="notes">{{ old(
                    'notes',
                    optional($payroll)->notes
                ) }}</textarea>
            </div>

        </div>

        @if (
            auth('company')->user()
                ->hasPermission('onboarding.manage')
        )
            <div class="payroll-actions">
                <button type="submit"
                        class="payroll-save">
                    Save Payroll Setup
                </button>
            </div>
        @endif

    </form>

</div>

@endsection