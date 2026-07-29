@extends('company.layouts.app')

@section('title', 'Replace Payslip')

@section('content')

<style>
    .replace-payslip-header {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .replace-payslip-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .replace-payslip-header p {
        margin: 0;
        color: #aab4ca;
    }

    .replace-payslip-card {
        max-width: 900px;
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .replace-payslip-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
        margin-bottom: 18px;
    }

    .replace-payslip-field label {
        color: #c9d2e5;
        font-size: 13px;
        font-weight: 600;
    }

    .replace-payslip-field input,
    .replace-payslip-field textarea {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
        box-sizing: border-box;
    }

    .replace-payslip-field textarea {
        min-height: 100px;
        resize: vertical;
    }

    .replace-payslip-error {
        color: #ff8a92;
        font-size: 12px;
    }

    .replace-payslip-actions {
        display: flex;
        justify-content: flex-end;
    }

    .replace-payslip-save,
    .replace-payslip-back {
        display: inline-flex;
        align-items: center;
        min-height: 42px;
        padding: 0 17px;
        border: 0;
        border-radius: 7px;
        color: #ffffff;
        text-decoration: none;
        cursor: pointer;
    }

    .replace-payslip-save {
        background: #2878dc;
    }

    .replace-payslip-back {
        background: #3a4967;
    }
</style>

<div class="replace-payslip-header">

    <div>
        <h1>Replace Payslip</h1>

        <p>
            Update the payslip for
            {{ $payslip->employee->first_name }}
            {{ $payslip->employee->last_name }}.
        </p>
    </div>

    <a href="{{ route(
            'company.hr.payslips.index'
        ) }}"
       class="replace-payslip-back">
        Back
    </a>

</div>

<div class="replace-payslip-card">

    <form method="POST"
          enctype="multipart/form-data"
          action="{{ route(
              'company.hr.payslips.update',
              $payslip
          ) }}">

        @csrf
        @method('PUT')

        <div class="replace-payslip-field">
            <label>Pay Period *</label>

            <input type="text"
                   name="pay_period"
                   value="{{ old(
                       'pay_period',
                       $payslip->pay_period
                   ) }}"
                   required>

            @error('pay_period')
                <div class="replace-payslip-error">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="replace-payslip-field">
            <label>Payment Date *</label>

            <input type="date"
                   name="payment_date"
                   value="{{ old(
                       'payment_date',
                       optional(
                           $payslip->payment_date
                       )->format('Y-m-d')
                   ) }}"
                   required>

            @error('payment_date')
                <div class="replace-payslip-error">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="replace-payslip-field">
            <label>Replacement PDF</label>

            <input type="file"
                   name="payslip_file"
                   accept=".pdf,application/pdf">

            <small style="color:#aab4ca;">
                Leave empty to keep the current PDF:
                {{ $payslip->original_filename }}
            </small>

            @error('payslip_file')
                <div class="replace-payslip-error">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="replace-payslip-field">
            <label>Notes</label>

            <textarea name="notes">{{ old(
                'notes',
                $payslip->notes
            ) }}</textarea>
        </div>

        <div class="replace-payslip-actions">
            <button type="submit"
                    class="replace-payslip-save">
                Save Changes
            </button>
        </div>

    </form>

</div>

@endsection