@extends('layouts.frontendapp')

@section('title', config('app.name'))
@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
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
                <div class="card-header">Support Request</div>
                <div class="card-body">
                    <form action="{{ route('support.submit') }}" method="POST">
                        @csrf

                        <!-- Subject -->
                        <div class="form-group mt-4">
                            <label for="subject">Subject</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="form-group mt-4">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-group mt-4">
                            <label for="email">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary mt-4">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
