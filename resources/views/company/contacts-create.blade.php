@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">Add New Contact</h4>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('company.contacts.store') }}">
    @csrf
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label>Email</label>
        <input type="email" name="email" class="form-control">
      </div>
      <div class="col-md-4">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control">
      </div>
    </div>

    <div class="mt-3">
      <button type="submit" class="btn btn-primary">Save Contact</button>
      <a href="{{ route('company.contacts') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
