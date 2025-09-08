@extends('layouts.frontendapp')

@section('title', config('app.name'))

@section('meta')
    <meta name="description" content="Your landing page description here.">
@endsection

@section('content')


@include('frontend.section.attendee') 
@endsection