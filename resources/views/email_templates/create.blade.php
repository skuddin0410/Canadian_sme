@extends('layouts.admin')
@section('title')
    Admin | Email & Notifications Settings
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
<div class="container">
    <h2 class="mt-3">Create Email Template</h2>

    <div class="row">
    <div class="col-xl">
    <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
    <div class="card-body"> 
    <form action="{{ route('email-templates.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Template Name</label>
            <input type="text" name="template_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Subject</label>
            <input type="text" name="subject" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Type</label>
                <select name="type" class="form-control" required>
                    <option value="email">Email</option>
                    <option value="notifications">Notifications</option>
                </select>

        </div>
        <div class="mb-3">
            <label>Message(supported tags: name, site_name, profile_update_link, site_name, qr_code)</label>
            <textarea name="message" class="form-control" rows="20" required></textarea>
        </div>
        <button class="btn btn-success">Save</button>
    </form>
        </div>
</div>
</div>
</div>
</div>
</div>
@endsection
