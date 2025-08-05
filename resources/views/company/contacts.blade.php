@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">Company / Contact Management</h4>

  @if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
  @endif

  <form method="POST" action="{{ route('company.contacts.store') }}">
    @csrf
    <div class="row g-3 mb-4">
      {{-- <div class="col-md-3">
        <input type="text" name="type" class="form-control" placeholder="Contact Type (e.g. Sales)" required>
      </div> --}}
      <div class="col-md-3">
        <input type="text" name="name" class="form-control" placeholder="Name" required>
      </div>
      <div class="col-md-3">
        <input type="email" name="email" class="form-control" placeholder="Email">
      </div>
      <div class="col-md-3">
        <input type="text" name="phone" class="form-control" placeholder="Phone">
      </div>
    
        <div class="col-md-3">
        <button type="submit" class="btn btn-primary">Add Contact</button>
      </div>
    </div>
  </form>

  <table class="table table-bordered">
    <thead>
      <tr>
        {{-- <th>Type</th> --}}
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($contacts as $contact)
        <tr>
          {{-- <td>{{ $contact->type }}</td> --}}
          <td>{{ $contact->name }}</td>
          <td>{{ $contact->email }}</td>
          <td>{{ $contact->phone }}</td>
          <td>
            <form action="{{ route('company.contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Delete this contact?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="5">No contacts found.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
