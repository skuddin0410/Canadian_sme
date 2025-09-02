@extends('layouts.frontendapp')

@section('title', config('app.name'))

@section('meta')
    <meta name="description" content="Your landing page description here.">
@endsection

@section('content')
 
@include('frontend.section.banner')  

@include('frontend.section.countdown')  

@include('frontend.section.attendee') 

@include('frontend.section.speaker') 

@include('frontend.section.exhibitor')    

@include('frontend.section.sponsor') 

@include('frontend.section.schedule') 

@include('frontend.section.maps') 


@endsection