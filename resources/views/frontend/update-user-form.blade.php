@extends('layouts.frontendapp')

@section('title', config('app.name'))

@section('content')
    <div class="container mt-5">
         <div class="row justify-content-center">
        <div class="col-lg-9">
        <h2>Update Your Details</h2>
        <form action="{{ route('update-user', $user->id) }}" method="POST">
            @csrf
            @method('PUT') 

            <div class="form-group mt-2">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $user->name) }}" required>
            </div>

            <div class="form-group mt-2">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $user->lastname) }}" required>
            </div>

            <div class="form-group mt-2">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="form-group mt-2">
                <label for="company">Company</label>
                <input type="text" class="form-control" id="company" name="company" value="{{ old('company', $user->company) }}">
            </div>

            <div class="form-group mt-2">
                <label for="designation">Designation</label>
                <input type="text" class="form-control" id="designation" name="designation" value="{{ old('designation', $user->designation) }}">
            </div>

            <div class="form-group mt-2">
                <label for="bio">Bio</label>
                <textarea class="form-control" id="bio" name="bio" rows="4">{{ old('bio', $user->bio) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary mt-2 mb-2">Update Details</button>
        </form>
    </div>
</div>
    </div>
@endsection