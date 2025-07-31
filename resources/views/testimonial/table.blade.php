<style>
.custom_pagination {
  /* display: inline-block; */
  float: right;
  margin: 10px;
}
.custom_pagination a {
  color: #78818b;
  float: left;
  padding: 8px 16px;
  text-decoration: none;
  border: 1px solid transparent;

}
.custom_pagination a.pagination-link:hover {
  background: linear-gradient(90deg, #F5286E 0%, #FC6D43 100%);
  color: #FFF;

}
.custom_pagination a {
  color: #78818b;
  border-radius: 7px;

}
.custom_pagination a.pagination-link {
  box-shadow: 0 5px 15px rgb(0 0 0 / 10%);
  margin: 10px 2px 10px 2px;
  font-size: 12px;
  font-weight: 300;
}
.page-count a {
  border: none;
  margin: 10px 0 10px 0;
}
</style>
<table id="post-manager" class="stripe row-border order-column dataTable no-footer table table-striped table-bordered dt-responsive display nowrap">
<thead>
	<tr>
		<th width="4%">photo</th>
		<th>name</th>
		<th width="10%">rating</th>
		<th>message</th>
		<th>Created At</th>
		<th>Order</th>
		<th>Status</th>
		<th width="10%">Action</th>
	</tr>
</thead>
<tbody>	
    @foreach($testimonials as $testimonial)
    <tr> 
    	@if(!empty($testimonial->photo) && $testimonial->photo->file_path)
    	<th><img src="{{asset($testimonial->photo->file_path)  ?? ''}}" alt="Profile Image" width="50px" class="responsive"></th>
    	@else
         <th></th>
    	@endif
    	<th>{{$testimonial->name}}</th>
		<th>
		    @for($i=1;$i<=5;$i++)
		    <span class="fa fa-star {{ !empty($testimonial->rating) && $testimonial->rating >= $i ? 'checked' : '' }}"></span>
			
			@endfor
        </th>

		<th>{!! $testimonial->message ? getExpert($testimonial->message) : '' !!}</th>
		<th>{{dateFormat($testimonial->created_at)}}</th>
		<th contenteditable='true' data-id="{{$testimonial->id ?? ''}}" data-order="{{$testimonial->order ?? ''}}" class="editOrder" id="{{$testimonial->id ?? ''}}">{{$testimonial->order ?? ''}}</th>
		<th>{{$testimonial->status =='init' ? 'Inactive' :'Active'}}</th>
		<th>
		<div class="row">
			<div class="col-4 p-1">	
				<a href="{{ route("testimonials.show",["testimonial"=> $testimonial->id ]) }}" class="btn btn-sm btn-icon item-show"><i class="bx bxs-show"></i></a>
            </div>
			@if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Content Manager'))	
		    <div class="col-4 p-1">	
			<a href="{{ route("testimonials.edit",["testimonial"=> $testimonial->id ]) }}" class="btn btn-sm btn-icon item-edit"><i class="bx bxs-edit"></i></a>
            </div>
            <div class="col-4 p-1">
			<form action="{{ route('testimonials.destroy', $testimonial->id) }}" method="post">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-icon btn-danger delete" onclick="return confirm('Are you sure you want to delete?')"><i class="bx bxs-trash"></i></button>
            </form>
		   </div>
		   @endif
       </th>
       </div>
	</tr>
	@endforeach
	@if(count($testimonials) <=0)
	    <tr>
          <td colspan="14">No data available</td>
        </tr>
	@endif
</tbody>
</table>
<div class="text-xs-center">
	    @if ($testimonials->hasPages())
	        <div class="custom_pagination">
	            @if (!$testimonials->onFirstpage())
	                <a href="{{ $testimonials->appends(request()->input())->url(1) }}" class="pagination-link">
	                    <i class="bx bx-chevron-left"></i>
	                    <i class="bx bx-chevron-left"></i>
	                </a>
	                <a href="{{ $testimonials->appends(request()->input())->previousPageUrl() }}" class="pagination-link">
	                    <i class="bx bx-chevron-left"></i>
	                </a>
	            @endif

	            <span class="page-count"> <a href="#"> Page {{ number_format($testimonials->currentPage()) }} of
	                    {{ number_format($testimonials->lastPage()) }} </a></span>
	            @if (!$testimonials->onLastpage())
	                <a href="{{ $testimonials->appends(request()->input())->nextPageUrl() }}" class="pagination-link">
	                    <i class="bx bx-chevron-right"></i>

	                </a>
	                <a href="{{ $testimonials->appends(request()->input())->url($testimonials->lastPage()) }}" class="pagination-link">
	                    <i class="bx bx-chevron-right"></i>
	                    <i class="bx bx-chevron-right"></i>
	                </a>
	            @endif
	        </div>
	    @endif
	</div>