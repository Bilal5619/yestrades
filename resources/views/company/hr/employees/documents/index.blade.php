@extends('company.layouts.app')

@section('title', 'Employee Documents')

@section('content')

<style>
    .document-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .document-header h1 {
        margin: 0 0 6px;
        font-size: 28px;
    }

    .document-header p {
        margin: 0;
        color: #aab4ca;
    }

    .document-back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 17px;
        border-radius: 7px;
        background: #3a4967;
        color: #ffffff;
        text-decoration: none;
    }

    .document-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }

    .document-summary-card {
        padding: 19px;
        border-radius: 9px;
        background: #1e2a44;
    }

    .document-summary-label {
        margin-bottom: 9px;
        color: #aab4ca;
        font-size: 13px;
    }

    .document-summary-value {
        font-size: 27px;
        font-weight: 700;
    }

    .document-panel {
        margin-bottom: 22px;
        padding: 23px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .document-panel h2 {
        margin: 0 0 18px;
        padding-bottom: 13px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        font-size: 20px;
    }

    .document-form-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .document-form-group {
        margin-bottom: 17px;
    }

    .document-form-group label {
        display: block;
        margin-bottom: 7px;
        font-size: 14px;
    }

    .document-control {
        width: 100%;
        min-height: 44px;
        padding: 10px 12px;
        border: 1px solid #3a4967;
        border-radius: 6px;
        background: #172036;
        color: #ffffff;
        outline: none;
    }

    textarea.document-control {
        min-height: 95px;
        resize: vertical;
    }

    .document-submit-button {
        padding: 12px 18px;
        border: 0;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    .document-message {
        margin-bottom: 18px;
        padding: 14px 16px;
        border-radius: 7px;
    }

    .document-success {
        background: rgba(46,191,114,0.15);
        color: #59dc96;
    }

    .document-error {
        background: rgba(236,87,98,0.15);
        color: #ff929a;
    }

    .document-table-wrapper {
        overflow-x: auto;
    }

    .document-table {
        width: 100%;
        min-width: 1100px;
        border-collapse: collapse;
    }

    .document-table th,
    .document-table td {
        padding: 13px 12px;
        text-align: left;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        vertical-align: middle;
    }

    .document-table th {
        color: #aab4ca;
        font-size: 13px;
    }

    .document-status {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 14px;
        font-size: 12px;
        text-transform: capitalize;
    }

    .status-missing,
    .status-expired,
    .status-rejected {
        background: rgba(236,87,98,0.16);
        color: #ff7c86;
    }

    .status-pending_review,
    .status-expiring_soon {
        background: rgba(243,166,58,0.17);
        color: #ffc46d;
    }

    .status-valid {
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .document-actions {
        display: flex;
        gap: 7px;
        flex-wrap: wrap;
    }

    .document-action {
        display: inline-flex;
        align-items: center;
        min-height: 34px;
        padding: 0 11px;
        border: 0;
        border-radius: 5px;
        color: #ffffff;
        text-decoration: none;
        cursor: pointer;
    }

    .document-view {
        background: #2878dc;
    }

    .document-approve {
        background: #23985d;
    }

    .document-reject {
        background: #cf4d58;
    }

    .document-reject-form {
        margin-top: 8px;
    }

    .document-reject-form input {
        width: 230px;
        min-height: 34px;
        padding: 6px 9px;
        border: 1px solid #3a4967;
        border-radius: 5px;
        background: #172036;
        color: #ffffff;
    }

    @media (max-width: 950px) {
        .document-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .document-form-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 550px) {
        .document-header {
            flex-direction: column;
        }

        .document-summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="document-header">
    <div>
        <h1>Documents: {{ $employee->full_name }}</h1>

        <p>
            Upload and review required employee documents,
            certificates and expiry information.
        </p>
    </div>

    <a href="{{ route(
            'company.hr.employees.show',
            $employee
        ) }}"
       class="document-back-button">
        <i class="fas fa-arrow-left"></i>
        Employee Profile
    </a>
</div>

@if (session('success'))
    <div class="document-message document-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="document-message document-error">
        {{ $errors->first() }}
    </div>
@endif

<div class="document-summary-grid">

    <div class="document-summary-card">
        <div class="document-summary-label">Missing</div>
        <div class="document-summary-value">
            {{ $summary['missing'] }}
        </div>
    </div>

    <div class="document-summary-card">
        <div class="document-summary-label">
            Pending Review
        </div>
        <div class="document-summary-value">
            {{ $summary['pending_review'] }}
        </div>
    </div>

    <div class="document-summary-card">
        <div class="document-summary-label">
            Expiring Soon
        </div>
        <div class="document-summary-value">
            {{ $summary['expiring'] }}
        </div>
    </div>

    <div class="document-summary-card">
        <div class="document-summary-label">Expired</div>
        <div class="document-summary-value">
            {{ $summary['expired'] }}
        </div>
    </div>

</div>

@if (
    auth('company')->user()
        ->hasPermission('employee-documents.upload')
)


    <div class="document-panel">
        <h2>Upload or Replace Document</h2>

        <form method="POST"
              action="{{ route(
                  'company.hr.employees.documents.store',
                  $employee
              ) }}"
              enctype="multipart/form-data">

            @csrf

            <div class="document-form-grid">

                <div class="document-form-group">
                    <label for="document_type_id">
                        Document type
                    </label>

                    <select
                        id="document_type_id"
                        name="document_type_id"
                        class="document-control"
                        required
                    >
                        <option value="">
                            Select document type
                        </option>

                        @foreach ($documentTypes as $type)
                            <option
                                value="{{ $type->id }}"
                                {{ (string) old('document_type_id')
                                    === (string) $type->id
                                    ? 'selected'
                                    : ''
                                }}
                            >
                                {{ $type->name }}

                                @if ($type->is_required_by_default)
                                    — Required
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="document-form-group">
                    <label for="title">Document title</label>

                    <input
                        id="title"
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        class="document-control"
                        placeholder="Defaults to document type"
                    >
                </div>

                <div class="document-form-group">
                    <label for="document_number">
                        Document number
                    </label>

                    <input
                        id="document_number"
                        type="text"
                        name="document_number"
                        value="{{ old('document_number') }}"
                        class="document-control"
                    >
                </div>

                <div class="document-form-group">
                    <label for="issue_date">Issue date</label>

                    <input
                        id="issue_date"
                        type="date"
                        name="issue_date"
                        value="{{ old('issue_date') }}"
                        class="document-control"
                    >
                </div>

                <div class="document-form-group">
                    <label for="expiry_date">
                        Expiry date
                    </label>

                    <input
                        id="expiry_date"
                        type="date"
                        name="expiry_date"
                        value="{{ old('expiry_date') }}"
                        class="document-control"
                    >
                </div>

                <div class="document-form-group">
                    <label for="file">File</label>

                    <input
                        id="file"
                        type="file"
                        name="file"
                        class="document-control"
                        accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx"
                        required
                    >
                </div>

            </div>

            <div class="document-form-group">
                <label for="notes">Notes</label>

                <textarea
                    id="notes"
                    name="notes"
                    class="document-control"
                >{{ old('notes') }}</textarea>
            </div>

            <button type="submit"
                    class="document-submit-button">
                <i class="fas fa-upload"
                   style="margin-right:7px;"></i>
                Upload Document
            </button>

        </form>
    </div>

@endif

<div class="document-panel">
    <h2>Document Checklist</h2>

    <div class="document-table-wrapper">

        <table class="document-table">

            <thead>
                <tr>
                    <th>Document</th>
                    <th>Required</th>
                    <th>Number</th>
                    <th>Issue Date</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th>Uploaded By</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($documents as $document)

                    @php
                        $status = $document->document_status;
                    @endphp

                    <tr>
                        <td>
                            <strong>
                                {{ optional(
                                    $document->documentType
                                )->name ?? $document->title }}
                            </strong>

                            @if ($document->review_notes)
                                <div style="
                                    margin-top:4px;
                                    color:#ff929a;
                                    font-size:12px;
                                ">
                                    {{ $document->review_notes }}
                                </div>
                            @endif
                        </td>

                        <td>
                            {{ $document->is_required
                                ? 'Yes'
                                : 'No'
                            }}
                        </td>

                        <td>
                            {{ $document->document_number ?: '—' }}
                        </td>

                        <td>
                            {{ $document->issue_date
                                ? $document->issue_date->format('d M Y')
                                : '—'
                            }}
                        </td>

                        <td>
                            {{ $document->expiry_date
                                ? $document->expiry_date->format('d M Y')
                                : 'No expiry'
                            }}
                        </td>

                        <td>
                            <span class="document-status status-{{ $status }}">
                                {{ ucwords(str_replace(
                                    '_',
                                    ' ',
                                    $status
                                )) }}
                            </span>
                        </td>

                        <td>
                            {{ optional($document->uploadedBy)->full_name
                                ?? 'Not uploaded'
                            }}
                        </td>

                        <td>
    <div class="document-actions">

        {{-- View --}}
        @if ($document->file_path)
            <a href="{{ route(
                    'company.hr.employees.documents.view',
                    [$employee, $document]
                ) }}"
               target="_blank"
               rel="noopener"
               class="document-action document-view">
                View
            </a>
        @endif

        {{-- Download: Company Owner / authorised user only --}}
        @if (
            $document->file_path &&
            auth('company')->user()
                ->hasPermission('employee-documents.download')
        )
            <a href="{{ route(
                    'company.hr.employees.documents.download',
                    [$employee, $document]
                ) }}"
               class="document-action"
               style="background:#50627f;">
                Download
            </a>
        @endif

        {{-- Approve --}}
        @if (
            $document->file_path &&
            auth('company')->user()
                ->hasPermission('employee-documents.approve')
        )
            <form method="POST"
                  action="{{ route(
                      'company.hr.employees.documents.approve',
                      [$employee, $document]
                  ) }}">

                @csrf

                <button type="submit"
                        class="document-action document-approve">
                    Approve
                </button>
            </form>
        @endif

    </div>

    {{-- Reject --}}
    @if (
        $document->file_path &&
        auth('company')->user()
            ->hasPermission('employee-documents.approve')
    )
        <form method="POST"
              action="{{ route(
                  'company.hr.employees.documents.reject',
                  [$employee, $document]
              ) }}"
              class="document-reject-form">

            @csrf

            <input
                type="text"
                name="review_notes"
                placeholder="Reason for rejection"
                required
            >

            <button type="submit"
                    class="document-action document-reject">
                Reject
            </button>
        </form>
    @endif
</td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="8">
                            No document checklist records found.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>
</div>

@endsection