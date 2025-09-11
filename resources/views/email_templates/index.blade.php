@extends('layouts.admin')
@section('title')
    Admin | Email & Notifications Settings
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h2>Email Templates</h2>

    <div class="row">
    <div class="col-xl">
    <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
    <div class="card-body">
    <a href="{{ route('email-templates.create') }}" class="btn btn-primary mb-3">Add Template</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Template Name</th>
            <th>Subject</th>
            <th>Type</th>
            <th>Actions</th>
        </tr>
        @foreach ($templates as $template)
            <tr>
                <td>{{ $template->id }}</td>
                <td>{{ $template->template_name }}</td>
                <td>{{ $template->subject }}</td>
                <td>{{ $template->type }}</td>
                <td>
                    <a href="{{ route('email-templates.edit', $template->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('email-templates.destroy', $template->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>

    {{ $templates->links() }}
</div>
</div>
</div>
</div>
</div>
</div>
@endsection
