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
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Assign Booth</h4>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="booth_number" class="form-label">Booth Number</label>
                            <input type="text" name="booth_number" id="booth_number" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="booth_size" class="form-label">Booth Size</label>
                            <input type="text" name="booth_size" id="booth_size" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Assign Booth</button>
                    </form>
                </div>
            </div>

            <!-- Booth Details -->
            <div class="card">
                <div class="card-header">
                    <h4>Booth Details</h4>
                </div>
                <div class="card-body">
                    @if($company->booths->isNotEmpty())
                        <ul class="list-group">
                            @foreach($company->booths as $booth)
                                <li class="list-group-item">
                                    <strong>#{{ $booth->booth_number }}</strong> - {{ $booth->booth_size }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No booths assigned yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
