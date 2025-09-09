@extends('layouts.admin')
@section('title')
    Admin | Email Settings
@endsection
@section('content')

<div class="container-xxl flex-grow-1 container-p-y pt-0">
	<h4 class="py-3 mb-4"><span class="text-muted fw-light">Email Template</h4>
	<div class="row">
	    <div class="col-xl">
	      <div class="card mb-4">
	      	<div class="card-header d-flex justify-content-between align-items-center">
               
               <div class="card-body">
               	<form  action="{{route('email-template-settings')}}" method="POST" enctype="multipart/form-data">
                 {{ csrf_field() }}
	                 <div class="row">
					        <div class="col-12">
				             <div class="mb-3">
				              <label class="form-label" for="title">Subject<span class="text-danger">*</span></label>
				              <div class="input-group input-group-merge">
				                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
				                <input
				                  type="text"
				                  class="form-control"
				                  name="subject"
				                  id="slug-source"
				                  value="{{ old('subject',getKeyValue('email_subject')->value ?? '') }}"
				                  placeholder="Email Subject"/>
				              </div>
				              @if ($errors->has('title'))
				                <span class="text-danger text-left">{{ $errors->first('subject') }}</span>
				              @endif
				            </div>
				            </div>
				            <input type="hidden" name="mode" value="save">
                             <div class="col-12">
								<div class="mb-3">
					               <label class="form-label" for="title">Content<span class="text-danger">*</span></label>
					              <div class="input-group input-group-merge"  style="height: 300px;">
					                <textarea
					                  type="text"
					                  name="content"
					                  id="content"
					                  class="form-control"
					                  placeholder="Email content"
					                  rows="8" cols="50"
					                >{{ old('content', getKeyValue('email_content')->value ?? '') }}</textarea>
					              </div>
					              @if ($errors->has('content'))
					                <span class="text-danger text-left">{{ $errors->first('content') }}</span>
					              @endif
					            </div>
					        </div>
	                 </div>

			        <div class="d-flex pt-3 justify-content-end"> 
		              <button type="submit" class="btn btn-primary btn-streach font-book mt-6 fs-14 add_user">Save</button>
		            </div>
                </form>
               </div>
            </div>
	      </div>
	    </div>
    </div>
</div>

@endsection