@extends('layouts.frontendapp')

@section('title', config('app.name'))

@section('meta')
    <meta name="description" content="Your landing page description here.">
@endsection

@section('content')
 
@include('frontend.section.banner')  

@include('frontend.section.countdown')  

@php
    $allPossibleOrderable = ['attendee', 'speaker', 'exhibitor', 'sponsor'];
    $order = json_decode($event->section_order, true);
    if (empty($order) || !is_array($order)) {
        $order = $allPossibleOrderable;
    } else {
        // Ensure all 4 are present even if some was missing from saved order
        $extra = array_diff($allPossibleOrderable, $order);
        $order = array_merge($order, $extra);
    }
@endphp

@foreach($order as $section)
    @if(in_array($section, $allPossibleOrderable) && view()->exists('frontend.section.' . $section))
        @include('frontend.section.' . $section)
    @endif
@endforeach

@include('frontend.section.gallery') 

@include('frontend.section.schedule') 

@include('frontend.section.maps') 

@endsection