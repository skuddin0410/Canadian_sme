@extends('layouts.frontendapp')

@section('title', config('app.name'))
@section('content')
<div class="container py-5">
  <div class="row">
    <div class="col-lg-8">

      <!-- Session Details Card -->
      <div class="card shadow-sm rounded-4">
        <div class="card-body p-4">

          <!-- Title + Badge -->
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">AI in Healthcare</h3>
            <span class="badge bg-success">Room A</span>
          </div>

          <!-- Meta Info -->
          <ul class="list-unstyled mb-4">
            <li class="mb-2"><i class="fas fa-user me-2 text-primary"></i><strong>Speaker:</strong> Sarah Johnson</li>
            <li class="mb-2"><i class="fas fa-clock me-2 text-primary"></i><strong>Time:</strong> 10:15 AM – 11:00 AM</li>
            <li><i class="fas fa-calendar me-2 text-primary"></i><strong>Date:</strong> September 10, 2025</li>
          </ul>

          <!-- Description -->
          <h5 class="mb-2">About this Session</h5>
          <p class="text-muted">
            This session will explore the role of Artificial Intelligence in revolutionizing healthcare. 
            Topics include diagnostic tools, predictive analytics, patient data security, and how AI can 
            enhance clinical workflows to improve patient outcomes.
          </p>

          <!-- Action Buttons -->
          <div class="mt-4">
           <!--  <a href="#" class="btn btn-primary me-2"><i class="fas fa-ticket-alt me-1"></i> Register</a>
            <a href="#" class="btn btn-outline-secondary"><i class="fas fa-calendar-plus me-1"></i> Add to Calendar</a> -->
          </div>

        </div>
      </div>

    </div>

    <div class="col-lg-4">
       <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white fw-bold"><i class="fas fa-users me-2 text-primary"></i>Speakers</div>
        <ul class="list-group list-group-flush">
          <li class="list-group-item d-flex align-items-center">
            <img src="https://via.placeholder.com/40" class="rounded-circle me-2" alt="Sarah Johnson">
            <div>
              <strong>Sarah Johnson</strong><br>
              <small class="text-muted">AI Specialist</small>
            </div>
          </li>
          <li class="list-group-item d-flex align-items-center">
            <img src="https://via.placeholder.com/40" class="rounded-circle me-2" alt="Michael Lee">
            <div>
              <strong>Michael Lee</strong><br>
              <small class="text-muted">Cybersecurity Expert</small>
            </div>
          </li>
        </ul>
      </div>

      <!-- Other Sessions -->
      <div class="card shadow-sm rounded-4">
        <div class="card-header bg-white fw-bold"><i class="fas fa-calendar-day me-2 text-primary"></i>Other Sessions</div>
        <ul class="list-group list-group-flush">
          <li class="list-group-item">
            <strong>Opening Keynote</strong><br>
            <small class="text-muted">09:00 AM – 10:00 AM · Main Hall</small>
          </li>
          <li class="list-group-item">
            <strong>Cybersecurity Trends</strong><br>
            <small class="text-muted">11:15 AM – 12:00 PM · Room B</small>
          </li>
        </ul>
      </div>
    </div>
    </div>

  </div>
</div>
@endsection