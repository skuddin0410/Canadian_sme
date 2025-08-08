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
		<th>Code</th>
		<th>price</th>
		<th>type</th>
		<th>expires at</th>
		<th>Uses</th>
		<th width="8%">Action</th>
	</tr>
</thead>
<tbody>	
    @foreach($coupons as $coupon)
    <tr> 
    	<th style="text-transform:none">{{$coupon->name}}</th>
      @if($coupon->type == 'fixed')	
		  <th>{{config('app.currency_sign')}}{{$coupon->price }}</th>
		  @else
      <th>{{$coupon->price }}%</th>
		  @endif
		<th>{{$coupon->type}}</th>
		<th>{{dateFormat($coupon->expires_at)}}</th>
		<th>{{$coupon->spinners->count()}}</th>
		<th>
		<div class="row">
	    <div class="col-4 p-1">
			<a href="{{ route("coupons.show",["coupon"=> $coupon->id ]) }}" class="btn btn-sm btn-icon item-show"><i class="bx bxs-show"></i></a>
		</div>
	    @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Admin'))			
		<div class="col-4 p-1">
			<a href="{{ route("coupons.edit",["coupon"=> $coupon->id ]) }}" class="btn btn-sm btn-icon item-edit"><i class="bx bxs-edit"></i></a>
		</div>
		<div class="col-4 p-1">
		   <form action="{{ route('coupons.destroy', ["coupon"=> $coupon->id ]) }}" method="post">
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
	@if(count($coupons) <=0)
	    <tr>
          <td colspan="14">No data available</td>
        </tr>
	@endif
</tbody>
</table>
	<div class="text-xs-center">
	    @if ($coupons->hasPages())
	        <div class="custom_pagination">
	            @if (!$coupons->onFirstpage())
	                <a href="{{ $coupons->appends(request()->input())->url(1) }}" class="pagination-link">
	                    <i class="bx bx-chevron-left"></i>
	                    <i class="bx bx-chevron-left"></i>
	                </a>
	                <a href="{{ $coupons->appends(request()->input())->previousPageUrl() }}" class="pagination-link">
	                    <i class="bx bx-chevron-left"></i>
	                </a>
	            @endif

	            <span class="page-count"> <a href="#"> Page {{ number_format($coupons->currentPage()) }} of
	                    {{ number_format($coupons->lastPage()) }} </a></span>
	            @if (!$coupons->onLastpage())
	                <a href="{{ $coupons->appends(request()->input())->nextPageUrl() }}" class="pagination-link">
	                    <i class="bx bx-chevron-right"></i>

	                </a>
	                <a href="{{ $coupons->appends(request()->input())->url($coupons->lastPage()) }}" class="pagination-link">
	                    <i class="bx bx-chevron-right"></i>
	                    <i class="bx bx-chevron-right"></i>
	                </a>
	            @endif
	        </div>
	    @endif
	</div>