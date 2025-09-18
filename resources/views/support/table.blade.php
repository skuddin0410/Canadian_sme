@if($supports->count() > 0)
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Subject</th>
                <th>Description</th>
                <th>Status</th>
                <th>Added By</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($supports as $support)
                <tr>
                   
                    <td>{{ $loop->iteration + ($supports->currentPage() - 1) * $supports->perPage() }}</td>

                    <td>{{ $support->subject }}</td>

                  
                    <td>{{ \Illuminate\Support\Str::limit($support->description, 80) }}</td>

                
                    <td>
                        <form action="{{ route('support.updateStatus', $support->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                           <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                           <option value="pending" {{ $support->status === 'pending' ? 'selected' : '' }}>Pending</option>
                           <option value="inprogress" {{ $support->status === 'inprogress' ? 'selected' : '' }}>In Progress</option>
                           <option value="completed" {{ $support->status === 'completed' ? 'selected' : '' }}>Completed</option>
                           </select>
                        </form>
                    </td>


                   
                    <td>{{ $support->user?->name ?? 'N/A' }}</td>

                    <td>{{ $support->created_at->format('d M, Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    
    <div class="d-flex justify-content-center">
        {!! $supports->links() !!}
    </div>
@else
    <p class="text-center text-muted">No support tickets found.</p>
@endif
