@extends('layouts.app')

@section('content')
<div class="container text-center py-5">
    <h2>✅ Payment Successful</h2>
    <p>You are successfully registered for:</p>
    <h4>{{ $event->title }}</h4>

    
</div>
@endsection