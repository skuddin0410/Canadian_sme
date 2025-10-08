@extends('layouts.admin')

@section('title', 'Admin | Update Edit')

@section('content')
<style>
  .preview-wrap .hover-reveal {
    opacity: 0;
    pointer-events: none;
    transition: opacity .15s ease-in-out;
    z-index: 2;
  }
  .preview-wrap:hover .hover-reveal {
    opacity: 1;
    pointer-events: auto;
  }
</style>

<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <div class="d-flex align-items-center justify-content-between mb-3 mt-3">
    <h4 class="mb-0"><span class="text-muted fw-light">Update /</span> Event</h4>
    <div>
      <a href="{{ route('events.index') }}" class="btn btn-primary btn-sm">
        <i class="bx bx-left-arrow-alt me-1"></i> Back to list
      </a>
    </div>
  </div>

  @php $e = $event ?? null; @endphp

  <form action="{{ route('events.update', ['event' => $event->id]) }}" method="POST" enctype="multipart/form-data" novalidate>
    @csrf
    @if(!empty($event))
      @method('PUT')
    @endif

    <div class="row g-4">
      {{-- LEFT: Main details --}}
      <div class="col-12 col-lg-8">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">General > General infromation about your app and event</h5>
            
          </div>
          <div class="card-body">
            {{-- Title + Slug --}}
            <div class="row">
                @php
                    $exts = array_map('trim', explode(',', config('app.image_mime_types')));
                    $acceptList = implode(',', array_map(fn($e) => (stripos($e, 'image/') === 0 ? $e : 'image/'.$e), $exts));
                  @endphp

                  <div class="mb-2">
                    <label class="form-label">
                      Image 
                      <span class="text-danger">
                        (Allowed: {{ (int) config('app.blog_image_size') }} KB; Types: {{ config('app.image_mime_types') }}) (<span class="text-danger">1920px (width) x 800px (height)</span>)
                      </span>
                    </label>

                    <div class="row g-3 align-items-start">
                      <!-- LEFT: Dropzone -->
                      <div class="col-12 col-md-12">
                        <!-- Real input (hidden) -->
                        <input
                          type="file"
                          class="form-control d-none @error('image') is-invalid @enderror"
                          name="image"
                          id="image"
                          accept="{{ $acceptList ?: 'image/*' }}"
                          data-max-size-kb="{{ (int) config('app.blog_image_size') }}"
                        />


                            @php
                              $hasImage = !empty($event->photo) && !empty($event->photo->file_path);
                              $imgSrc = $hasImage ? (Str::startsWith($event->photo->file_path, ['http://','https://'])
                                          ? $event->photo->file_path
                                          : Storage::url($event->photo->file_path)) : '';
                            @endphp

                           <div id="image-dropzone"
                               class="position-relative rounded-3 p-4 text-center d-flex align-items-center justify-content-center overflow-hidden"
                               style="border: 2px dashed var(--bs-border-color); cursor: pointer; background: var(--bs-body-bg); min-height: 180px;">

                            {{-- Placeholder --}}
                            <div id="dz-placeholder" class="d-flex flex-column align-items-center gap-2 {{ $hasImage ? 'd-none' : '' }}">
                              <i class="bx bx-cloud-upload" style="font-size: 2rem;"></i>
                              <div>
                                <strong>Drag & drop</strong> an image here, or
                                <button type="button" id="dz-browse" class="btn btn-sm btn-outline-primary ms-1">Browse</button>
                              </div>
                              <small class="text-muted d-block">Max {{ (int) config('app.blog_image_size') }} KB</small>
                            </div>

                            {{-- Inline preview --}}
                            <img id="dz-image"
                                 src="{{ $imgSrc }}"
                                 alt="Preview"
                                 class="{{ $hasImage ? '' : 'd-none' }} rounded"
                                 style="max-height: 180px; max-width: 100%; object-fit: contain;" />

                            {{-- Remove button --}}
                           
                            <button type="button"
                                    id="dz-remove"
                                    class="btn btn-sm btn-danger position-absolute {{ $hasImage ? '' : 'd-none' }}"
                                    style="top: .5rem; right: .5rem;" data-photoid=" {{!empty($event->photo) ? $event->photo->id : ''}}">
                              <i class="bx bx-x"></i> Remove
                            </button>

                            <input type="file" id="dz-input" name="image" accept="image/*" class="d-none">
                          </div>


                        @error('image')
                          <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                  </div>

              <div class="col-md-7">
                <div class="mb-3">
                  <label class="form-label" for="title">Event Name <span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-book"></i></span>
                    <input
                      type="text"
                      class="form-control @error('title') is-invalid @enderror"
                      name="title"
                      id="title"
                      value="{{ old('title', $e->title ?? '') }}"
                      placeholder="Event Name" />
                  </div>
                  @error('title')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-5">
                <div class="mb-3">
                  <label class="form-label" for="slug">Slug <span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-link-alt"></i></span>
                    <input
                      type="text"
                      class="form-control @error('slug') is-invalid @enderror"
                      name="slug"
                      id="slug"
                      value="{{ old('slug', $e->slug ?? '') }}"
                      placeholder="auto-from-title or edit" />
                  </div>
                  @error('slug')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            {{-- Location --}}
            <div class="mb-3">
              <label class="form-label" for="location">Venue <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-map"></i></span>
                <input
                  type="text"
                  class="form-control @error('location') is-invalid @enderror"
                  name="location"
                  id="location"
                  value="{{ old('location', $event->location ?? '') }}"
                  placeholder="Event Venue" />
              </div>
              @error('location')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            {{-- YouTube link --}}
            <div class="mb-3">
              <label class="form-label" for="youtube_link">YouTube Link</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bxl-youtube"></i></span>
                <input
                  type="text"
                  class="form-control @error('youtube_link') is-invalid @enderror"
                  name="youtube_link"
                  id="youtube_link"
                  value="{{ old('youtube_link', $event->youtube_link ?? '') }}"
                  placeholder="https://www.youtube.com/watch?v=xxxx" />
              </div>
              @error('youtube_link')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
 
            <input type="hidden" name="tags" id="tags-hidden" value="{{ old('tags', $e->tags ?? '') }}">

              <div class="mb-2">
              <label class="form-label d-flex align-items-center justify-content-between">
                <span>Tags</span>
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addTagModal">
                  <i class="bx bx-plus"></i> Add Tag
                </button>
              </label>

              <div id="tags-list" class="d-flex flex-wrap gap-2">
                @php

                  $availableTags = $availableTags ?? ['Music', 'Meetup', 'Conference', 'Workshop', '2025'];
                  $selectedTags = collect(explode(',', old('tags', $e->tags ?? '')))
                                    ->map(fn($t) => trim($t))
                                    ->filter();
                @endphp

                @foreach($availableTags as $tag)
                  @php $id = 'tag-'.\Illuminate\Support\Str::slug($tag); @endphp
                  <input type="checkbox"
                         class="btn-check"
                         id="{{ $id }}"
                         name="tags[]"
                         value="{{ $tag }}"
                         @checked($selectedTags->contains($tag))>

                  <label class="btn btn-outline-primary rounded-pill px-3 py-1" for="{{ $id }}">
                    <i class="bx bx-purchase-tag me-1"></i>{{ $tag }}
                  </label>
                @endforeach
              </div>

              {{-- If you store as comma string instead of array, uncomment below & include sync script further down --}}
              {{-- <input type="hidden" name="tags" id="tags-hidden" value="{{ old('tags', $e->tags ?? '') }}"> --}}

              @error('tags')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3">
              <label class="form-label" for="description">Description <span class="text-danger">*</span></label>
              <textarea
              name="description"
              id="description1"
              class="form-control"
              rows="8"
              style="min-height: 250px;">{{ old('description', $e->description ?? '') }}</textarea>
                @error('description')
                <div class="text-danger mt-2">{{ $message }}</div>
               @enderror
            </div>

                
          </div>
        </div>
      </div>


      <div class="col-12 col-lg-4">
        <div class="card position-sticky" style="top: 1rem;">
          <div class="card-header">
            <h6 class="mb-0">Event Settings</h6>
          </div>
          <div class="card-body">

            {{-- Start / End dates --}}
            <div class="mb-3">
              <label class="form-label" for="start_date">Start Date <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                <input
                  type="date"
                  class="form-control @error('start_date') is-invalid @enderror"
                  name="start_date"
                  id="start_date"
                  value="{{ old('start_date', isset($e) ? $e->start_date->format('Y-m-d') : '') }}" />
              </div>
              @error('start_date')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label class="form-label" for="end_date">End Date <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-calendar-check"></i></span>
                <input
                  type="date"
                  class="form-control @error('end_date') is-invalid @enderror"
                  name="end_date"
                  id="end_date"
                  value="{{ old('end_date', isset($e) ? $e->end_date->format('Y-m-d') : '') }}" />
              </div>
              @error('end_date')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            {{-- Status / Visibility --}}
            <div class="row">
              <div class="col-12">
                <div class="mb-3">
                  <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-check-shield"></i></span>
                    <select class="form-select @error('status') is-invalid @enderror" name="status" id="status">
                      @foreach(['draft', 'published', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $e->status ?? '') === $status)>{{ ucfirst($status) }}</option>
                      @endforeach
                    </select>
                  </div>
                  @error('status')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="col-12">
                <div class="mb-3">
                  <label class="form-label" for="visibility">Listing Privacy <span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-hide"></i></span>
                    <select class="form-select @error('visibility') is-invalid @enderror" name="visibility" id="visibility">
                      @foreach(['listed', 'unlisted'] as $visibility)
                        <option value="{{ $visibility }}" @selected(old('visibility', $e->visibility ?? '') === $visibility)>{{ ucfirst($visibility) }}</option>
                      @endforeach
                    </select>
                  </div>
                  @error('visibility')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <hr class="my-3">

            <div class="d-grid gap-2">
              <a href="{{ route('events.index') }}" class="btn btn-outline-primary">
                Cancel
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="bx bx-save me-1"></i> Save Changes
              </button>
            </div>

          </div>
        </div>
      </div>
    </div>

  </form>
</div>


<div class="modal fade" id="addTagModal" tabindex="-1" aria-labelledby="addTagModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="addTagForm" action="{{route('categories.store-tags')}}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="addTagModalLabel">Add New Tag</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <input type="hidden" class="form-control" id="type" name="type" value="tags">
      <div class="modal-body">
        <div class="mb-3">
          <label for="tag-name" class="form-label">Tag Name <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="tag-name" name="name" placeholder="e.g. Webinar" required>
          <div class="invalid-feedback" id="tag-name-error"></div>
        </div>

        <div class="mb-0">
          <label for="tag-slug" class="form-label">Slug</label>
          <input type="text" class="form-control" id="tag-slug" name="slug" placeholder="auto-generated">
          <div class="form-text">Leave blank to auto-generate from name.</div>
          <div class="invalid-feedback" id="tag-slug-error"></div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary" id="addTagSubmitBtn">
          <span class="save-text"><i class="bx bx-save me-1"></i> Save</span>
          <span class="saving-text d-none">Saving…</span>
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
  // --- Slug auto-generate from Title ---
  const titleEl = document.getElementById('title');
  const slugEl  = document.getElementById('slug');

  function slugify(str) {
    return String(str)
      .trim()
      .toLowerCase()
      .replace(/[^a-z0-9 -]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-+|-+$/g, '');
  }

  if (titleEl && slugEl) {
    const syncSlug = () => { if (!slugEl.dataset.touched) slugEl.value = slugify(titleEl.value); };
    titleEl.addEventListener('keyup', syncSlug);
    titleEl.addEventListener('blur', syncSlug);
    slugEl.addEventListener('input', () => slugEl.dataset.touched = '1');
  }

  // --- Quill setup for Description ---
  document.addEventListener('DOMContentLoaded', function() {
    const descInput = document.getElementById('description');
    const quillContainer = document.getElementById('quill-editor');
    if (quillContainer && descInput) {
      const editor = new Quill('#quill-editor', {
        theme: 'snow',
        modules: {
          toolbar: [
            [{ header: [1, 2, 3, false] }],
            ['bold', 'italic', 'underline'],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            ['link', 'blockquote', 'code-block', 'clean']
          ]
        }
      });
      // Set initial HTML
      editor.root.innerHTML = descInput.value || '';
      // Keep textarea in sync
      editor.on('text-change', function() {
        const html = editor.root.innerHTML;
        descInput.value = (html === '<p><br></p>') ? '' : html;
      });
    }
  });

  // --- Lightweight tags chips preview ---
  const tagsInput = document.getElementById('tags');
  const tagsPreview = document.getElementById('tags-preview');

  function renderTagsPreview(value) {
    if (!tagsPreview) return;
    const parts = (value || '')
      .split(',')
      .map(t => t.trim())
      .filter(Boolean);

    tagsPreview.innerHTML = parts.map(tag =>
      `<span class="badge bg-label-primary border me-1 mb-1">${tag}</span>`
    ).join('');
  }

  if (tagsInput) {
    renderTagsPreview(tagsInput.value);
    tagsInput.addEventListener('input', (e) => renderTagsPreview(e.target.value));
    tagsInput.addEventListener('blur', (e) => renderTagsPreview(e.target.value));
  }

</script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const wrap = document.querySelector('.preview-wrap');
    if (!wrap) return;
    const reveal = wrap.querySelector('.hover-reveal');
    let toggled = false;

    // Toggle on tap (touch/click)
    wrap.addEventListener('click', (e) => {
      // ignore clicks directly on the button
      if (e.target.closest('.hover-reveal')) return;
      toggled = !toggled;
      reveal.style.opacity = toggled ? '1' : '0';
      reveal.style.pointerEvents = toggled ? 'auto' : 'none';
    });
  });
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const hidden = document.getElementById('tags-hidden');
  const checks = document.querySelectorAll('input[name="tags[]"]');
  function syncTags() {
    const values = [...checks].filter(c => c.checked).map(c => c.value);
    hidden.value = values.join(',');
  }
  checks.forEach(c => c.addEventListener('change', syncTags));
  syncTags();
});
</script>

<script>
  // ---- Slugify helper
  function slugifyTags(str) {
    return String(str || '')
      .trim()
      .toLowerCase()
      .replace(/[^a-z0-9 -]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-+|-+$/g, '');
  }

  // Auto-fill slug as user types name
  document.addEventListener('DOMContentLoaded', () => {
    const nameEl = document.getElementById('tag-name');
    const slugEl = document.getElementById('tag-slug');
    if (nameEl && slugEl) {
      nameEl.addEventListener('input', () => {
        if (!slugEl.dataset.touched || !slugEl.value) {
          slugEl.value = slugifyTags(nameEl.value);
        }
      });
      slugEl.addEventListener('input', () => slugEl.dataset.touched = '1');
    }
  });

  // ---- AJAX create tag + append to list
  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('addTagForm');
    const modalEl = document.getElementById('addTagModal');
    const submitBtn = document.getElementById('addTagSubmitBtn');
    const nameInput = document.getElementById('tag-name');
    const slugInput = document.getElementById('tag-slug');
    const nameErr = document.getElementById('tag-name-error');
    const slugErr = document.getElementById('tag-slug-error');
    const tagsList = document.getElementById('tags-list');

    if (!form || !tagsList) return;

    const bsModal = modalEl ? bootstrap.Modal.getOrCreateInstance(modalEl) : null;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      // UI: disable while saving
      submitBtn.disabled = true;
      submitBtn.querySelector('.save-text').classList.add('d-none');
      submitBtn.querySelector('.saving-text').classList.remove('d-none');
      nameErr.textContent = ''; slugErr.textContent = '';
      nameInput.classList.remove('is-invalid'); slugInput.classList.remove('is-invalid');

      try {
        // Build payload
        const payload = {
          name: nameInput.value.trim(),
          slug: slugInput.value.trim() || slugifyTags(nameInput.value)
        };

        // CSRF
        const token =
          document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
          form.querySelector('input[name="_token"]')?.value;

        // POST
        const res = await fetch(form.getAttribute('action'), {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
          },
          body: JSON.stringify(payload)
        });

        if (!res.ok) {
          const data = await res.json().catch(() => ({}));
          // validation errors format: { errors: { name: [...], slug: [...] } }
          if (data?.errors) {
            if (data.errors.name?.[0]) { nameErr.textContent = data.errors.name[0]; nameInput.classList.add('is-invalid'); }
            if (data.errors.slug?.[0]) { slugErr.textContent = data.errors.slug[0]; slugInput.classList.add('is-invalid'); }
          } else {
            alert('Failed to create tag.');
          }
          return;
        }

        const created = await res.json(); // expect { id, name, slug }
        const labelText = created.name || payload.name;
        const value = labelText; // store by name (or use slug if you prefer)
        const id = 'tag-' + (created.slug || payload.slug);

        // Add checkbox + label (pre-checked)
        const input = document.createElement('input');
        input.type = 'checkbox';
        input.className = 'btn-check';
        input.name = 'tags[]';
        input.id = id;
        input.value = value;
        input.checked = true;

        const label = document.createElement('label');
        label.className = 'btn btn-outline-primary rounded-pill px-3 py-1';
        label.setAttribute('for', id);
        label.innerHTML = `<i class="bx bx-purchase-tag me-1"></i>${labelText}`;

        tagsList.appendChild(input);
        tagsList.appendChild(label);

        // If you store as comma-string, also sync hidden input:
        const hidden = document.getElementById('tags-hidden');
        if (hidden) {
          const checks = tagsList.querySelectorAll('input[name="tags[]"]');
          hidden.value = [...checks].filter(c => c.checked).map(c => c.value).join(',');
        }

        // Reset + close modal
        form.reset();
        submitBtn.disabled = false;
        submitBtn.querySelector('.save-text').classList.remove('d-none');
        submitBtn.querySelector('.saving-text').classList.add('d-none');
        bsModal?.hide();
      } catch (err) {
        console.error(err);
        alert('Something went wrong while creating the tag.');
      } finally {
        submitBtn.disabled = false;
        submitBtn.querySelector('.save-text').classList.remove('d-none');
        submitBtn.querySelector('.saving-text').classList.add('d-none');
      }
    });
  });
</script>


<script>
document.addEventListener('DOMContentLoaded', () => {
  const dropzone = document.getElementById('image-dropzone');
  const img = document.getElementById('dz-image');
  const placeholder = document.getElementById('dz-placeholder');
  const removeBtn = document.getElementById('dz-remove');
  const input = document.getElementById('dz-input');
  const browse = document.getElementById('dz-browse');

  const showPreview = (file) => {
    const reader = new FileReader();
    reader.onload = e => {
      img.src = e.target.result;
      img.classList.remove('d-none');
      placeholder.classList.add('d-none');
      removeBtn.classList.remove('d-none');
    };
    reader.readAsDataURL(file);
  };

  browse?.addEventListener('click', () => input.click());
  input?.addEventListener('change', e => {
    const file = e.target.files?.[0]; if (file) showPreview(file);
  });

  dropzone.addEventListener('dragover', e => { e.preventDefault(); });
  dropzone.addEventListener('drop', e => {
    e.preventDefault();
    const file = e.dataTransfer.files?.[0]; if (file) { input.files = e.dataTransfer.files; showPreview(file); }
  });

  removeBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    img.src = '';
    img.classList.add('d-none');
    placeholder.classList.remove('d-none');
    removeBtn.classList.add('d-none');
    input.value = ''; // clears chosen file
    const photoId = removeBtn.dataset.photoid;
      $.ajax({
        url: `/delete/photo`, 
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        data: { photo_id: photoId },
        success: function (res) {
            console.log('Image removed successfully:', res);
        },
        error: function (xhr) {
            console.error('Error removing image:', xhr.responseText);
        }
      });
  });
});

</script>
@endsection
