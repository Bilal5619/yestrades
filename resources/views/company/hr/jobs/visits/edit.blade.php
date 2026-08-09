@extends(
    'company.layouts.app'
)

@section(
    'title',
    'Edit Job Visit'
)

@section('content')

@include(
    'company.hr.jobs.partials.styles'
)


<div class="jobsheet-header">

    <div>

        <h1>
            Edit Visit
        </h1>

        <p>

            {{
                $job->job_reference
                    ?: 'Job #'
                        .$job->id
            }}

            —

            {{ $job->title }}

        </p>

    </div>


    <a
        href="{{
            route(
                'company.hr.jobs.show',
                $job
            )
        }}"
        class="jobsheet-back"
    >
        Back to Job
    </a>

</div>


<form
    method="POST"
    action="{{
        route(
            'company.hr.jobs.visits.update',
            [
                $job,
                $visit
            ]
        )
    }}"
>

    @csrf
    @method('PUT')


    @include(
        'company.hr.jobs.visits._form'
    )


    <div
        style="
            display:flex;
            justify-content:
                space-between;
            margin-top:20px;
        "
    >

        <button
            type="submit"
            form="delete-visit-form"
            class="jobsheet-back"
        >
            Remove Visit
        </button>


        <button
            type="submit"
            class="jobsheet-save"
        >
            Save Visit
        </button>

    </div>

</form>


<form
    id="delete-visit-form"
    method="POST"
    action="{{
        route(
            'company.hr.jobs.visits.destroy',
            [
                $job,
                $visit
            ]
        )
    }}"
    style="display:none;"
    onsubmit="
        return confirm(
            'Remove this visit?'
        );
    "
>

    @csrf
    @method('DELETE')

</form>


@endsection