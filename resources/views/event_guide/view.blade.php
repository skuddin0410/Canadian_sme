@extends('layouts.admin')

@section('content')
<div class="container">
  <h4 class="mb-4 mt-4">Event Guide Details</h4>

  <div class="card">
    <div class="d-flex pt-3 justify-content-end">
      <a href="{{ route('event-guides.index') }}" class="btn btn-outline-primary me-2">Back</a>
    </div>
    <div class="card-body">
      <p><strong>Category:</strong> {{ $eventGuide->category }}</p>
      <p><strong>Title:</strong> {{ $eventGuide->title ?? 'N/A' }}</p>
      <p><strong>Type:</strong> {{ $eventGuide->type ?? 'N/A' }}</p>

      <p><strong>Weblink:</strong> 
        @if($eventGuide->weblink)
          <a href="{{ $eventGuide->weblink }}" target="_blank">{{ $eventGuide->weblink }}</a>
        @else
          N/A
        @endif
      </p>

      <p><strong>Document:</strong> 
        @if($eventGuide->doc)
          <a href="{{ asset('storage/'.$eventGuide->doc) }}" target="_blank" class="btn btn-sm btn-primary">
            View Document
          </a>
        @else
          N/A
        @endif
      </p>
      {{-- <p><strong>Document:</strong> 
    @if($eventGuide->doc)
        @php
            $extension = pathinfo($eventGuide->doc, PATHINFO_EXTENSION);
        @endphp

        @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
            <img src="{{ asset('storage/'.$eventGuide->doc) }}"

                 alt="Event Guide Document" 
                 class="img-fluid rounded mt-2" 
                 style="max-width: 300px;">
        @else
            <a href="{{ asset('storage/'.$eventGuide->doc) }}" 
               target="_blank" 
               class="btn btn-sm btn-primary mt-2">
               View Document
            </a>
        @endif
    @else
        N/A
    @endif
</p> --}}


      <p><strong>Created:</strong> {{ $eventGuide->created_at->format('d M Y') }}</p>
    </div>
  </div>
</div>
@endsection
