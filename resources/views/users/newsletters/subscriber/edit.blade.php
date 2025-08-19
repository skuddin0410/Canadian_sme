@extends('layouts.admin')

@section('title')
    Admin | Edit Newsletter Subscriber
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Newsletter/</span>Subscriber</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Edit Subscriber</h5>
        </div>
        <div class="card-body">

          @if(Session::has('success'))
              <div class="alert alert-success">{{ Session::get('success') }}</div>
          @endif
          @if(Session::has('error'))
              <div class="alert alert-danger">{{ Session::get('error') }}</div>
          @endif

          <form action="{{ route('newsletter-subscribers.update', $newsletterSubscriber->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Email -->
            <div class="mb-3">
              <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $newsletterSubscriber->email) }}" required>
              </div>
              @error('email')
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>

            <!-- Name -->
            <div class="mb-3">
              <label class="form-label" for="name">Name</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-user"></i></span>
                <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $newsletterSubscriber->name) }}">
              </div>
              @error('name')
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>

            <!-- Preferences -->
            <div class="mb-3">
              <label class="form-label" for="preferences">Preferences (comma separated)</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-list-check"></i></span>
                <input type="text" class="form-control" name="preferences" id="preferences" 
                       value="{{ old('preferences', is_array($newsletterSubscriber->preferences) ? implode(',', $newsletterSubscriber->preferences) : $newsletterSubscriber->preferences) }}">
              </div>
              @error('preferences')
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>

            <!-- Tags -->
            <div class="mb-3">
              <label class="form-label" for="tags">Tags (comma separated)</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-tag"></i></span>
                <input type="text" class="form-control" name="tags" id="tags" 
                       value="{{ old('tags', is_array($newsletterSubscriber->tags) ? implode(',', $newsletterSubscriber->tags) : $newsletterSubscriber->tags) }}">
              </div>
              @error('tags')
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>

            <!-- Status -->
            <div class="mb-3">
              <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-check-circle"></i></span>
                <select class="form-control" name="status" id="status">
                  <option value="subscribed" @selected(old('status', $newsletterSubscriber->status) == 'subscribed')>Subscribed</option>
                  <option value="unsubscribed" @selected(old('status', $newsletterSubscriber->status) == 'unsubscribed')>Unsubscribed</option>
                </select>
              </div>
              @error('status')
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>

            <!-- Subscription Source -->
            <div class="mb-3">
              <label class="form-label" for="subscription_source">Subscription Source</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-globe"></i></span>
                <input type="text" class="form-control" name="subscription_source" id="subscription_source" 
                       value="{{ old('subscription_source', $newsletterSubscriber->subscription_source) }}">
              </div>
              @error('subscription_source')
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>

            <!-- Buttons -->
            <div class="d-flex pt-3 justify-content-end">
              <a href="{{ route('newsletter-subscribers.index') }}" class="btn btn-outline-primary btn-pill me-2">Cancel</a>
              <button type="submit" class="btn btn-primary btn-pill">Save</button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
