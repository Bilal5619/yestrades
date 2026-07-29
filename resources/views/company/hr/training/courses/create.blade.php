@extends('company.layouts.app')

@section('title', 'Add Training Course')

@section('content')

@include(
    'company.hr.training.courses.form',
    [
        'course' => null,
        'formAction' => route(
            'company.hr.training.courses.store'
        ),
        'formMethod' => 'POST',
        'pageTitle' => 'Add Training Course',
        'submitLabel' => 'Create Course',
    ]
)

@endsection