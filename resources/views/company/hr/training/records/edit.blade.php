@extends('company.layouts.app')

@section('title', 'Edit Training Record')

@section('content')

<style>
    .edit-training-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .edit-training-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .edit-training-header p {
        margin: 0;
        color: #aab4ca;
    }

    .edit-training-back {
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

    .edit-training-panel {
        max-width: 950px;
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .edit-training-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .edit-training-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .edit-training-full {
        grid-column: 1 / -1;
    }

    .edit-training-group input,
    .edit-training-group select,
    .edit-training-group textarea {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
    }

    .edit-training-group textarea {
        min-height: 110px;
        resize: vertical;
    }

    .edit-training-actions {
        display: flex;
        gap: 10px;
        margin-top: 22px;
    }

    .edit-training-submit,
    .edit-training-cancel {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 43px;
        padding: 0 18px;
        border: 0;
        border-radius: 7px;
        color: #ffffff;
        text-decoration: none;
        cursor: pointer;
    }

    .edit-training-submit {
        background: #2878dc;
    }

    .edit-training-cancel {
        background: #3a4967;
    }

    .edit-training-error {
        padding: 14px 17px;
        margin-bottom: 20px;
        border-radius: 7px;
        background: rgba(255,93,103,0.16);
        color: #ff8a92;
    }

    @media (max-width: 750px) {
        .edit-training-header {
            flex-direction: column;
        }

        .edit-training-grid {
            grid-template-columns: 1fr;
        }

        .edit-training-full {
            grid-column: auto;
        }
    }
</style>

<div class="edit-training-header">

    <div>
        <h1>Edit Training Record</h1>

        <p>
            Update the assigned training information.
        </p>
    </div>

    <a href="{{ route(
            'company.hr.training.records.show',
            $record
        ) }}"
       class="edit-training-back">
        <i class="fas fa-arrow-left"></i>
        Training Record
    </a>

</div>

@if ($errors->any())
    <div class="edit-training-error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="edit-training-panel">

    <form method="POST"
          action="{{ route(
              'company.hr.training.records.update',
              $record
          ) }}">

        @csrf
        @method('PUT')

        <div class="edit-training-grid">

            <div class="
                edit-training-group
                edit-training-full
            ">
                <label for="employee_id">
                    Employee
                </label>

                <select name="employee_id"
                        id="employee_id"
                        required>

                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}"
                            {{ (int) old(
                                    'employee_id',
                                    $record->employee_id
                                ) === (int) $employee->id
                                ? 'selected'
                                : '' }}>
                            {{ $employee->first_name }}
                            {{ $employee->last_name }}
                            —
                            {{ $employee->employee_number }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div class="
                edit-training-group
                edit-training-full
            ">
                <label for="training_course_id">
                    Training Course
                </label>

                <select name="training_course_id"
                        id="training_course_id"
                        required>

                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}"
                            {{ (int) old(
                                    'training_course_id',
                                    $record->training_course_id
                                ) === (int) $course->id
                                ? 'selected'
                                : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div class="edit-training-group">
                <label for="provider_name">
                    Provider
                </label>

                <input type="text"
                       name="provider_name"
                       id="provider_name"
                       maxlength="180"
                       value="{{ old(
                           'provider_name',
                           $record->provider_name
                       ) }}">
            </div>

            <div class="edit-training-group">
                <label for="scheduled_date">
                    Scheduled Date
                </label>

                <input type="date"
                       name="scheduled_date"
                       id="scheduled_date"
                       value="{{ old(
                           'scheduled_date',
                           optional(
                               $record->scheduled_date
                           )->toDateString()
                       ) }}">
            </div>

            <div class="edit-training-group">
                <label for="status">
                    Status
                </label>

                <select name="status"
                        id="status"
                        required>

                    @foreach (
                        [
                            'not_started' => 'Not Started',
                            'scheduled' => 'Scheduled',
                            'in_progress' => 'In Progress',
                        ] as $value => $label
                    )
                        <option value="{{ $value }}"
                            {{ old(
                                'status',
                                $record->status
                            ) === $value
                                ? 'selected'
                                : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div class="edit-training-group">
                <label for="cost">
                    Cost
                </label>

                <input type="number"
                       name="cost"
                       id="cost"
                       min="0"
                       step="0.01"
                       value="{{ old(
                           'cost',
                           $record->cost
                       ) }}">
            </div>

            <div class="
                edit-training-group
                edit-training-full
            ">
                <label for="notes">
                    Notes
                </label>

                <textarea name="notes"
                          id="notes"
                          maxlength="3000">{{ old(
                              'notes',
                              $record->notes
                          ) }}</textarea>
            </div>

        </div>

        <div class="edit-training-actions">

            <button type="submit"
                    class="edit-training-submit">
                Update Training
            </button>

            <a href="{{ route(
                    'company.hr.training.records.show',
                    $record
                ) }}"
               class="edit-training-cancel">
                Cancel
            </a>

        </div>

    </form>

</div>

@endsection