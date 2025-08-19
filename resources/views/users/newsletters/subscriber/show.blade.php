@extends('layouts.admin')

@section('title')
    Admin | Newsletter Subscriber Details
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Newsletter/</span>Subscriber</h4>

    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-body">

                    <!-- Action Buttons -->
                    <div class="d-flex pt-3 justify-content-end gap-2">
                        <a href="{{ route('newsletter-subscribers.edit',  $newsletterSubscriber->id) }}" 
                           class="btn btn-outline-primary btn-pill btn-streach font-book fs-14 mt-6">Edit</a>
                        <a href="{{ route('newsletter-subscribers.index') }}" 
                           class="btn btn-outline-primary btn-pill btn-streach font-book fs-14 mt-6">Back</a>
                    </div>

                    <h5 class="pb-2 border-bottom mb-4 mt-3">Subscriber Details</h5>

                    <div class="info-container">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <span class="fw-medium me-2">Name:</span>
                                <span>{{ $newsletterSubscriber->name ?? '-' }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-medium me-2">Email:</span>
                                <span>{{ $newsletterSubscriber->email }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-medium me-2">Preferences:</span>
                                <span>{{ is_array($newsletterSubscriber->preferences) ? implode(', ', $newsletterSubscriber->preferences) : ($newsletterSubscriber->preferences ?? '-') }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-medium me-2">Tags:</span>
                                <span>{{ is_array($newsletterSubscriber->tags) ? implode(', ', $newsletterSubscriber->tags) : ($newsletterSubscriber->tags ?? '-') }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-medium me-2">Status:</span>
                                <span>{{ ucfirst($newsletterSubscriber->status) }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-medium me-2">Subscription Source:</span>
                                <span>{{ $newsletterSubscriber->subscription_source ?? '-' }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-medium me-2">Subscribed At:</span>
                                <span>{{$newsletterSubscriber->subscribed_at ? $newsletterSubscriber->subscribed_at->format('d M Y H:i') : '-' }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-medium me-2">Unsubscribed At:</span>
                                <span>{{ $newsletterSubscriber->unsubscribed_at ? $newsletterSubscriber->unsubscribed_at->format('d M Y H:i') : '-' }}</span>
                            </li>
                        </ul>
                    </div>

                  

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
