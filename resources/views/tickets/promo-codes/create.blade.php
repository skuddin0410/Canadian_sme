@extends('layouts.admin')

@section('title', 'Create Promo Code')

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <div class="row">
        <div class="col-12 mt-3">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                <h1 class="h3 mb-0">Create Promo Code</h1>
                <a href="{{ route('admin.promo-codes.index') }}" class="btn btn-outline-secondary">Back</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.promo-codes.store') }}">
                        @csrf
                        @include('tickets.promo-codes._form', ['promoCode' => null])
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Create Promo Code</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
