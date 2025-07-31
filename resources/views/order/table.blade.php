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
			<th>Name</th>
			<th>Mobile</th>
			<th>Email</th>
			<th>Winning type</th>
			<th>Winning</th>
			<th>Amount</th>
			<th width="6%">Action</th>
		</tr>
	</thead>
	<tbody> 
        @foreach($order as $orderVal)
	    <tr> 
	    <th>{{$orderVal->user->name ?? ''}}</th>
	    <th>{{$orderVal->user->mobile ?? ''}}</th>
	    <th style="text-transform:none">{{$orderVal->user->email ?? ''}}</th>
	    <th>{{$orderVal->winning_type ?? ''}}</th>
	     <th>{{$orderVal->winning ?? ''}}</th>
	    <th>{{$orderVal->amount ? config('app.currency_sign').$orderVal->amount : ''}}</th>
	    	
			<th><a href="{{ route("orders.show",["order"=> $orderVal->id ]) }}" class="btn btn-sm btn-icon item-show"><i class="bx bxs-show"></i></a></th>
		</tr>
		@endforeach
		@if(count($order) <=0)
		    <tr>
              <td colspan="14">No data available</td>
            </tr>
		@endif
	</tbody>
</table>
	<div class="text-xs-center">
	    @if ($order->hasPages())
	        <div class="custom_pagination">
	            @if (!$order->onFirstpage())
	                <a href="{{ $order->appends(request()->input())->url(1) }}" class="pagination-link">
	                    <i class="bx bx-chevron-left"></i>
	                    <i class="bx bx-chevron-left"></i>
	                </a>
	                <a href="{{ $order->appends(request()->input())->previousPageUrl() }}" class="pagination-link">
	                    <i class="bx bx-chevron-left"></i>
	                </a>
	            @endif

	            <span class="page-count"> <a href="#"> Page {{ number_format($order->currentPage()) }} of
	                    {{ number_format($order->lastPage()) }} </a></span>
	            @if (!$order->onLastpage())
	                <a href="{{ $order->appends(request()->input())->nextPageUrl() }}" class="pagination-link">
	                    <i class="bx bx-chevron-right"></i>

	                </a>
	                <a href="{{ $order->appends(request()->input())->url($order->lastPage()) }}" class="pagination-link">
	                    <i class="bx bx-chevron-right"></i>
	                    <i class="bx bx-chevron-right"></i>
	                </a>
	            @endif
	        </div>
	    @endif
	</div>