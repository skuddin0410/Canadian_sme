@if($trainings->count())
  <div class="table-responsive">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Name</th>
          <th>Description</th>
          <th>YouTube</th>
          <th>File</th>
          <th>Uploaded</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($trainings as $material)
          <tr>
            <td>{{ $material->material_name }}</td>
            <td>{{ Str::limit($material->material_description, 60) }}</td>
            <td>
              @if($material->youtube_link)
                <a href="{{ $material->youtube_link }}" target="_blank">View</a>
              @else
                —
              @endif
            </td>
            <td>
              @php
                $file = \App\Models\Drive::where([
                  'table_id' => $material->id,
                  'table_type' => 'trainings',
                  'file_type' => 'training_material',
                ])->first();
              @endphp
              @if($file)
                <a href="{{ asset('storage/' . $file->file_name) }}" target="_blank">Download</a>
              @else
                
              @endif
            </td>
            <td>{{ $material->created_at->format('d M Y') }}</td>
            <td>
              <div class="d-flex gap-2">
                <a href="{{ route('trainings.show', $material->id) }}" class="btn btn-sm btn-icon btn-primary" title="View"><i class="bx bx-show"></i></a>
                <a href="{{ route('trainings.edit', $material->id) }}" class="btn btn-sm btn-icon item-edit" title="Edit"><i class="bx bxs-edit"></i></a>
                <form action="{{ route('trainings.destroy', $material->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this material?');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger" type="submit" title="Delete"><i class="fas fa-trash-alt"></i></button>
                </form>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-3">
    {!! $trainings->links() !!}
  </div>
@else
  <p class="text-muted">No materials uploaded yet.</p>
@endif
