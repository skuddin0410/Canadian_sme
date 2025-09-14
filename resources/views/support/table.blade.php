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
                        @if($support->status === 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($support->status === 'inprogress')
                            <span class="badge bg-info text-dark">In Progress</span>
                        @elseif($support->status === 'completed')
                            <span class="badge bg-success">Completed</span>
                        @else
                            <span class="badge bg-secondary">Unknown</span>
                        @endif
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
