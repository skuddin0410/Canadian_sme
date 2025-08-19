@extends('layouts.admin')

@section('title', 'Manage Subscribers')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Newsletter Subscribers</h5>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary">Back to Dashboard</a>
        </div>
        <div class="card-body">
            <p>This page will allow you to manage your newsletter subscribers.</p>

            <table class="table table-bordered table-hover mt-3">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Email</th>
                        <th>Subscribed At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Sample static data — replace with dynamic --}}
                    <tr>
                        <td>1</td>
                        <td>subscriber1@example.com</td>
                        <td>2025-08-14</td>
                        <td>
                            <button class="btn btn-sm btn-danger">Unsubscribe</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>subscriber2@example.com</td>
                        <td>2025-08-14</td>
                        <td>
                            <button class="btn btn-sm btn-danger">Unsubscribe</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
