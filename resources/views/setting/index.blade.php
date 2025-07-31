@extends('layouts.admin')

@section('title')
    Admin | Settings
@endsection

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Settings</span></h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
			@if(!empty($setting))	
			<div class="card-header d-flex justify-content-between align-items-center">
				<h5 class="mb-0"> Update  Setting</h5>
		    </div> 
            <div class="card-body pt-0">	
	            <div class="row"> 
			      @if(!empty($setting))
		             <form  action="{{route('settings.update',["setting"=>$setting->id])}}" method="POST" enctype="multipart/form-data">
		          
		          @endif 	
		            {{ csrf_field() }}
		            @if(!empty($setting))
                       @method('PUT')
                    @endif
                    <div class="row">
		            <div class="col-6">  	
			            <div class="mb-3">
			              <label class="form-label" for="title">Key<span class="text-danger">*</span></label>
			              <div class="input-group input-group-merge">
			                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
			                <input
			                  type="text"
			                  class="form-control"
			                  name="key"
			                  id="key"
			                  value="{{ $setting->key ?? old('key') }}"
			                  placeholder="Key"
			                  readonly>
			              </div>
			              @if ($errors->has('key'))
			                <span class="text-danger text-left">{{ $errors->first('key') }}</span>
			              @endif
			            </div>
		            </div>
		            <div class="col-6">  	
			            <div class="mb-3">
			              <label class="form-label" for="title">Value<span class="text-danger">*</span></label>
			              <div class="input-group input-group-merge">
			                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
			                <input
			                  type="text"
			                  class="form-control"
			                  name="value"
			                  id="value"
			                  value="{{ $setting->value ?? old('value') }}"
			                  placeholder="Value"/>
			              </div>
			              @if ($errors->has('value'))
			                <span class="text-danger text-left">{{ $errors->first('value') }}</span>
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
			@endif
				<div class="card-header d-flex justify-content-between align-items-center">
				    <h5 class="mb-0">Setting Lists</h5>
				</div>
				<div class="card-body pt-0">
					@if(Session::has('success'))
		              <div class="alert alert-success">
		              {{ Session::get('success') }}
		              </div>
		          @endif
		          @if(Session::has('error'))
		              <div class="alert alert-danger">
		              {{ Session::get('error') }}
		              </div>
		          @endif
					<table id="post-manager" class="stripe row-border order-column dataTable no-footer table table-striped table-bordered dt-responsive display nowrap">
					<thead>
						<tr>
							<th width="47%">Key</th>
							<th width="47%">Value</th>
							<th width="6%">Action</th>
						</tr>
                        @foreach($settings as $setting)
					    <tr> 
					    	<th style="text-transform:none">{{$setting->key}}</th>
							<th style="text-transform:none">{{$setting->value ?? ''}}</th>
							<th>
							<div class="row">
							    <div class="col-6 p-1">	
									<a href="{{ route("settings.edit",["setting"=> $setting->id ]) }}" class="btn btn-sm btn-icon item-edit"><i class="bx bxs-edit"></i></a>
								</div>	
							    {{-- <div class="col-6 p-1">
								<form action="{{ route('settings.destroy', $setting->id) }}" method="post">
			                      @csrf
			                      @method('DELETE')
			                      <button type="submit" class="btn btn-sm btn-icon btn-danger delete" onclick="return confirm('Are you sure you want to delete?')"><i class="bx bxs-trash"></i></button>
			                    </form>
							   </div> --}}

                            </div>	
                           </th>
						</tr>
						@endforeach
						@if(count($settings) <=0)
						    <tr>
                              <td colspan="14">No data available</td>
                            </tr>
						@endif
					</thead>
					</table>
					<div class="d-flex pt-3 justify-content-end">
	                   {{ $settings->links('pagination::bootstrap-4') }}
	                </div>
		        </div>
			</div>
		</div>
    </div>
</div>
@endsection
@section('scripts')
 <script>
  $("#key_value").keyup(function() {
      var Text = $(this).val();
      Text = slugify(Text);
      $("#key").val(Text);        
  });

function slugify(str) {
  str = str.replace(/^\s+|\s+$/g, ''); // trim leading/trailing white space
  str = str.toLowerCase(); // convert string to lowercase
  str = str.replace(/[^a-z0-9 -]/g, '') // remove any non-alphanumeric characters
           .replace(/\s+/g, '-') // replace spaces with hyphens
           .replace(/-+/g, '-'); // remove consecutive hyphens
  return str.replace(/^-+|-+$/g, '');        
}
</script
@endsection