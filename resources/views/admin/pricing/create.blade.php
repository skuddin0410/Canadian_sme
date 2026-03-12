@extends('layouts.admin')

@section('title')
Admin | Add New Pricing
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Pricing / Setup /</span> Add New</h4>

    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">New Pricing Plan</h5>
                    <small class="text-muted float-end">Fill in the details below</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.pricing.setup.store') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="e.g., Basic Plan" required />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="amount">Amount ($)</label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" placeholder="e.g., 99.00" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="attendee_count">Attendee Count</label>
                                <input type="number" class="form-control" id="attendee_count" name="attendee_count" placeholder="e.g., 50" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="timespan">Timespan (Months)</label>
                                <input type="number" class="form-control" id="timespan" name="timespan" placeholder="e.g., 2" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="event_no">Event No</label>
                                <input type="number" class="form-control" id="event_no" name="event_no" value="1" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="order_by">Order</label>
                                <input type="number" class="form-control" id="order_by" name="order_by" value="0" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="pricing_description">Features (Press Enter for new point)</label>
                            <textarea id="pricing_description" name="description" class="form-control" rows="5" placeholder="Feature 1&#10;Feature 2&#10;Feature 3"></textarea>
                            <small class="text-muted">Enter each point on a new line.</small>
                        </div>

                        <div class="row mb-3 mt-4">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="mostpopular" name="mostpopular" value="1">
                                    <label class="form-check-label" for="mostpopular">Most Popular Badge</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="status" name="status" value="1" checked>
                                    <label class="form-check-label" for="status">Status (Active/Inactive)</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Save Pricing</button>
                            <a href="{{ route('admin.pricing.setup.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
