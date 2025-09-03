@extends('layouts.admin')

@section('title', 'Form Builder')

@section('content')
 <div class="container-xxl flex-grow-1 container-p-y pt-0">
        <!-- Header -->
        <header class="bg-white border-bottom header-shadow">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h1 class="h2 mb-0 text-dark fw-bold">Form Builder</h1>
                        <small class="text-muted">Manage all your forms</small>
                    </div>
                    <div class="d-flex gap-3">
                        <a href="{{ route('form-builder.index') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create New Form
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="container py-4">
            <!-- Stats Row -->
            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-file-alt text-primary fs-4"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0 text-muted">Total Forms</h6>
                                    <h3 class="mb-0" id="total-forms">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-check-circle text-success fs-4"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0 text-muted">Active Forms</h6>
                                    <h3 class="mb-0" id="active-forms">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-info bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-paper-plane text-info fs-4"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0 text-muted">Total Submissions</h6>
                                    <h3 class="mb-0" id="total-submissions">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-clock text-warning fs-4"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0 text-muted">This Month</h6>
                                    <h3 class="mb-0" id="month-submissions">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Forms Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Forms</h5>
                    <div class="d-flex gap-2">
                        <div class="input-group" style="width: 300px;">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" placeholder="Search forms..." id="search-forms">
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">Form Title</th>
                                    <th class="px-4 py-3">Description</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Fields</th>
                                    <th class="px-4 py-3">Submissions</th>
                                    <th class="px-4 py-3">Created</th>
                                    <th class="px-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="forms-table-body">
                                <!-- Forms will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Empty State -->
                    <div class="text-center py-5" id="empty-state" style="display: none;">
                        <i class="fas fa-file-alt text-muted display-1 mb-3"></i>
                        <h5 class="text-muted">No forms found</h5>
                        <p class="text-muted">Create your first form to get started</p>
                        <a href="{{ route('form-builder.index') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create New Form
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this form? This action cannot be undone.</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        All form submissions will also be deleted permanently.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete">
                        <i class="fas fa-trash me-2"></i>Delete Form
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let deleteFormId = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadForms();
            setupEventListeners();
        });

        function setupEventListeners() {
            // Search functionality
            document.getElementById('search-forms').addEventListener('input', function(e) {
                filterForms(e.target.value);
            });

            // Delete confirmation
            document.getElementById('confirm-delete').addEventListener('click', function() {
                if (deleteFormId) {
                    deleteForm(deleteFormId);
                }
            });
        }

        function loadForms() {
            fetch('/form-builder/forms')
                .then(response => response.json())
                .then(data => {
                    renderForms(data.forms);
                    updateStats(data.forms);
                })
                .catch(error => {
                    console.error('Error loading forms:', error);
                    showAlert('Error loading forms', 'danger');
                });
        }

        function renderForms(forms) {
            const tbody = document.getElementById('forms-table-body');
            const emptyState = document.getElementById('empty-state');

            if (forms.length === 0) {
                tbody.innerHTML = '';
                emptyState.style.display = 'block';
                return;
            }

            emptyState.style.display = 'none';
            tbody.innerHTML = forms.map(form => `
                <tr data-form-id="${form.id}">
                    <td class="px-4 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                <i class="fas fa-file-alt text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">${form.title}</h6>
                                <small class="text-muted">ID: ${form.id}</small>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-muted">${form.description || 'No description'}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge ${form.is_active ? 'bg-success' : 'bg-secondary'} status-badge">
                            ${form.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge bg-info">${form.form_data ? form.form_data.length : 0} fields</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge bg-primary">${form.submissions_count || 0}</span>
                    </td>
                    <td class="px-4 py-3">
                        <small class="text-muted">${formatDate(form.created_at)}</small>
                    </td>
                    <td class="px-4 py-3">
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-outline-primary" onclick="editForm(${form.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="/form-builder/forms/${form.id}" class="btn btn-sm btn-outline-success" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-secondary" onclick="duplicateForm(${form.id})" title="Duplicate">
                                <i class="fas fa-copy"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(${form.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function updateStats(forms) {
            document.getElementById('total-forms').textContent = forms.length;
            document.getElementById('active-forms').textContent = forms.filter(f => f.is_active).length;
            
            const totalSubmissions = forms.reduce((sum, form) => sum + (form.submissions_count || 0), 0);
            document.getElementById('total-submissions').textContent = totalSubmissions;
            
            // For demo purposes - in real app, you'd calculate this from actual submission dates
            document.getElementById('month-submissions').textContent = Math.floor(totalSubmissions * 0.3);
        }

        function filterForms(searchTerm) {
            const rows = document.querySelectorAll('#forms-table-body tr');
            rows.forEach(row => {
                const title = row.querySelector('h6').textContent.toLowerCase();
                const description = row.querySelector('.text-muted').textContent.toLowerCase();
                const matches = title.includes(searchTerm.toLowerCase()) || description.includes(searchTerm.toLowerCase());
                row.style.display = matches ? '' : 'none';
            });
        }

        function editForm(formId) {
            window.location.href = `/form-builder/edit/${formId}`;
        }

        function duplicateForm(formId) {
            if (confirm('Do you want to create a copy of this form?')) {
                fetch(`/form-builder/forms/${formId}/duplicate`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.form) {
                        showAlert('Form duplicated successfully!', 'success');
                        loadForms();
                    }
                })
                .catch(error => {
                    console.error('Error duplicating form:', error);
                    showAlert('Error duplicating form', 'danger');
                });
            }
        }

        function confirmDelete(formId) {
            deleteFormId = formId;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        function deleteForm(formId) {
            fetch(`/form-builder/forms/${formId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    showAlert('Form deleted successfully!', 'success');
                    loadForms();
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    modal.hide();
                }
            })
            .catch(error => {
                console.error('Error deleting form:', error);
                showAlert('Error deleting form', 'danger');
            });
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
        }

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    </script>
@endsection