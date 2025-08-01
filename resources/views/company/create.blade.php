@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Add Company Info</h2>

    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <form action="{{ route('company.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Company Name</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
        </div>

        <div class="mb-3">
            <label for="industry" class="form-label">Industry</label>
            <input type="text" name="industry" class="form-control" value="{{ old('industry') }}">
        </div>

        <div class="mb-3">
            <label for="size" class="form-label">Company Size</label>
            <input type="text" name="size" class="form-control" value="{{ old('size') }}">
        </div>

        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" name="location" class="form-control" value="{{ old('location') }}">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Company Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
        </div>
        

        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('company.details') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
