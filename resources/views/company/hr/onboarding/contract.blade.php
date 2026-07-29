@extends('company.layouts.app')

@section('title', 'Employee Contract')

@section('content')

<style>
    .employee-contract-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .employee-contract-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .employee-contract-header p {
        margin: 0;
        color: #aab4ca;
    }

    .employee-contract-back {
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

    .employee-contract-layout {
        display: grid;
        grid-template-columns: 360px minmax(0, 1fr);
        gap: 20px;
    }

    .employee-contract-card {
        padding: 22px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .employee-contract-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
        margin-bottom: 17px;
    }

    .employee-contract-field label {
        color: #c9d2e5;
        font-size: 13px;
        font-weight: 600;
    }

    .employee-contract-field select,
    .employee-contract-field textarea {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
        box-sizing: border-box;
    }

    .employee-contract-field textarea {
        min-height: 120px;
        resize: vertical;
    }

    .employee-contract-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .employee-contract-button {
        min-height: 41px;
        padding: 0 15px;
        border: 0;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    .employee-contract-button-secondary {
        background: #3a4967;
    }

    .employee-contract-preview {
        min-height: 700px;
        padding: 40px;
        border-radius: 10px;
        background: #ffffff;
        color: #111111;
        line-height: 1.7;
    }

    .employee-contract-preview h1,
    .employee-contract-preview h2,
    .employee-contract-preview h3 {
        color: #111111;
    }

    .employee-contract-empty {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 500px;
        color: #707782;
        text-align: center;
    }

    .employee-contract-status {
        margin-bottom: 18px;
        padding: 12px 14px;
        border-radius: 7px;
        background: #151f35;
        color: #aab4ca;
        font-size: 13px;
    }

    .employee-contract-error {
        color: #ff8a92;
        font-size: 12px;
    }

    @media (max-width: 950px) {
        .employee-contract-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .employee-contract-header {
            flex-direction: column;
        }

        .employee-contract-preview {
            padding: 22px;
        }
    }
    .employee-contract-upload {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,0.08);
}

.employee-contract-upload h3 {
    margin: 0 0 8px;
    font-size: 17px;
}

.employee-contract-upload p {
    margin: 0 0 14px;
    color: #aab4ca;
    font-size: 13px;
}

.employee-contract-upload input,
.employee-contract-upload textarea {
    width: 100%;
    padding: 11px 12px;
    margin-bottom: 12px;
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 7px;
    background: #151f35;
    color: #ffffff;
    box-sizing: border-box;
}

.employee-contract-upload textarea {
    min-height: 90px;
    resize: vertical;
}
</style>

@php
    $employee = $onboarding->employee;

    $selectedTemplateId = old(
        'contract_template_id',
        optional($selectedTemplate)->id
            ?? optional($contract)
                ->contract_template_id
    );

    $notesValue = old(
        'notes',
        $draftNotes
            ?? optional($contract)->notes
    );
@endphp

<div class="employee-contract-header">

    <div>
        <h1>Employee Contract</h1>

        <p>
            Prepare the employment contract for
            {{ $employee->first_name }}
            {{ $employee->last_name }}.
        </p>
    </div>

    <a href="{{ route(
            'company.hr.onboarding.show',
            $onboarding
        ) }}"
       class="employee-contract-back">

        <i class="fas fa-arrow-left"></i>
        Employee Onboarding
    </a>

</div>

<div class="employee-contract-layout">

    <div class="employee-contract-card">

        @if ($contract)
            <div class="employee-contract-status">
                Current draft:
                Version {{ $contract->version }}

                <br>

                Status:
                {{ ucwords(
                    str_replace(
                        '_',
                        ' ',
                        $contract->status
                    )
                ) }}
            </div>
        @endif

        <form method="POST"
              action="{{ route(
                  'company.hr.onboarding.contract.preview',
                  $onboarding
              ) }}">

            @csrf

            <div class="employee-contract-field">
                <label>Contract Template *</label>

                <select name="contract_template_id"
                        required>

                    <option value="">
                        Select template
                    </option>

                    @foreach ($templates as $template)
                        <option value="{{ $template->id }}"
                            {{ (string) $selectedTemplateId ===
                               (string) $template->id
                                ? 'selected'
                                : ''
                            }}>

                            {{ $template->name }}

                            @if ($template->is_default)
                                — Default
                            @endif
                        </option>
                    @endforeach

                </select>

                @error('contract_template_id')
                    <div class="employee-contract-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="employee-contract-field">
                <label>Internal Notes</label>

                <textarea name="notes">{{ $notesValue }}</textarea>
            </div>

            <div class="employee-contract-actions">

                <button type="submit"
                        class="
                            employee-contract-button
                            employee-contract-button-secondary
                        ">
                    Preview Contract
                </button>

            </div>

        </form>

        @if (
            $selectedTemplate &&
            auth('company')->user()
                ->hasPermission(
                    'employee_contracts.generate'
                )
        )
            <form method="POST"
                  action="{{ route(
                      'company.hr.onboarding.contract.draft',
                      $onboarding
                  ) }}"
                  style="margin-top:12px;">

                @csrf

                <input type="hidden"
                       name="contract_template_id"
                       value="{{ $selectedTemplate->id }}">

                <input type="hidden"
                       name="notes"
                       value="{{ $notesValue }}">

                <button type="submit"
                        class="employee-contract-button">
                    Save Contract Draft
                </button>
            </form>
  @endif
@if (
    $contract &&
    $contract->status === 'draft' &&
    auth('company')->user()
        ->hasPermission(
            'employee_contracts.generate'
        )
)
    <form method="POST"
          action="{{ route(
              'company.hr.onboarding.contract.generate',
              $onboarding
          ) }}"
          style="margin-top:12px;">

        @csrf

        <button type="submit"
                class="employee-contract-button">
            <i class="fas fa-file-pdf"></i>
            Generate PDF
        </button>

    </form>
@endif
@if (
    $contract &&
    $contract->generated_file_path &&
    auth('company')->user()
        ->hasPermission(
            'employee_contracts.download'
        )
)
    <a href="{{ route(
            'company.hr.onboarding.contract.download',
            [
                $onboarding,
                $contract,
            ]
        ) }}"
       class="employee-contract-button"
       style="
           display:inline-flex;
           align-items:center;
           text-decoration:none;
           margin-top:12px;
       ">

        <i class="fas fa-download"></i>
        Download PDF
    </a>
@endif
@if (
    $contract &&
    in_array(
        $contract->status,
        ['generated', 'awaiting_signature', 'signed']
    )
)
    <div class="employee-contract-upload">

        <h3>Signed Contract</h3>

        @if ($contract->status === 'signed')

            <p>
                Signed contract uploaded on
                {{ optional(
                    $contract->signed_at
                )->format('d M Y H:i') }}.
            </p>

            @if (
                auth('company')->user()
                    ->hasPermission(
                        'employee_contracts.download'
                    )
            )
                <a href="{{ route(
                        'company.hr.onboarding.contract.signed.download',
                        [
                            $onboarding,
                            $contract,
                        ]
                    ) }}"
                   class="employee-contract-button"
                   style="
                       display:inline-flex;
                       align-items:center;
                       text-decoration:none;
                   ">

                    <i class="fas fa-download"></i>
                    Download Signed Contract
                </a>
            @endif

        @elseif (
            auth('company')->user()
                ->hasPermission(
                    'employee_contracts.upload_signed'
                )
        )

            <p>
                Upload the final signed PDF to complete the contract section.
            </p>

            <form method="POST"
                  enctype="multipart/form-data"
                  action="{{ route(
                      'company.hr.onboarding.contract.signed.upload',
                      [
                          $onboarding,
                          $contract,
                      ]
                  ) }}">

                @csrf

                <input type="file"
                       name="signed_contract"
                       accept=".pdf,application/pdf"
                       required>

                @error('signed_contract')
                    <div class="employee-contract-error">
                        {{ $message }}
                    </div>
                @enderror

                <textarea name="signed_notes"
                          placeholder="Optional notes">{{ old(
                    'signed_notes'
                ) }}</textarea>

                <button type="submit"
                        class="employee-contract-button">

                    <i class="fas fa-upload"></i>
                    Upload Signed Contract
                </button>

            </form>

        @endif

    </div>
@endif

    </div>

    <div class="employee-contract-card">

        @if ($previewContent)

            <div class="employee-contract-preview">
                {!! $previewContent !!}
            </div>

        @else

            <div class="employee-contract-preview">
                <div class="employee-contract-empty">
                    Select a contract template and click
                    Preview Contract.
                </div>
            </div>

        @endif

    </div>

</div>

@endsection