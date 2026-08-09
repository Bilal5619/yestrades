@extends(
    'company.layouts.app'
)

@section(
    'title',
    'Add Job Visit'
)

@section('content')

@include(
    'company.hr.jobs.partials.styles'
)


<div class="jobsheet-header">

    <div>

        <h1>
            Add Visit
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
            'company.hr.jobs.visits.store',
            $job
        )
    }}"
>

    @csrf


    @include(
        'company.hr.jobs.visits._form'
    )


    <div style="
        margin-top:20px;
        text-align:right;
    ">

        <button
            type="submit"
            class="jobsheet-save"
        >
            Schedule Visit
        </button>

    </div>

</form>


@endsection