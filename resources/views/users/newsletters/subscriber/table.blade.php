<table class="table table-bordered table-striped">
    <thead>
        <tr>
            {{-- <th></th> --}}
            <th>Email</th>
            <th>Name</th>
            <th>Status</th>
            <th>Subscribed At</th>
            <th>Unsubscribed At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($subscribersPaginator as $subscriber)
            <tr>
                {{-- <td>{{ $i + $loop->index + 1 }}</td> --}}
                <td>{{ $subscriber->email }}</td>
                <td>{{ $subscriber->name ?? '-' }}</td>
                <td>
                    @if($subscriber->status === 'subscribed')
                        <span class="badge bg-success">Subscribed</span>
                    @else
                        <span class="badge bg-danger">Unsubscribed</span>
                    @endif
                </td>
                <td>{{ $subscriber->subscribed_at?->format('Y-m-d H:i') ?? '-' }}</td>
                <td>{{ $subscriber->unsubscribed_at?->format('Y-m-d H:i') ?? '-' }}</td>
               <td>
    <div class="d-flex gap-2">
        {{-- View --}}
        <a href="{{ route('newsletter-subscribers.show', $subscriber->id) }}" class="btn btn-sm btn-icon btn-primary" title="View">
            <i class="bx bx-show"></i>
        </a>

        {{-- Edit --}}
        <a href="{{ route('newsletter-subscribers.edit', $subscriber->id) }}" class="btn btn-sm btn-icon btn-primary" title="Edit">
            <i class="bx bx-edit-alt"></i>
        </a>

        {{-- Delete --}}
        <form action="{{ route('newsletter-subscribers.destroy', $subscriber->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this subscriber?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger btn-icon" title="Delete">
                <i class="bx bx-trash"></i>
            </button>
        </form>
    </div>
</td>

            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">No subscribers found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Pagination links -->
<div class="d-flex justify-content-end">
    {!! $subscribersPaginator->links() !!}
</div>
