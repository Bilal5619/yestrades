@extends('company.layouts.app')

@section('title', 'Edit Training Course')

@section('content')

@include(
    'company.hr.training.courses.form',
    [
        'course' => $course,
        'formAction' => route(
            'company.hr.training.courses.update',
            $course
        ),
        'formMethod' => 'PUT',
        'pageTitle' => 'Edit Training Course',
        'submitLabel' => 'Update Course',
    ]
)

@endsection