@extends('company.layouts.app')

@section('title', 'Training Courses')

@section('content')

<style>
    .course-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .course-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .course-header p {
        margin: 0;
        color: #aab4ca;
    }

    .course-header-actions {
        display: flex;
        gap: 10px;
    }

    .course-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 15px;
        border-radius: 7px;
        color: #ffffff;
        text-decoration: none;
    }

    .course-button-primary {
        background: #2878dc;
    }

    .course-button-secondary {
        background: #3a4967;
    }

    .course-filters,
    .course-panel {
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .course-filter-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 12px;
    }

    .course-filter-grid input,
    .course-filter-grid select {
        width: 100%;
        min-height: 41px;
        padding: 8px 11px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 6px;
        background: #151f35;
        color: #ffffff;
    }

    .course-filter-button {
        min-height: 41px;
        padding: 0 16px;
        border: 0;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    .course-table-wrapper {
        overflow-x: auto;
    }

    .course-table {
        width: 100%;
        min-width: 1000px;
        border-collapse: collapse;
    }

    .course-table th,
    .course-table td {
        padding: 13px 10px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
    }

    .course-table th {
        color: #aab4ca;
        font-size: 12px;
    }

    .course-name {
        font-weight: 600;
    }

    .course-category {
        margin-top: 3px;
        color: #aab4ca;
        font-size: 12px;
    }

    .course-badge {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 13px;
        font-size: 12px;
    }

    .course-badge-green {
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .course-badge-grey {
        background: rgba(170,180,202,0.14);
        color: #c4ccdc;
    }

    .course-edit {
        display: inline-flex;
        padding: 7px 11px;
        border-radius: 6px;
        background: #2878dc;
        color: #ffffff;
        text-decoration: none;
    }

    .course-alert {
        padding: 13px 16px;
        margin-bottom: 18px;
        border-radius: 7px;
        background: rgba(46,191,114,0.16);
        color: #59dc96;
    }

    .course-empty {
        padding: 35px;
        text-align: center;
        color: #aab4ca;
    }

    @media (max-width: 850px) {
        .course-header {
            flex-direction: column;
        }

        .course-filter-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="course-header">

    <div>
        <h1>Training Courses</h1>

        <p>
            Manage reusable company training courses.
        </p>
    </div>

    <div class="course-header-actions">

        <a href="{{ route(
                'company.hr.training.index'
            ) }}"
           class="
               course-button
               course-button-secondary
           ">
            <i class="fas fa-arrow-left"></i>
            Training Overview
        </a>

        <a href="{{ route(
                'company.hr.training.courses.create'
            ) }}"
           class="
               course-button
               course-button-primary
           ">
            <i class="fas fa-plus"></i>
            Add Course
        </a>

    </div>

</div>

@if (session('success'))
    <div class="course-alert">
        {{ session('success') }}
    </div>
@endif

<div class="course-filters">

    <form method="GET"
          action="{{ route(
              'company.hr.training.courses.index'
          ) }}">

        <div class="course-filter-grid">

            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Course name or category">

            <select name="status">
                <option value="">All statuses</option>

                <option value="active"
                    {{ request('status') === 'active'
                        ? 'selected'
                        : '' }}>
                    Active
                </option>

                <option value="inactive"
                    {{ request('status') === 'inactive'
                        ? 'selected'
                        : '' }}>
                    Inactive
                </option>
            </select>

            <select name="mandatory">
                <option value="">Mandatory and optional</option>

                <option value="yes"
                    {{ request('mandatory') === 'yes'
                        ? 'selected'
                        : '' }}>
                    Mandatory
                </option>

                <option value="no"
                    {{ request('mandatory') === 'no'
                        ? 'selected'
                        : '' }}>
                    Optional
                </option>
            </select>

            <button type="submit"
                    class="course-filter-button">
                Filter
            </button>

        </div>

    </form>

</div>

<div class="course-panel">

    <div class="course-table-wrapper">

        <table class="course-table">

            <thead>
                <tr>
                    <th>Course</th>
                    <th>Mandatory</th>
                    <th>Certificate</th>
                    <th>Expiry</th>
                    <th>Validity</th>
                    <th>Records</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($courses as $course)

                    <tr>
                        <td>
                            <div class="course-name">
                                {{ $course->name }}
                            </div>

                            <div class="course-category">
                                {{ $course->category ?: 'No category' }}
                            </div>
                        </td>

                        <td>
                            {{ $course->is_mandatory
                                ? 'Yes'
                                : 'No'
                            }}
                        </td>

                        <td>
                            {{ $course->requires_certificate
                                ? 'Required'
                                : 'Not required'
                            }}
                        </td>

                        <td>
                            {{ $course->has_expiry
                                ? 'Yes'
                                : 'No'
                            }}
                        </td>

                        <td>
                            @if ($course->has_expiry)
                                {{ $course->validity_months }}
                                months
                            @else
                                —
                            @endif
                        </td>

                        <td>
                            {{ $course->training_records_count }}
                        </td>

                        <td>
                            <span class="
                                course-badge
                                {{ $course->is_active
                                    ? 'course-badge-green'
                                    : 'course-badge-grey'
                                }}
                            ">
                                {{ $course->is_active
                                    ? 'Active'
                                    : 'Inactive'
                                }}
                            </span>
                        </td>

                        <td>
                            <a href="{{ route(
                                    'company.hr.training.courses.edit',
                                    $course
                                ) }}"
                               class="course-edit">
                                Edit
                            </a>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="8">
                            <div class="course-empty">
                                No training courses found.
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div style="margin-top:20px;">
        {{ $courses->links() }}
    </div>

</div>

@endsection