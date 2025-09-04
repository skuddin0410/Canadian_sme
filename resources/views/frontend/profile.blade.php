@extends('layouts.frontendapp')

@section('title', config('app.name'))
@section('content')

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      
      <!-- Profile Card -->
      <div class="card shadow-sm rounded-4">
        <div class="card-body p-4">
          <div class="d-flex align-items-center mb-4">
            <img src="https://via.placeholder.com/100" 
                 alt="Profile Picture" 
                 class="rounded-circle me-3 border border-3 border-primary" width="100" height="100">
            <div>
              <h4 class="mb-1">John Doe</h4>
              <span class="badge bg-success">Active</span>
            </div>
          </div>

          <!-- Profile Info -->
          <div class="row g-3">
            <div class="col-sm-6">
              <p class="mb-1 text-muted"><i class="fas fa-envelope me-2 text-primary"></i>Email</p>
              <p class="fw-semibold">johndoe@example.com</p>
            </div>
            <div class="col-sm-6">
              <p class="mb-1 text-muted"><i class="fas fa-phone me-2 text-primary"></i>Phone</p>
              <p class="fw-semibold">+91 98765 43210</p>
            </div>
            <div class="col-sm-6">
              <p class="mb-1 text-muted"><i class="fas fa-briefcase me-2 text-primary"></i>Position</p>
              <p class="fw-semibold">Software Engineer</p>
            </div>
            <div class="col-sm-6">
              <p class="mb-1 text-muted"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Location</p>
              <p class="fw-semibold">Bangalore, India</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
  <div class="list-group shadow-sm rounded-4">
    <!-- Session Item -->
    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
      <div class="ms-2 me-auto">
        <div class="fw-bold">Opening Keynote</div>
        <small class="text-muted"><i class="fas fa-user me-1 text-primary"></i>Dr. John Smith</small><br>
        <small class="text-muted"><i class="fas fa-clock me-1 text-primary"></i>09:00 AM – 10:00 AM</small>
      </div>
      <span class="badge bg-primary rounded-pill align-self-center">Main Hall</span>
    </div>

    <!-- Session Item -->
    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
      <div class="ms-2 me-auto">
        <div class="fw-bold">AI in Healthcare</div>
        <small class="text-muted"><i class="fas fa-user me-1 text-primary"></i>Sarah Johnson</small><br>
        <small class="text-muted"><i class="fas fa-clock me-1 text-primary"></i>10:15 AM – 11:00 AM</small>
      </div>
      <span class="badge bg-success rounded-pill align-self-center">Room A</span>
    </div>

    <!-- Session Item -->
    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
      <div class="ms-2 me-auto">
        <div class="fw-bold">Cybersecurity Trends</div>
        <small class="text-muted"><i class="fas fa-user me-1 text-primary"></i>Michael Lee</small><br>
        <small class="text-muted"><i class="fas fa-clock me-1 text-primary"></i>11:15 AM – 12:00 PM</small>
      </div>
      <span class="badge bg-warning text-dark rounded-pill align-self-center">Room B</span>
    </div>
  </div>
    </div> 	
  </div>
</div>

@endsection