@extends('company.layouts.app')

@section('title', 'Induction Meeting')

@section('content')

<style>
    .induction-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .induction-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .induction-header p {
        margin: 0;
        color: #aab4ca;
    }

    .induction-back {
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

    .induction-card {
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .induction-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .induction-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .induction-field-full {
        grid-column: 1 / -1;
    }

    .induction-field label {
        color: #c9d2e5;
        font-size: 13px;
        font-weight: 600;
    }

    .induction-field input,
    .induction-field select,
    .induction-field textarea {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
        box-sizing: border-box;
    }

    .induction-field textarea {
        min-height: 120px;
        resize: vertical;
    }

    .induction-error {
        color: #ff8a92;
        font-size: 12px;
    }

    .induction-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 24px;
    }

    .induction-save {
        min-height: 42px;
        padding: 0 18px;
        border: 0;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    @media (max-width: 750px) {
        .induction-grid {
            grid-template-columns: 1fr;
        }

        .induction-field-full {
            grid-column: auto;
        }

        .induction-header {
            flex-direction: column;
        }
    }
</style>

@php
    $induction = $onboarding->induction;
    $employee = $onboarding->employee;
@endphp

<div class="induction-header">

    <div>
        <h1>Induction Meeting</h1>

        <p>
            Record the induction meeting for
            {{ $employee->first_name }}
            {{ $employee->last_name }}.
        </p>
    </div>

    <a href="{{ route(
            'company.hr.onboarding.show',
            $onboarding
        ) }}"
       class="induction-back">

        <i class="fas fa-arrow-left"></i>
        Employee Onboarding
    </a>

</div>

<div class="induction-card">

    <form method="POST"
          action="{{ route(
              'company.hr.onboarding.induction.update',
              $onboarding
          ) }}">

        @csrf
        @method('PUT')

        <div class="induction-grid">

            <div class="induction-field">
                <label>Meeting Date *</label>

                <input type="date"
                       name="meeting_date"
                       value="{{ old(
                           'meeting_date',
                           optional(
                               optional($induction)->meeting_date
                           )->format('Y-m-d')
                       ) }}"
                       required>

                @error('meeting_date')
                    <div class="induction-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="induction-field">
                <label>Meeting Time</label>

                <input type="time"
                       name="meeting_time"
                       value="{{ old(
                           'meeting_time',
                           optional($induction)->meeting_time
                       ) }}">

                @error('meeting_time')
                    <div class="induction-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="induction-field">
                <label>Location</label>

                <input type="text"
                       name="location"
                       placeholder="Office, online or site address"
                       value="{{ old(
                           'location',
                           optional($induction)->location
                       ) }}">

                @error('location')
                    <div class="induction-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="induction-field">
                <label>Manager</label>

                <select name="manager_id">
                    <option value="">Select manager</option>

                    @foreach ($managers as $manager)
                        <option value="{{ $manager->id }}"
                            {{ (string) old(
                                'manager_id',
                                optional($induction)->manager_id
                            ) === (string) $manager->id
                                ? 'selected'
                                : ''
                            }}>

                            {{ trim(
                                $manager->first_name
                                . ' '
                                . $manager->last_name
                            ) }}
                        </option>
                    @endforeach
                </select>

                @error('manager_id')
                    <div class="induction-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="
                induction-field
                induction-field-full
            ">
                <label>Topics Discussed</label>

                <textarea name="topics_discussed"
                          placeholder="Working hours, company policies, health and safety, reporting structure, sickness and holiday procedures">{{ old(
                    'topics_discussed',
                    optional($induction)->topics_discussed
                ) }}</textarea>

                @error('topics_discussed')
                    <div class="induction-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="
                induction-field
                induction-field-full
            ">
                <label>Meeting Notes</label>

                <textarea name="meeting_notes"
                          placeholder="Add notes from the induction meeting">{{ old(
                    'meeting_notes',
                    optional($induction)->meeting_notes
                ) }}</textarea>

                @error('meeting_notes')
                    <div class="induction-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="induction-field">
                <label>Employee Confirmed? *</label>

                <select name="employee_confirmed" required>

                    <option value="0"
                        {{ (string) old(
                            'employee_confirmed',
                            optional($induction)->employee_confirmed ?? 0
                        ) === '0'
                            ? 'selected'
                            : ''
                        }}>
                        No
                    </option>

                    <option value="1"
                        {{ (string) old(
                            'employee_confirmed',
                            optional($induction)->employee_confirmed ?? 0
                        ) === '1'
                            ? 'selected'
                            : ''
                        }}>
                        Yes
                    </option>

                </select>

                @error('employee_confirmed')
                    <div class="induction-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="induction-field">
                <label>Meeting Completed? *</label>

                <select name="meeting_completed" required>

                    <option value="0"
                        {{ (string) old(
                            'meeting_completed',
                            optional($induction)->meeting_completed ?? 0
                        ) === '0'
                            ? 'selected'
                            : ''
                        }}>
                        No
                    </option>

                    <option value="1"
                        {{ (string) old(
                            'meeting_completed',
                            optional($induction)->meeting_completed ?? 0
                        ) === '1'
                            ? 'selected'
                            : ''
                        }}>
                        Yes
                    </option>

                </select>

                @error('meeting_completed')
                    <div class="induction-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

        </div>

        @if (
            auth('company')->user()
                ->hasPermission('onboarding.manage')
        )
            <div class="induction-actions">
                <button type="submit"
                        class="induction-save">
                    Save Induction Meeting
                </button>
            </div>
        @endif

    </form>

</div>

@endsection