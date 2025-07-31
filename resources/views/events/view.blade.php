@extends('layouts.app')

@section('content')
<h1>{{ $event->title }}</h1>

<p><strong>Location:</strong> {{ $event->location }}</p>
<p><strong>Status:</strong> {{ ucfirst($event->status) }}</p>
<p><strong>Start:</strong> {{ $event->start_date }}</p>
<p><strong>End:</strong> {{ $event->end_date }}</p>
<p>{{ $event->description }}</p>

<a href="{{ route('events.edit', $event) }}">Edit</a>
<a href="{{ route('events.index') }}">Back to list</a>
@endsection
