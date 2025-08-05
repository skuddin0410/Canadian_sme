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
{{-- <table class="table table-bordered"> --}}
    <thead>
        <tr>
            <th>Name</th><th>Industry</th><th>Size</th><th>Location</th><th>Email</th>
        <th>Phone</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($companies as $company)
            <tr>
                <td>{{ $company->name }}</td>
                <td>{{ $company->industry }}</td>
                <td>{{ $company->size }}</td>
                <td>{{ $company->location }}</td>
                <td>{{ $company->email }}</td>
                <td>{{ $company->phone }}</td>
                <td>
                    <a href="{{ route('company.show', $company->id) }}" class="btn btn-sm btn-info">View</a>
                    <a href="{{ route('company.edit', $company->id) }}" class="btn btn-sm btn-warning">Edit</a>
                </td>
                {{-- <td>
                    <a href="{{ route('company.edit', $company->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('company.destroy', $company->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td> --}}
            </tr>
        @empty
            <tr><td colspan="5">No data found.</td></tr>
        @endforelse
    </tbody>
</table>
