@if($supports->count() > 0)
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Subject</th>
                <th>Description</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($supports as $index => $support)
                <tr>
                    <td>{{ $loop->iteration + ($supports->currentPage() - 1) * $supports->perPage() }}</td>
                    <td>{{ $support->subject }}</td>
                    <td>{{ Str::limit($support->description, 80) }}</td>
                    <td>{{ $support->created_at->format('d M, Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center">
        {!! $supports->links() !!}
    </div>
@else
    <p class="text-center text-muted">No support tickets found.</p>
@endif
