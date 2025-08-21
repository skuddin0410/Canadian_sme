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
		<th width="15%">Image</th>
		<th>name</th>
		{{-- <th>description</th> --}}
		<th>link</th>
		<th>order</th>
		<th width="8%">Action</th>
	</tr>
</thead>
<tbody>
    @foreach($banners as $banner)
    <tr> 
    	@if(!empty($banner->photo) && $banner->photo->file_path)
    	<th><img src="{{asset($banner->photo->file_path)  ?? ''}}" alt="Banner Image" width="50px" class="responsive"></th>
    	@else
         <th></th>
    	@endif
    	<th>{{$banner->name}}</th>
		{{-- <th>{{$banner->description ? getExpert($banner->description) :''}}</th> --}}
		<th style="text-transform:none">{{$banner->link}}</th>
		<th contenteditable='true' data-id="{{$banner->id ?? ''}}" data-order="{{$banner->order ?? ''}}" class="editOrder" id="{{$banner->id ?? ''}}">{{$banner->order ?? ''}}</th>
		<th>
		<div class="row">

		<div class="col-4 p-1">	
			<a href="{{ route("banners.show",["banner"=> $banner->id ]) }}" class="btn btn-sm btn-icon item-show"><i class="bx bxs-show"></i></a>
        </div>
    @if(Auth::user()->hasRole('Admin') )     	
		<div class="col-4 p-1">
			<a href="{{ route("banners.edit",["banner"=> $banner->id ]) }}" class="btn btn-sm btn-icon item-edit"><i class="bx bxs-edit"></i></a>
		</div>

      <div class="col-4 p-1">
			<form action="{{ route('banners.destroy', $banner->id) }}" method="post">
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
	@if(count($banners) <=0)
	    <tr>
          <td colspan="14">No data available</td>
        </tr>
	@endif
</tbody>
</table>

<div class="text-xs-center">
@if ($banners->hasPages())
<div class="custom_pagination">
@if (!$banners->onFirstpage())
<a href="{{ $banners->appends(request()->input())->url(1) }}" class="pagination-link">
    <i class="bx bx-chevron-left"></i>
    <i class="bx bx-chevron-left"></i>
</a>
<a href="{{ $banners->appends(request()->input())->previousPageUrl() }}" class="pagination-link">
    <i class="bx bx-chevron-left"></i>
</a>
@endif

<span class="page-count"> <a href="#"> Page {{ number_format($banners->currentPage()) }} of
    {{ number_format($banners->lastPage()) }} </a></span>
@if (!$banners->onLastpage())
<a href="{{ $banners->appends(request()->input())->nextPageUrl() }}" class="pagination-link">
    <i class="bx bx-chevron-right"></i>

</a>
<a href="{{ $banners->appends(request()->input())->url($banners->lastPage()) }}" class="pagination-link">
    <i class="bx bx-chevron-right"></i>
    <i class="bx bx-chevron-right"></i>
</a>
@endif
</div>
@endif
</div>
