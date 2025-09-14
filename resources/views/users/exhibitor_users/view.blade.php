@php use Illuminate\Support\Str; @endphp

@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="row">
        <!-- Left section: profile/details -->
        <div class="col-md-8">
            <div class="card mb-4">
                
                <div class="card-header">
                    <h4>Exhibitor Details</h4>
                </div>
                
                
                <div class="card-body">
                    <!-- Company Profile Info -->
                    <p><strong>Name:</strong> {{ $company->name }}</p>
                    <p><strong>Email:</strong> {{ $company->email }}</p>
                    <p><strong>Phone:</strong> {{ $company->phone }}</p>
                    <p><strong>Description:</strong> {{ $company->description }}</p>
                    <p><strong>Website:</strong> <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a></p>
                    <p><strong>LinkedIn:</strong> <a href="{{ $company->linkedin }}" target="_blank">{{ $company->linkedin }}</a></p>
                    <p><strong>Twitter:</strong> <a href="{{ $company->twitter }}" target="_blank">{{ $company->twitter }}</a></p>
                    <p><strong>Facebook:</strong> <a href="{{ $company->facebook }}" target="_blank">{{ $company->facebook }}</a></p>
                    <p><strong>Instagram:</strong> <a href="{{ $company->instagram }}" target="_blank">{{ $company->instagram }}</a></p>
                    <h4 class="ms-2">
                       Booth No :
                      <span class="badge bg-primary rounded-pill">
                        {{ $company->booth ?? 'N/A' }}
                      </span>
                    </h4>
                    
                </div>
                
            </div>
            <!-- New Card: Media & QR -->
<div class="card mt-4 col-12">
    <div class="card-header">
        <h4>Media & QR Code</h4>
    </div>
    <div class="card-body text-center">
        <div class="row">
            <!-- Content Icon -->
            <div class="col-md-6">
                <h6>Logo</h6>
                @if(!empty($company->contentIconFile) && !empty($company->contentIconFile->file_path))
                    <img src="{{$company->contentIconFile->file_path }}"
                         alt="Content Icon"
                         class="img-fluid rounded shadow-sm"
                         style="max-height: 150px; object-fit: contain;">
                @else
                    <p class="text-muted">No Content Icon</p>
                @endif
            </div>

            <!-- Quick Link Icon -->
            <div class="col-md-6">

                <h6>Banner</h6>
                @if(!empty($company->quickLinkIconFile) && !empty($company->quickLinkIconFile->file_path))
                    <img src="{{$company->quickLinkIconFile->file_path}}"
                         alt="Quick Link Icon"
                         class="img-fluid rounded shadow-sm"
                         style="max-height: 150px; object-fit: contain;">
                @else
                    <p class="text-muted">No Quick Link Icon</p>
                @endif
            </div>
        </div>
    </div>
</div>

        </div>

        <!-- Right section: Booth assignment -->
 <div class="col-md-4">
    <div class="card">
        <div class="d-flex pt-3 justify-content-end">
            <a href="{{ route('exhibitor-users.index') }}" class="btn btn-outline-primary me-2">Back</a>
        </div>
       <div class="card mt-4">
    <div class="card-header">
        <h4>Upload Private Docs</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('exhibitor.uploadDocs', $company->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div id="docsDropZone"
                 class="position-relative rounded-3 p-4 text-center d-flex align-items-center justify-content-center overflow-hidden"
                 style="border: 2px dashed var(--bs-border-color); cursor: pointer; background: var(--bs-body-bg); min-height: 180px;">

                {{-- Placeholder --}}
                <div id="dz-placeholder" class="d-flex flex-column align-items-center gap-2">
                    <i class="bx bx-cloud-upload" style="font-size: 2rem;"></i>
                    <div>
                        <strong>Drag & drop</strong> files here, or
                        <button type="button" id="dz-browse" class="btn btn-sm btn-outline-primary ms-1">Browse</button>
                    </div>
                    <small class="text-muted d-block">PNG, JPG, PDF, DOC, DOCX (Max 2MB)</small>
                </div>

                {{-- Hidden input --}}
                <input type="file" id="privateDocsInput" name="private_docs[]" accept=".png,.jpg,.jpeg,.pdf,.doc,.docx" class="d-none" multiple>
            </div>

            {{-- Previews --}}
            <div id="docsPreview" class="row g-3 mt-3"></div>

            <button type="submit" class="btn btn-primary mt-3">Upload Files</button>
        </form>
    </div>
</div>

{{-- Uploaded documents --}}
<div class="card mt-4">
    <div class="card-header">Uploaded Documents</div>
    <div class="card-body">
        @if($company->Docs && $company->Docs->count() > 0)
            <div class="row g-3">
               @foreach($company->Docs as $doc)
    <div class="col-md-4 text-center">
        <div class="card p-2 shadow-sm position-relative doc-card">

            {{-- Delete Button (hidden by default, visible on hover) --}}
            <form action="{{ route('exhibitor.deleteDoc', $doc->id) }}" method="POST" 
                  class="position-absolute top-0 end-0 m-1 delete-form"
                  onsubmit="return confirm('Are you sure you want to delete this document?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger rounded-circle delete-btn">
                    &times;
                </button>
            </form>

            {{-- File Preview --}}
            @if(Str::endsWith($doc->file_path, ['.png', '.jpg', '.jpeg']))
                <img src="{{ asset($doc->file_path) }}"
                     alt="Document"
                     class="img-fluid rounded mb-2"
                     style="max-height: 200px; object-fit: contain;">
            @elseif(Str::endsWith($doc->file_path, '.pdf'))
                <a href="{{ asset($doc->file_path) }}" target="_blank">
                    <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 4rem;"></i>
                </a>
            @elseif(Str::endsWith($doc->file_path, ['.doc', '.docx']))
                <a href="{{ asset($doc->file_path) }}" target="_blank">
                    <i class="bi bi-file-earmark-word text-primary" style="font-size: 4rem;"></i>
                </a>
            @else
                <a href="{{ asset($doc->file_path) }}" target="_blank">
                    <i class="bi bi-file-earmark-text" style="font-size: 4rem;"></i>
                </a>
            @endif

            <p class="mt-2 text-truncate">{{ $doc->file_name }}</p>
        </div>
    </div>
@endforeach

            </div>
        @else
            <p class="text-muted">No documents uploaded yet.</p>
        @endif
    </div>
</div>

    </div>
             
 
    </div>
</div>
<style>
    .delete-btn {
        display: none;
        width: 28px;
        height: 28px;
        padding: 0;
        line-height: 1;
        font-size: 18px;
        font-weight: bold;
    }

    /* Fix: target .doc-card instead of .gallery-card */
    .doc-card:hover .delete-btn {
        display: inline-block;
    }
</style>

@endsection


@section('scripts')
<script>
    const input = document.getElementById('privateDocsInput');
    const browseBtn = document.getElementById('dz-browse');
    const dropZone = document.getElementById('docsDropZone');
    const preview = document.getElementById('docsPreview');
    const placeholder = document.getElementById('dz-placeholder');

    // Browse button click
    browseBtn.addEventListener('click', () => input.click());

    // File selection
    input.addEventListener('change', updatePreview);

    // Drag & drop support
    dropZone.addEventListener('dragover', e => {
        e.preventDefault();
        dropZone.classList.add('border-primary');
    });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('border-primary'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('border-primary');
        input.files = e.dataTransfer.files;
        updatePreview();
    });

    function updatePreview() {
        preview.innerHTML = '';
        placeholder.classList.toggle('d-none', input.files.length > 0);

        Array.from(input.files).forEach(file => {
            const col = document.createElement('div');
            col.classList.add('col-12', 'col-md-6');

            const card = document.createElement('div');
            card.classList.add('card', 'p-2', 'text-center');

            const filename = document.createElement('p');
            filename.textContent = file.name;

            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.style.maxHeight = '150px';
                img.style.objectFit = 'contain';
                img.classList.add('img-fluid', 'mb-2');
                card.appendChild(img);
            } else {
                const icon = document.createElement('i');
                icon.classList.add('bi', 'bi-file-earmark-text');
                icon.style.fontSize = '3rem';
                card.appendChild(icon);
            }

            card.appendChild(filename);
            col.appendChild(card);
            preview.appendChild(col);
        });
    }
</script>

@endsection
