@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Reset Password</h2>
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <button type="submit" class="btn btn-primary mt-3">Send Reset Link</button>
    </form>
</div>
@endsection
