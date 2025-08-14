@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto bg-white rounded-lg shadow-md p-6">
    <section class="py-24">
        <div class="container">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold mb-6">Subscribe to Our Newsletter</h1>
                <p class="text-lg">
                   Get the latest real estate investment insights and opportunities delivered to your inbox.
                </p>
            </div>
        </div>
    </section>
   
    <section class="py-8 bg-white">
    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-4">
        @csrf
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="your@email.com">
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name (optional)</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Your Name">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Preferences</label>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="checkbox" name="preferences[]" value="weekly_updates" 
                           {{ in_array('weekly_updates', old('preferences', [])) ? 'checked' : '' }}
                           class="mr-2">
                    Weekly Market Updates
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="preferences[]" value="new_properties" 
                           {{ in_array('new_properties', old('preferences', [])) ? 'checked' : '' }}
                           class="mr-2">
                    New Property Listings
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="preferences[]" value="investment_tips" 
                           {{ in_array('investment_tips', old('preferences', [])) ? 'checked' : '' }}
                           class="mr-2">
                    Investment Tips & Guides
                </label>
               {{--  <label class="flex items-center">
                    <input type="checkbox" name="preferences[]" value="events" 
                           {{ in_array('events', old('preferences', [])) ? 'checked' : '' }}
                           class="mr-2">
                    Event Announcements
                </label> --}}
            </div>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 font-medium">
            Subscribe Now
        </button>
    </form>

    <p class="text-xs text-gray-500 text-center mt-4">
        We respect your privacy. You can unsubscribe at any time.
    </p>
</section>
</div>
@endsection