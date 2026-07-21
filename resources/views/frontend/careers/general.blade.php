@extends('frontend.layout')

@section('pageHeading', 'Send Your CV | Careers at Yes Trades')
@section('metaDescription', 'Send your CV to Yes Trades for future career opportunities.')
@section('content')
  <main class="pt-100 pb-100"><div class="container"><div class="row justify-content-center"><div class="col-lg-9"><h1>Send Your CV</h1><p class="lead">Share your details for future opportunities with Yes Trades.</p><div class="card border-0 shadow-sm"><div class="card-body"><form method="POST" action="{{ route('careers.general.store') }}" enctype="multipart/form-data" novalidate>@csrf<input type="text" name="website" value="" class="d-none" tabindex="-1" autocomplete="off" aria-hidden="true">@include('frontend.careers.partials.fields', ['general' => true])</form></div></div></div></div></div></main>
@endsection
