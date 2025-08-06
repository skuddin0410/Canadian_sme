<table class="table table-bordered">
  <thead>
    <tr>
      <th>Name</th>
      <th>Email</th>
      <th>Phone</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    @forelse($contacts as $contact)
      <tr>
        <td>{{ $contact->name }}</td>
        <td>{{ $contact->email }}</td>
        <td>{{ $contact->phone }}</td>
        <td>
          <button class="btn btn-sm btn-danger delete-contact" data-id="{{ $contact->id }}">Delete</button>
        </td>
      </tr>
    @empty
      <tr><td colspan="4">No contacts found.</td></tr>
    @endforelse
  </tbody>
</table>
