@extends('layouts.admin')
@section('title')
    Admin | Email & Notifications Settings
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h2>Edit Email Template</h2>
       <div class="row">
    <div class="col-xl">
    <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
    <div class="card-body"> 
    <form action="{{ route('email-templates.update', $emailTemplate->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Template Name</label>
            <input type="text" name="template_name" value="{{ $emailTemplate->template_name }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Subject</label>
            <input type="text" name="subject" value="{{ $emailTemplate->subject }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Type</label>
            <select name="type" class="form-control" required>
                <option value="email" {{ $emailTemplate->type == 'email' ? 'selected' : '' }}>Email</option>
                <option value="notifications" {{ $emailTemplate->type == 'notifications' ? 'selected' : '' }}>Notifications</option>
            </select>

        </div>
        <div class="mb-3">
            <label>Message</label>
            <textarea name="message" class="form-control" rows="20" required>{{ str_replace('<br>', "\n", $emailTemplate->message) }}</textarea>
        </div>

        <button class="btn btn-success">Update</button>
    </form>
    </div>
</div>
</div>
</div>
</div>
</div>
@endsection
