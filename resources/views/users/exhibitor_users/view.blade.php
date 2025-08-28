@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="row">
        <!-- Left section: profile/details -->
        <div class="col-md-8">
            <div class="card mb-4">
                
                <div class="card-header">
                    <h4>Company Details</h4>
                </div>
                
                
                <div class="card-body">
                    <!-- Company Profile Info -->
                    <p><strong>Name:</strong> {{ $company->name }}</p>
                    <p><strong>Email:</strong> {{ $company->email }}</p>
                    <p><strong>Phone:</strong> {{ $company->phone }}</p>
                    <p><strong>Description:</strong> {{ $company->description }}</p>
                    <p><strong>Website:</strong> <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a></p>
                </div>
            </div>
        </div>

        <!-- Right section: Booth assignment -->
        <div class="col-md-4">
            <!-- Assign Booth Form -->
             <div class="card">
    <div class="d-flex pt-3 justify-content-end">
    <a href="{{ route('exhibitor-users.index') }}" class="btn btn-outline-primary me-2">Back</a>
    </div>
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Assign Booth</h4>
                </div>
                <div class="card-body">
                    <form action="{{route('exhibitor-users.assign-booth-form',$user->id)}}" method="POST">
                        @csrf
                         <div class="mb-3">
                          <label for="booth_title" class="form-label">Booth Title</label>
                          <input type="text" name="booth_title" id="booth_title" class="form-control" required>
                         </div>
                          <div class="mb-3">
                <label for="booth_id" class="form-label">Booth Number</label>
                <select name="booth_id" id="booth_id" class="form-control" required>
                    <option value="">Select Booth Number</option>
                    @foreach($booths as $booth)
                        <option value="{{ $booth->id }}">{{ $booth->booth_number }}</option>
                    @endforeach
                </select>
            </div>
                        <button type="submit" class="btn btn-primary w-100">Assign Booth</button>
                    </form>
                </div>
            </div>
        </div>

            <!-- Booth Details -->
          @if($company->boothUsers && $company->boothUsers->count())
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Booth Number</th>
                <th>Booth Title</th>
                <th>Size</th>
            </tr>
        </thead>
        <tbody>
            @foreach($company->boothUsers as $bu)
                <tr>
                    <td>{{ $bu->booth->booth_number ?? '-' }}</td>
                    <td>{{ $bu->booth->title ?? '-' }}</td>
                    <td>{{ $bu->booth->size ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>No booths assigned yet.</p>
@endif

        </div>
    </div>
</div>
@endsection
