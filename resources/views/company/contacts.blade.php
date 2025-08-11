@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">Company / Contact Management</h4>

  <div id="alert-area"></div>

  {{-- Add Contact Button --}}
  <div class="mb-3">
    <a href="{{ route('contacts.create') }}" class="btn btn-primary">Add New Contact</a>
  </div>

  {{-- Contacts Table --}}
  <div id="contactsTable">
    @include('company.partials.contacts-table', ['contacts' => $contacts])
  </div>
</div>
@endsection

@push('scripts')
<script>
  // AJAX Delete Contact
  $(document).on('click', '.delete-contact', function(e) {
    e.preventDefault();
    if (!confirm('Delete this contact?')) return;

    let id = $(this).data('id');

    $.ajax({
      url: `/company/contacts/${id}`,
      method: 'DELETE',
      data: { _token: '{{ csrf_token() }}' },
      success: function(res) {
        $('#contactsTable').html(res.html);
        showAlert('Contact deleted successfully.', 'success');
      },
      error: function() {
        showAlert('Failed to delete contact.', 'danger');
      }
    });
  });

  function showAlert(message, type) {
    $('#alert-area').html(`
      <div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    `);
  }
</script>
@endpush
