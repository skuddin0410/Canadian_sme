@extends('layouts.admin')
@section('title')
    Admin | Email & Notifications Settings
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <h2>Edit Email Template</h2>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-body"> 
                    <form id="emailTemplateForm" 
                          action="{{ route('email-templates.update', $emailTemplate->id) }}" 
                          method="POST">
                        @csrf 
                        @method('PUT')

                        <div class="mb-3">
                            <label>Template Name</label>
                            <input type="text" name="template_name" 
                                   value="{{ $emailTemplate->template_name }}" 
                                   class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Subject</label>
                            <input type="text" name="subject" 
                                   value="{{ $emailTemplate->subject }}" 
                                   class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Type</label>
                            <select name="type" id="templateType" class="form-control" required>
                                <option value="email" {{ $emailTemplate->type == 'email' ? 'selected' : '' }}>Email</option>
                                <option value="notification" {{ $emailTemplate->type == 'notification' ? 'selected' : '' }}>Notification</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Message (supported tags: name, site_name, profile_update_link, qr_code)</label>

                            {{-- One dynamic textarea only --}}
                            <textarea id="messageBox" name="message" 
                                      class="form-control {{ $emailTemplate->type === 'email' ? 'description-cls' : '' }}" 
                                      rows="20" required>
                                {{ $emailTemplate->type === 'email' 
                                    ? strip_tags($emailTemplate->message)  {{-- show without HTML tags --}}
                                    : $emailTemplate->message }}
                            </textarea>
                        </div>

                        <button class="btn btn-success">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TinyMCE --}}
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('templateType');
    const messageBox = document.getElementById('messageBox');
    const form = document.getElementById('emailTemplateForm');

    function initEditor() {
        if (!tinymce.get("messageBox")) {
            tinymce.init({
                selector: '#messageBox.description-cls',
                height: 400,
                menubar: false,
                plugins: 'link lists code',
                toolbar: 'undo redo | bold italic underline | bullist numlist | link | code',
                setup: function (editor) {
                    editor.on('change keyup', function () {
                        tinymce.triggerSave();
                    });
                }
            });
        }
    }

    function destroyEditor() {
        if (tinymce.get("messageBox")) {
            tinymce.get("messageBox").remove();
        }
    }

    function toggleEditor() {
        if (typeSelect.value === 'email') {
            destroyEditor();
            messageBox.classList.add("description-cls");
            initEditor();
        } else {
            destroyEditor();
            messageBox.classList.remove("description-cls");
        }
    }

    // Validation before submit
    form.addEventListener("submit", function (e) {
        if (typeSelect.value === 'email') {
            const editor = tinymce.get("messageBox");
            const content = editor ? editor.getContent({ format: "text" }).trim() : '';
            if (!content) {
                e.preventDefault();
                alert("Message is required.");
                editor.focus();
            }
        } else {
            if (!messageBox.value.trim()) {
                e.preventDefault();
                alert("Message is required.");
                messageBox.focus();
            }
        }
    });

    typeSelect.addEventListener('change', toggleEditor);
    toggleEditor(); // run on page load
});
</script>
@endsection
