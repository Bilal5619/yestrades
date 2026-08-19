@extends('company.layouts.app')

@section('title', 'Add Company Policy')

@section('content')

<style>
    .policy-form-wrapper {
        max-width: 850px;
    }

    .policy-heading {
        margin-bottom: 24px;
    }

    .policy-heading h1 {
        margin: 0 0 7px;
    }

    .policy-heading p {
        margin: 0;
        color: #aab4ca;
    }

    .policy-form-card {
        background: #1e2a44;
        padding: 24px;
        border-radius: 10px;
    }

    .policy-form-group {
        margin-bottom: 18px;
    }

    .policy-form-group label {
        display: block;
        margin-bottom: 7px;
        color: #cbd4e6;
        font-size: 13px;
        font-weight: 600;
    }

    .policy-input,
    .policy-textarea,
    .policy-file {
        width: 100%;
        box-sizing: border-box;
        padding: 11px 12px;
        border-radius: 7px;
        border: 1px solid rgba(255,255,255,.12);
        background: #162138;
        color: white;
    }

    .policy-textarea {
        min-height: 130px;
        resize: vertical;
    }

    .policy-actions {
        display: flex;
        gap: 10px;
        margin-top: 22px;
    }

    .policy-button {
        padding: 11px 17px;
        border-radius: 7px;
        border: 0;
        background: #2878dc;
        color: white;
        text-decoration: none;
        cursor: pointer;
    }

    .policy-cancel {
        background: #3a4967;
    }

    .policy-errors {
        margin-bottom: 18px;
        padding: 12px 15px;
        border-radius: 7px;
        background: rgba(236,87,98,.15);
        color: #ff9aa2;
    }
</style>

<div class="policy-form-wrapper">

    <div class="policy-heading">

        <h1>Add Company Policy</h1>

        <p>
            Upload an HR or compliance policy for employees.
        </p>

    </div>

    @if ($errors->any())

        <div class="policy-errors">
            <ul style="margin:0;padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

    @endif

    <div class="policy-form-card">

        <form
            method="POST"
            action="{{ route(
                'company.hr.policies.store'
            ) }}"
            enctype="multipart/form-data"
        >
            @csrf

            <div class="policy-form-group">

                <label>Policy Title</label>

                <input
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    class="policy-input"
                    placeholder="Example: Attendance & Absence Policy"
                    required
                >

            </div>

            <div class="policy-form-group">

                <label>Version</label>

                <input
                    type="text"
                    name="version"
                    value="{{ old('version', '1.0') }}"
                    class="policy-input"
                    placeholder="1.0"
                >

            </div>

            <div class="policy-form-group">

                <label>Description</label>

                <textarea
                    name="description"
                    class="policy-textarea"
                    placeholder="Brief description of this policy"
                >{{ old('description') }}</textarea>

            </div>

            <div class="policy-form-group">

                <label>
                    Policy Document
                </label>

                <input
                    type="file"
                    name="policy_file"
                    class="policy-file"
                    accept=".pdf,.doc,.docx"
                    required
                >

                <div
                    style="
                        color:#aab4ca;
                        font-size:12px;
                        margin-top:6px;
                    "
                >
                    PDF, DOC or DOCX. Maximum 10MB.
                </div>

            </div>

            <div class="policy-actions">

                <button
                    type="submit"
                    class="policy-button"
                >
                    <i class="fas fa-save"></i>
                    Save Policy
                </button>

                <a
                    href="{{ route(
                        'company.hr.policies.index'
                    ) }}"
                    class="
                        policy-button
                        policy-cancel
                    "
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

@endsection