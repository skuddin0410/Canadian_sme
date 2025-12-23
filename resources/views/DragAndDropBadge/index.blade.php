@extends('layouts.admin')

@section('title', 'All Badges')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y pt-0 mt-3">
   
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Badge List</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#badgeModal">
            Add New Badge
        </button>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead >
                    <tr>
                        <th>Badge Name</th>
                        <th>Printer</th>
                        <th>Badge Size</th>
                        <th>Target</th>
                        <th style="width:120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($badges))
                    @foreach($badges as $badge) 
                    <tr>
                        <td>{{$badge->badge_name}}</td>
                        <td>{{$badge->printer}}</td>
                        <td>{{$badge->width}} inch X {{$badge->height}} inch</td>
                        <td>{{$badge->target}}</td>
                        <td>
                            <a href="{{route('newbadges.show',$badge->id)}}" class="btn btn-sm btn-primary">show</a>
                            <!-- <button class="btn btn-sm btn-danger">Delete</button> -->
                        </td>
                    </tr>
                    @endforeach
                    @endif
                   
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="badgeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('newbadges.store') }}" method="POST" enctype="multipart/form-data" id="badgeForm">
        @csrf
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add New Badge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form>

                    <!-- Badge Name -->
                    <div class="mb-3">
                        <label class="form-label">Badge Name</label>
                        <input type="text" name="badge_name" class="form-control" placeholder="Enter badge name">
                    </div>

                    <!-- Target -->
                    <div class="mb-3">
                        <label class="form-label">Target</label>
                        <select class="form-select" name="target">
                            <option value="">Select Target</option>
                            <option>All Attendees</option>
                            <option>VIP List</option>
                            <option>General Admission List</option>
                        </select>
                    </div>

                    <!-- Printer -->
                    <div class="mb-3">
                        <label class="form-label">Printer</label>
                        <select class="form-select" name="printer">
                            <option value="">Select Printer</option>
                            <option>Zebra</option>
                            <option>Brother</option>
                        </select>
                    </div>

                    <!-- Label Type -->
                    <div class="mb-3">
                        <label class="form-label">Size</label>
                        <select class="form-select" id="labelType">
                            <option value="">Select Label Type</option>
                            <option value="custom">Custom Size</option>
                            <!-- <option value="4x3">4 inch x 3 inch badge</option>
                            <option value="4x6">4 inch x 6 inch badge</option> -->
                        </select>
                    </div>

                    <!-- Custom Size Fields -->
                    <div class="row d-none" id="customSizeFields">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Width (inch)</label>
                            <input type="number" name="width" class="form-control" placeholder="Width">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Height (inch)</label>
                            <input type="number" name="height"  class="form-control" placeholder="Height">
                        </div>
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Save Badge</button>
            </div>

        </div>
        </form>
    </div>

</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById('labelType').addEventListener('change', function () {
        const customFields = document.getElementById('customSizeFields');
        if (this.value === 'custom') {
            customFields.classList.remove('d-none');
        } else {
            customFields.classList.add('d-none');
        }
    });
</script>
@endsection
	