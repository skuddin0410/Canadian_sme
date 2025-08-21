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
			<th width="40%">Question</th>
			<th width="45%">Answer</th>
			<th width="5%">Order</th>
			<th width="10%">Action</th>
		</tr>
	</thead>
	<tbody>	
        @foreach($faqs as $faq)
	    <tr> 
	    	<th>{{$faq->name}}</th>
			<th>{!! getExpert($faq->description) ?? ''!!}</th>
			<th contenteditable='true' data-id="{{$faq->id ?? ''}}" data-order="{{$faq->order ?? ''}}" class="editOrder" id="{{$faq->id ?? ''}}">{{$faq->order ?? ''}}</th>
			
			<th>
			<div class="row">
				<div class="col-4 p-1">	
					<a href="{{ route("faqs.show",["faq"=> $faq->id ]) }}" class="btn btn-sm btn-icon item-show"><i class="bx bxs-show"></i></a>
			    </div>
				@if(Auth::user()->hasRole('Admin') )
			    <div class="col-4 p-1">	
					<a href="{{ route("faqs.edit",["faq"=> $faq->id ]) }}" class="btn btn-sm btn-icon item-edit"><i class="bx bxs-edit"></i></a>
				</div>	
			    <div class="col-4 p-1">
				<form action="{{ route('faqs.destroy', $faq->id) }}" method="post">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-icon btn-danger delete" onclick="return confirm('Are you sure you want to delete?')"><i class="bx bxs-trash"></i></button>
                </form>
			   </div>
			   @endif

            </div>	
           </th>
		</tr>
		@endforeach
		@if(count($faqs) <=0)
		    <tr>
              <td colspan="14">No data available</td>
            </tr>
		@endif
	</tbody>
</table>
<div class="text-xs-center">
	    @if ($faqs->hasPages())
	        <div class="custom_pagination">
	            @if (!$faqs->onFirstpage())
	                <a href="{{ $faqs->appends(request()->input())->url(1) }}" class="pagination-link">
	                    <i class="bx bx-chevron-left"></i>
	                    <i class="bx bx-chevron-left"></i>
	                </a>
	                <a href="{{ $faqs->appends(request()->input())->previousPageUrl() }}" class="pagination-link">
	                    <i class="bx bx-chevron-left"></i>
	                </a>
	            @endif

	            <span class="page-count"> <a href="#"> Page {{ number_format($faqs->currentPage()) }} of
	                    {{ number_format($faqs->lastPage()) }} </a></span>
	            @if (!$faqs->onLastpage())
	                <a href="{{ $faqs->appends(request()->input())->nextPageUrl() }}" class="pagination-link">
	                    <i class="bx bx-chevron-right"></i>

	                </a>
	                <a href="{{ $faqs->appends(request()->input())->url($faqs->lastPage()) }}" class="pagination-link">
	                    <i class="bx bx-chevron-right"></i>
	                    <i class="bx bx-chevron-right"></i>
	                </a>
	            @endif
	        </div>
	    @endif
</div>