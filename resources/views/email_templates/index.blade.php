@extends('layouts.admin')
@section('title')
    Admin | Email & Notifications Settings
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h2>Email Templates</h2>

    <div class="row">
    <div class="col-xl">
    <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
    <div class="card-body">
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('email-templates.create') }}" class="btn btn-primary me-2">Add Template</a>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Test
    </button>
</div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Template Name</th>
            <th>Subject</th>
            <th>Type</th>
            <th>Actions</th>
        </tr>
        @foreach ($templates as $template)
            <tr>
                <td>{{ $template->id }}</td>
                <td>{{ $template->template_name }}</td>
                <td>{{ $template->subject }}</td>
                <td>{{ $template->type }}</td>
                <td>
                    <a href="{{ route('email-templates.edit', $template->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('email-templates.destroy', $template->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>

    {{ $templates->links() }}
</div>
</div>
</div>
</div>
</div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form action="{{ route('send.email.template') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Send Email Template</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <!-- Select Template -->
          <div class="mb-3">
            <label for="mySelect" class="form-label">Select Template</label>
            <select id="mySelect" name="template" class="form-select" required>
              <option value="">-- Select --</option>
              @if(!empty(fetchEmailTemplates()))
                  @foreach(fetchEmailTemplates() as $emailtemplate)
                    <option value="{{ $emailtemplate->id }}">{{ $emailtemplate->template_name }}</option>
                  @endforeach
              @endif
            </select>
          </div>

          <!-- Email Input -->
          <div class="mt-3">
            <label for="email" class="form-label">Recipient Email</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="Enter email" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Send Email</button>
        </div>
      </form>

    </div>
  </div>
</div>


<script>
// Handle select change
document.getElementById('mySelect').addEventListener('change', function() {
  let value = this.value;
  let content = "";

  if(value === "1") {
    content = "<p>You selected <strong>Option 1</strong></p>";
  } else if(value === "2") {
    content = "<p>You selected <strong>Option 2</strong></p>";
  } else if(value === "3") {
    content = "<p>You selected <strong>Option 3</strong></p>";
  } else {
    content = "";
  }

  document.getElementById('contentArea').innerHTML = content;
});
</script>
@endsection
