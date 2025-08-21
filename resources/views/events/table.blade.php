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
<table id="event-manager" class="stripe row-border order-column dataTable no-footer table table-striped table-bordered dt-responsive display nowrap">
<thead>
	<tr>
		<th>Image</th>
		<th>Title</th>
		<th>Tags</th>
		<th>Author</th>
		<th>Created At</th>
		<th width="12%">Action</th>
	</tr>
</thead>
<tbody>	
    @foreach($events as $event)
    <tr> 
    	@if(!empty($event->photo) && $event->photo->file_path)
    	<th><img src="{{asset($event->photo->file_path)  ?? ''}}" alt="Event Image" width="50px" class="responsive"></th>
    	@else
         <th></th>
    	@endif
    	<th>{{$event->title}}</th>
		<th>{{$event->tags}}</th>
		<th>{{$event->creator->full_name ?? ''}}</th>
		<th>{{dateFormat($event->created_at)}}</th>
		<th>
		<div class="row">

		  <div class="col-3 p-1">
			<a href="{{ route("events.show",["event"=> $event->id]) }}" class="btn btn-sm btn-icon btn-primary"><i class="bx bx-show"></i></a>
		  </div>

		  <div class="col-3 p-1">
			@if(Auth::user()->hasRole('Admin') )		
			  <a href="{{ route("events.edit",["event"=> $event->id ]) }}" class="btn btn-sm btn-icon item-edit"><i class="bx bxs-edit"></i></a>
      @endif
			</div>

      <div class="col-3 p-1">
        <a href="{{ route("calendar.index",["event_id"=> $event->id ]) }}" class="btn btn-sm btn-icon item-edit"><i class="bx bxs-calendar"></i></a>
      </div> 

      <div class="col-3 p-1">
			@if(Auth::user()->hasRole('Admin') )		
			<form action="{{ route('events.destroy', $event->id) }}" method="post">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-icon btn-danger deleteEmployer" onclick="return confirm('Are you sure you want to delete?')"><i class="bx bxs-trash"></i></button>
            </form>
			@endif
      </div>
      </div>    
      </th>
	</tr>
	@endforeach

	@if(count($events) <=0)
	    <tr>
          <td colspan="14">No data available</td>
        </tr>
	@endif
</tbody>
</table>
<div class="text-xs-center">
	    @if ($events->hasPages())
	        <div class="custom_pagination">
	            @if (!$events->onFirstpage())
	                <a href="{{ $events->appends(request()->input())->url(1) }}" class="pagination-link">
	                    <i class="bx bx-chevron-left"></i>
	                    <i class="bx bx-chevron-left"></i>
	                </a>
	                <a href="{{ $events->appends(request()->input())->previousPageUrl() }}" class="pagination-link">
	                    <i class="bx bx-chevron-left"></i>
	                </a>
	            @endif

	            <span class="page-count"> <a href="#"> Page {{ number_format($events->currentPage()) }} of
	                    {{ number_format($events->lastPage()) }} </a></span>
	            @if (!$events->onLastpage())
	                <a href="{{ $events->appends(request()->input())->nextPageUrl() }}" class="pagination-link">
	                    <i class="bx bx-chevron-right"></i>

	                </a>
	                <a href="{{ $events->appends(request()->input())->url($events->lastPage()) }}" class="pagination-link">
	                    <i class="bx bx-chevron-right"></i>
	                    <i class="bx bx-chevron-right"></i>
	                </a>
	            @endif
	        </div>
	    @endif
</div>