<style>
    .course-form-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .course-form-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .course-form-back {
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

    .course-form-panel {
        max-width: 900px;
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .course-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .course-form-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .course-form-full {
        grid-column: 1 / -1;
    }

    .course-form-group label {
        font-weight: 600;
    }

    .course-form-group input,
    .course-form-group textarea {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
    }

    .course-form-group textarea {
        min-height: 110px;
        resize: vertical;
    }

    .course-checkboxes {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 13px;
    }

    .course-checkbox {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 13px;
        border-radius: 7px;
        background: #151f35;
    }

    .course-checkbox input {
        width: auto;
    }

    .course-form-actions {
        display: flex;
        gap: 10px;
        margin-top: 22px;
    }

    .course-form-submit,
    .course-form-cancel {
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

    .course-form-submit {
        background: #2878dc;
    }

    .course-form-cancel {
        background: #3a4967;
    }

    .course-form-error {
        padding: 14px 17px;
        margin-bottom: 20px;
        border-radius: 7px;
        background: rgba(255,93,103,0.16);
        color: #ff8a92;
    }

    @media (max-width: 700px) {
        .course-form-header {
            flex-direction: column;
        }

        .course-form-grid,
        .course-checkboxes {
            grid-template-columns: 1fr;
        }

        .course-form-full {
            grid-column: auto;
        }
    }
</style>

<div class="course-form-header">

    <div>
        <h1>{{ $pageTitle }}</h1>

        <p style="margin:0; color:#aab4ca;">
            Configure the training requirements and validity period.
        </p>
    </div>

    <a href="{{ route(
            'company.hr.training.courses.index'
        ) }}"
       class="course-form-back">
        <i class="fas fa-arrow-left"></i>
        Training Courses
    </a>

</div>

@if ($errors->any())
    <div class="course-form-error">
        <strong>Please correct the following:</strong>

        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="course-form-panel">

    <form method="POST"
          action="{{ $formAction }}">

        @csrf

        @if ($formMethod === 'PUT')
            @method('PUT')
        @endif

        <div class="course-form-grid">

            <div class="course-form-group">
                <label for="name">Course Name</label>

                <input type="text"
                       name="name"
                       id="name"
                       maxlength="180"
                       value="{{ old(
                           'name',
                           optional($course)->name
                       ) }}"
                       required>
            </div>

            <div class="course-form-group">
                <label for="category">Category</label>

                <input type="text"
                       name="category"
                       id="category"
                       maxlength="120"
                       value="{{ old(
                           'category',
                           optional($course)->category
                       ) }}"
                       placeholder="Health and Safety">
            </div>

            <div class="
                course-form-group
                course-form-full
            ">
                <label for="description">Description</label>

                <textarea name="description"
                          id="description"
                          maxlength="3000">{{ old(
                              'description',
                              optional($course)->description
                          ) }}</textarea>
            </div>

            <div class="
                course-form-full
                course-checkboxes
            ">

                <label class="course-checkbox">
                    <input type="checkbox"
                           name="is_mandatory"
                           value="1"
                           {{ old(
                               'is_mandatory',
                               optional($course)->is_mandatory
                           ) ? 'checked' : '' }}>

                    Mandatory training
                </label>

                <label class="course-checkbox">
                    <input type="checkbox"
                           name="requires_certificate"
                           value="1"
                           {{ old(
                               'requires_certificate',
                               optional($course)
                                   ->requires_certificate
                           ) ? 'checked' : '' }}>

                    Certificate required
                </label>

                <label class="course-checkbox">
                    <input type="checkbox"
                           name="has_expiry"
                           id="has_expiry"
                           value="1"
                           {{ old(
                               'has_expiry',
                               optional($course)->has_expiry
                           ) ? 'checked' : '' }}>

                    Training expires
                </label>

                <label class="course-checkbox">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           {{ old(
                               'is_active',
                               $course
                                   ? $course->is_active
                                   : true
                           ) ? 'checked' : '' }}>

                    Active course
                </label>

            </div>

            <div class="course-form-group">
                <label for="validity_months">
                    Validity in Months
                </label>

                <input type="number"
                       name="validity_months"
                       id="validity_months"
                       min="1"
                       max="1200"
                       value="{{ old(
                           'validity_months',
                           optional($course)->validity_months
                       ) }}"
                       placeholder="36">

                <span style="color:#aab4ca; font-size:12px;">
                    Required only when the course expires.
                </span>
            </div>

        </div>

        <div class="course-form-actions">

            <button type="submit"
                    class="course-form-submit">
                {{ $submitLabel }}
            </button>

            <a href="{{ route(
                    'company.hr.training.courses.index'
                ) }}"
               class="course-form-cancel">
                Cancel
            </a>

        </div>

    </form>

</div>