@extends('layouts.admin')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6 text-center">
    <div class="mb-6">
        <div class="text-6xl mb-4">✓</div>
        <h2 class="text-2xl font-bold text-gray-800">Successfully Unsubscribed</h2>
        <p class="text-gray-600 mt-2">{{ $email }} has been removed from our newsletter list.</p>
    </div>

    <div class="space-y-4">
        <p class="text-sm text-gray-600">
            Sorry to see you go! If you change your mind, you can always 
            <a href="{{ route('subscribe-form') }}" class="text-blue-600 hover:text-blue-800">subscribe again</a>.
        </p>
        
        <a href="/" class="inline-block bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
            Return to Homepage
        </a>
    </div>
</div>
@endsection