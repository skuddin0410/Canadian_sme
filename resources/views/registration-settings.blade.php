@extends('layouts.admin')

@section('title')
    Admin | Settings
@endsection
@section('content')

<div class="container-xxl flex-grow-1 container-p-y pt-0">
<form method="POST" action="#" class=" py-3">
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm p-2">
  @csrf
  <input type="hidden" name="mode" value="save"/>
  <ul class="nav nav-tabs" id="siteSettingsTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="org-tab" data-bs-toggle="tab" data-bs-target="#org" type="button" role="tab" aria-controls="org" aria-selected="true">
        Organization Info
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="privacy-tab" data-bs-toggle="tab" data-bs-target="#privacy" type="button" role="tab" aria-controls="privacy" aria-selected="false">
        Privacy Policy
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="terms-tab" data-bs-toggle="tab" data-bs-target="#terms" type="button" role="tab" aria-controls="terms" aria-selected="false">
        T&amp;C
      </button>
    </li>

    <li class="nav-item" role="presentation">
      <button class="nav-link" id="about-tab" data-bs-toggle="tab" data-bs-target="#about" type="button" role="tab" aria-controls="about" aria-selected="false">
       About
      </button>
    </li>

    <li class="nav-item" role="presentation">
      <button class="nav-link" id="location-tab" data-bs-toggle="tab" data-bs-target="#location" type="button" role="tab" aria-controls="location" aria-selected="false">
       Location
      </button>
    </li>


    <li class="nav-item" role="presentation">
      <button class="nav-link" id="thankyou-tab" data-bs-toggle="tab" data-bs-target="#thankyou" type="button" role="tab" aria-controls="thankyou" aria-selected="false">
        Thank You Page
      </button>
    </li>
  </ul>

  <div class="tab-content border border-top-0 p-3" id="siteSettingsTabsContent">
    <!-- Organization Info -->
    <div class="tab-pane fade show active" id="org" role="tabpanel" aria-labelledby="org-tab" tabindex="0">
      <div class="row g-3">
        <div class="col-md-6">

          <label for="company_name" class="form-label">Company Name</label>
          <input type="text" class="form-control" id="company_name" name="company_name" value="{{getKeyValue('company_name')->value}}" required>
        </div>

        <div class="col-md-6">
          <label for="support_email" class="form-label">Support Email</label>
          <input type="email" class="form-control" id="support_email" name="support_email" value="{{getKeyValue('support_email')->value}}" required>
        </div>

        <div class="col-12">
          <label for="company_address" class="form-label">Company Address</label>
          <textarea class="form-control" id="company_address" name="company_address" rows="3" required>{{getKeyValue('company_address')->value}}</textarea>
        </div>

        <div class="col-md-6">
          <label for="tax_name" class="form-label">Tax Name</label>
          <input type="text" class="form-control" id="tax_name" name="tax_name" value="{{getKeyValue('tax_name')->value}}" placeholder="GST / VAT / CIN" required>
        </div>

        <div class="col-md-3">
          <label for="tax_percentage" class="form-label">Tax Percentage (%)</label>
          <input type="number" step="0.01" min="0" max="100" class="form-control" id="tax_percentage" name="tax_percentage" value="{{getKeyValue('tax_percentage')->value}}" required>
        </div>

        <div class="col-md-3">
          <label for="company_number" class="form-label">Company Number (VAT/CIN/GST)</label>
          <input type="text" class="form-control" id="company_number" name="company_number" value="{{getKeyValue('company_number')->value}}" required>
        </div>
      </div>
    </div>

    <!-- Privacy Policy -->
    <div class="tab-pane fade" id="privacy" role="tabpanel" aria-labelledby="privacy-tab" tabindex="0">
     

      <label for="privacy_policy" class="form-label">Privacy Policy Content</label>
      <p><a href="{{ config('app.url')}}privacy_policy"/>{{ config('app.url')}}privacy_policy</a> </p>
      <textarea class="form-control" id="privacy_policy" name="privacy_policy" rows="12" placeholder="Paste or write your Privacy Policy here...">{{getKeyValue('privacy_policy')->value}}</textarea>
    </div>

    <!-- Terms & Conditions -->
    <div class="tab-pane fade" id="terms" role="tabpanel" aria-labelledby="terms-tab" tabindex="0">
      <label for="terms_conditions" class="form-label">Terms &amp; Conditions Content</label>
      <p><a href="{{ config('app.url')}}terms_conditions"/>{{ config('app.url')}}terms_conditions </a></p>
      <textarea class="form-control" id="terms_conditions" name="terms_conditions" rows="12" placeholder="Paste or write your Terms & Conditions here...">{{getKeyValue('terms_conditions')->value}}</textarea>
    </div>

    <!-- about -->
    <div class="tab-pane fade" id="about" role="tabpanel" aria-labelledby="about-tab" tabindex="0">
      <label for="about" class="form-label">About</label>
      <p><a href="{{ config('app.url')}}about"/>{{ config('app.url')}}about </a></p>
      <textarea class="form-control" id="about" name="about" rows="12" placeholder="Paste or write about us here...">{{getKeyValue('about')->value}}</textarea>
    </div>

     <!-- about -->
    <div class="tab-pane fade" id="location" role="tabpanel" aria-labelledby="location-tab" tabindex="0">
      <label for="location" class="form-label">Location</label>
      <p><a href="{{ config('app.url')}}location"/>{{ config('app.url')}}location </a></p>
      <textarea class="form-control" id="location" name="location" rows="12" placeholder="Paste or write location us here...">{{getKeyValue('location')->value}}</textarea>
    </div>


    <!-- Thank You Page -->
    <div class="tab-pane fade" id="thankyou" role="tabpanel" aria-labelledby="thankyou-tab" tabindex="0">
      <label for="thank_you_page" class="form-label">Thank You Page Message / HTML</label>
      <p><a href="{{ config('app.url')}}thank_you_page"/>{{ config('app.url')}}thank_you_page </a></p>
      <textarea class="form-control" id="thank_you_page" name="thank_you_page" rows="8" placeholder="">{{getKeyValue('thank_you_page')->value}}</textarea>
      <div class="form-text"></div>
    </div>
  </div>

  <div class="d-flex justify-content-end gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Save Settings</button>
  </div>
  </div>
</div>
</div>
</form>
</div>
@endsection