@if($booths->count())
  <div class="table-responsive">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Title</th>
          <th>Booth Number</th>
          <th>Size</th>
          {{-- <th>Company</th> --}}
          <th>Location Pref.</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($booths as $booth)
          <tr>
            <td>{{ $booth->title ?? '-' }}</td>
            <td>{{ $booth->booth_number }}</td>
            <td>{{ $booth->size }}</td>
            {{-- <td>{{ $booth->company->name ?? 'N/A' }}</td> --}}
            <td>{{ $booth->location_preferences }}</td>
            <td>{{ $booth->created_at->format('d M Y') }}</td>
            <td>
              <div class="d-flex gap-2">
                {{-- View --}}
                <a href="{{ route('booths.show', $booth->id) }}" class="btn btn-sm btn-icon btn-primary" title="View">
                  <i class="bx bx-show"></i>
                </a>

                {{-- Edit --}}
                <a href="{{ route('booths.edit', $booth->id) }}" class="btn btn-sm btn-icon item-edit" title="Edit">
                  <i class="bx bx-edit-alt"></i>
                </a>

                {{-- Delete --}}
                <form action="{{ route('booths.destroy', $booth->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this booth?');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger" type="submit" title="Delete">
                    <i class="bx bx-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="mt-3">
    {{ $booths->links() }}
  </div>
@else
  <p class="text-muted">No booths found.</p>
@endif
