@extends('layouts.admin')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Update Landing Page Settings</h4>
    <div class="row">

        <div class="col-xl">
          <div class="card mb-4">

          @if(Session::has('success'))
              <div class="alert alert-success">
              {{ Session::get('success') }}
              </div>
          @endif
          @if(Session::has('error'))
              <div class="alert alert-danger">
              {{ Session::get('error') }}
              </div>
          @endif
            <div class="card-header d-flex justify-content-between align-items-center">
               
               <div class="card-body">

    <h2>Update Landing Page Settings</h2>
    <a href="{{ route('app-landing') }} " target="_blank" class="mb-2"> App Landing: {{ route('app-landing') }} </a>
    <form action="{{ route('landing-page-settings') }}" method="POST">
        @csrf 

        <!-- Title Field -->
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ $setting->title ?? '' }}" required>
        </div>

        <!-- Date Field -->
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" id="date" name="date" value="{{ $setting->date ?? '' }}" required>
        </div>

        <!-- Location Field -->
        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" class="form-control" id="location" name="location" value="{{ $setting->location ?? '' }}" required>
        </div>

        <!-- Website Field -->
        <div class="mb-3">
            <label for="website" class="form-label">Website</label>
            <input type="url" class="form-control" id="website" name="website" value="{{ $setting->website ?? '' }}" required>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Update Settings</button>
    </form>
 </div>
            </div>
          </div>
        </div>
    </div>
</div>
@endsection