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
			<th>Reference</th>
			<th>Amount</th>
			<th>status</th>
			<th width="6%">Action</th>
		</tr>
	</thead>
	<tbody> 
        @foreach($withdrawal as $withdrawalVal)
	    <tr> 
	    <th>{{$withdrawalVal->user->name ?? ''}}</th>
	    <th>{{$withdrawalVal->user->mobile ?? ''}}</th>
	    <th style="text-transform:none">{{$withdrawalVal->user->email ?? ''}}</th>
	    <th>{{$withdrawalVal->reference ?? ''}}</th>
	    <th>{{config('app.currency_sign')}}{{$withdrawalVal->amount ?? ''}}</th>
	    <th>{{$withdrawalVal->status ?? ''}}</th>
	    	
			<th><a href="{{ route("withdrawals-requests",["request_id"=> $withdrawalVal->id ]) }}" class="btn btn-sm btn-icon item-show"><i class="bx bxs-show"></i></a></th>
		</tr>
		@endforeach
		@if(count($withdrawal) <=0)
		    <tr>
              <td colspan="14">No data available</td>
            </tr>
		@endif
	</tbody>
</table>
	<div class="text-xs-center">
	    @if ($withdrawal->hasPages())
	        <div class="custom_pagination">
	            @if (!$withdrawal->onFirstpage())
	                <a href="{{ $withdrawal->appends(request()->input())->url(1) }}" class="pagination-link">
	                    <i class="bx bx-chevron-left"></i>
	                    <i class="bx bx-chevron-left"></i>
	                </a>
	                <a href="{{ $withdrawal->appends(request()->input())->previousPageUrl() }}" class="pagination-link">
	                    <i class="bx bx-chevron-left"></i>
	                </a>
	            @endif

	            <span class="page-count"> <a href="#"> Page {{ number_format($withdrawal->currentPage()) }} of
	                    {{ number_format($withdrawal->lastPage()) }} </a></span>
	            @if (!$withdrawal->onLastpage())
	                <a href="{{ $withdrawal->appends(request()->input())->nextPageUrl() }}" class="pagination-link">
	                    <i class="bx bx-chevron-right"></i>

	                </a>
	                <a href="{{ $withdrawal->appends(request()->input())->url($withdrawal->lastPage()) }}" class="pagination-link">
	                    <i class="bx bx-chevron-right"></i>
	                    <i class="bx bx-chevron-right"></i>
	                </a>
	            @endif
	        </div>
	    @endif
	</div>