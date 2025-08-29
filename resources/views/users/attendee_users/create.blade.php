@extends('layouts.admin')

@section('title')
Admin | Add Attendee
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Attendee/</span>Create</h4>

  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-3 align-items-start">
        <div class="col-md-4">
          <label class="form-label fw-semibold mb-2">Profile Photo</label>
          <div id="profileDropZone" class="profile-drop-zone rounded border border-2 d-flex align-items-center justify-content-center">
            <input type="file" id="profileImageInput" accept="image/*" class="d-none form-control">
            <img id="profileImagePreview"
                 src="{{ $user->photo->file_path ?? '' }}"
                 class="w-100 h-100 object-fit-cover rounded {{ empty($user?->photo?->file_path) ? 'd-none' : '' }}">
            <div class="dz-hint text-center p-3">
              <i class="bx bx-user-plus fs-2 d-block mb-1"></i>
              <small class="text-muted">Drag & drop or click</small>
            </div>
          </div>
        </div>

        <div class="col-md-8">
          <label class="form-label fw-semibold mb-2">Cover Photo</label>
          <div id="coverDropZone" class="cover-drop-zone rounded border border-2 d-flex align-items-center justify-content-center">
            <input type="file" id="coverImageInput" accept="image/*" class="d-none form-control">
            <img id="coverImagePreview"
                 src="{{ $user->cover_photo->file_path ?? '' }}"
                 class="w-100 h-100 object-fit-cover rounded {{ empty($user?->cover_photo?->file_path) ? 'd-none' : '' }}">
            <div class="dz-hint text-center">
              <i class="bx bx-image-add fs-1 d-block mb-1"></i>
              <small class="text-muted">Drag & drop or click</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- MAIN FORM --}}
  <form
    action="@if(!empty($user)) {{ route('attendee-users.update',['user'=>$user->id]) }} @else {{ route('attendee-users.store') }} @endif"
    method="POST" enctype="multipart/form-data">
    @csrf
    @if(!empty($user)) @method('PUT') @endif

    {{-- Hidden file inputs that actually submit --}}
    <input type="file" id="profileImageInputShadow" class="d-none" name="image" accept="image/*">
    <input type="file" id="coverImageInputShadow" class="d-none" name="cover_image" accept="image/*">

    <div class="row">
      {{-- LEFT: Tabs --}}
      <div class="col-lg-8">
        <div class="card mb-4">
          <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="attendeeTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">Basic Information</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="access-tab" data-bs-toggle="tab" data-bs-target="#access" type="button" role="tab">Access Permissions</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs" type="button" role="tab">Private Docs</button>
              </li>
            </ul>
          </div>

          <div class="card-body">
            <div class="tab-content">
              {{-- BASIC INFORMATION --}}
              <div class="tab-pane fade show active" id="basic" role="tabpanel">
                <div class="row">
                  <div class="col-md-6">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <div class="input-group input-group-merge mb-3">
                      <span class="input-group-text"><i class="bx bx-user"></i></span>
                      <input type="text" class="form-control" name="first_name" id="slug-source"
                             value="{{ $user->name ?? old('first_name') }}" placeholder="First name">
                    </div>
                    @error('first_name') <div class="text-danger">{{ $message }}</div> @enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <div class="input-group input-group-merge mb-3">
                      <span class="input-group-text"><i class="bx bx-user"></i></span>
                      <input type="text" class="form-control" name="last_name" id="last-name-target"
                             value="{{ $user->lastname ?? old('last_name') }}" placeholder="Last name">
                    </div>
                    @error('last_name') <div class="text-danger">{{ $message }}</div> @enderror
                  </div>

                  {{-- NEW: Primary & Secondary Groups --}}
                  <div class="col-md-6">
                    <label class="form-label">User Primary Group</label>
                    <select class="form-select mb-3" name="primary_group_id">
                      <option value="">Select primary group</option>
                      @foreach(($groups ?? []) as $g)
                        <option value="{{ $g->id }}" {{ (old('primary_group_id', $user->primary_group_id ?? null) == $g->id) ? 'selected' : '' }}>
                          {{ $g->name }}
                        </option>
                      @endforeach
                    </select>
                    @error('primary_group_id') <div class="text-danger">{{ $message }}</div> @enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">User Secondary Group</label>
                    <select class="form-select mb-3" name="secondary_group_id">
                      <option value="">Select secondary group</option>
                      @foreach(($groups ?? []) as $g)
                        <option value="{{ $g->id }}" {{ (old('secondary_group_id', $user->secondary_group_id ?? null) == $g->id) ? 'selected' : '' }}>
                          {{ $g->name }}
                        </option>
                      @endforeach
                    </select>
                    @error('secondary_group_id') <div class="text-danger">{{ $message }}</div> @enderror
                  </div>

                  {{-- NEW: Status --}}
                  <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="form-select mb-3" name="status">
                      @php $status = old('status', $user->status ?? 'confirmed'); @endphp
                      <option value="confirmed" {{ $status==='confirmed'?'selected':'' }}>Confirmed</option>
                      <option value="waitlist" {{ $status==='waitlist'?'selected':'' }}>Waitlist</option>
                      <option value="inactive" {{ $status==='inactive'?'selected':'' }}>Inactive</option>
                    </select>
                    @error('status') <div class="text-danger">{{ $message }}</div> @enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Company</label>
                    <div class="input-group input-group-merge mb-3">
                      <span class="input-group-text"><i class="bx bx-buildings"></i></span>
                      <input type="text" class="form-control" name="company" value="{{ $user->company ?? old('company') }}" placeholder="Company / Organization">
                    </div>
                    @error('company') <div class="text-danger">{{ $message }}</div> @enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <div class="input-group input-group-merge mb-3">
                      <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                      <input type="text" class="form-control" name="email" id="email" value="{{ $user->email ?? old('email') }}" placeholder="Email">
                    </div>
                    @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Mobile <span class="text-danger">*</span></label>
                    <div class="input-group input-group-merge mb-3">
                      <span class="input-group-text"><i class="bx bx-phone"></i></span>
                      <input type="text" class="form-control" name="mobile" id="mobile" value="{{ $user->mobile ?? old('mobile') }}" placeholder="Mobile">
                    </div>
                    @error('mobile') <div class="text-danger">{{ $message }}</div> @enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Designation</label>
                    <div class="input-group input-group-merge mb-3">
                      <span class="input-group-text"><i class="bx bx-briefcase"></i></span>
                      <input type="text" class="form-control" name="designation" id="designation" value="{{ $user->designation ?? old('designation') }}" placeholder="Designation">
                    </div>
                    @error('designation') <div class="text-danger">{{ $message }}</div> @enderror
                  </div>

                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">User Interest</label>
                      <div class="d-flex flex-wrap gap-2">
                        
                        <input type="checkbox" class="btn-check" id="tagEvent" name="tags[]" value="Event"
                          {{ in_array('Event', old('tags', $user->tags ?? [])) ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary" for="tagEvent">Event</label>

                        <input type="checkbox" class="btn-check" id="tagCloudTrends" name="tags[]" value="CloudTrends"
                          {{ in_array('CloudTrends', old('tags', $user->tags ?? [])) ? 'checked' : '' }}>
                        <label class="btn btn-outline-success" for="tagCloudTrends">CloudTrends</label>

                        <input type="checkbox" class="btn-check" id="tagDataseecurity" name="tags[]" value="Dataseecurity"
                          {{ in_array('Dataseecurity', old('tags', $user->tags ?? [])) ? 'checked' : '' }}>
                        <label class="btn btn-outline-info" for="tagDataseecurity">Dataseecurity</label>

                        <input type="checkbox" class="btn-check" id="tagTechnoVation" name="tags[]" value="TechnoVation"
                          {{ in_array('TechnoVation', old('tags', $user->tags ?? [])) ? 'checked' : '' }}>
                        <label class="btn btn-outline-warning" for="tagTechnoVation">TechnoVation</label>

                      </div>
                      @error('tags')
                        <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>

                  </div>

                  <div class="col-12">
                    <label class="form-label">Bio <span class="text-danger">*</span></label>
                    <textarea name="bio" id="bio" class="form-control mb-3" rows="6" placeholder="Speaker Bio">{{ $user->bio ?? old('bio') }}</textarea>
                    @error('bio') <div class="text-danger">{{ $message }}</div> @enderror
                  </div>

                  {{-- NEW: Checkboxes --}}
                  <div class="col-12">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-check mb-2">
                          <input class="form-check-input" type="checkbox" id="send_email" name="send_email" value="1" {{ old('send_email', $user->send_email ?? false) ? 'checked' : '' }}>
                          <label class="form-check-label" for="send_email">Would you like to send email?</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-check mb-2">
                          <input class="form-check-input" type="checkbox" id="gdpr_consent" name="gdpr_consent" value="1" {{ old('gdpr_consent', $user->gdpr_consent ?? false) ? 'checked' : '' }}>
                          <label class="form-check-label" for="gdpr_consent">Networking / GDPR Consent (share profile & content with other users)</label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              {{-- ACCESS PERMISSIONS: 3 separate selects --}}
              <div class="tab-pane fade" id="access" role="tabpanel">
                <div class="row">
                  @php
                    $ap = [
                      'speaker'    => old('access_speaker',    $user->access_speaker    ?? 0),
                      'exhibitor'  => old('access_exhibitor',  $user->access_exhibitor  ?? 0),
                      'sponsors'   => old('access_sponsors',   $user->access_sponsors   ?? 0),
                    ];
                  @endphp
                  <div class="col-md-4">
                    <label class="form-label">Speaker</label>
                    <select class="form-select mb-3" name="access_speaker">
                      <option value="0" {{ !$ap['speaker']?'selected':'' }}>No</option>
                      <option value="1" {{  $ap['speaker']?'selected':'' }}>Yes</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Exhibitor</label>
                    <select class="form-select mb-3" name="access_exhibitor">
                      <option value="0" {{ !$ap['exhibitor']?'selected':'' }}>No</option>
                      <option value="1" {{  $ap['exhibitor']?'selected':'' }}>Yes</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Sponsors</label>
                    <select class="form-select mb-3" name="access_sponsors">
                      <option value="0" {{ !$ap['sponsors']?'selected':'' }}>No</option>
                      <option value="1" {{  $ap['sponsors']?'selected':'' }}>Yes</option>
                    </select>
                  </div>
                </div>
              </div>

              {{-- PRIVATE DOCS --}}
              <div class="tab-pane fade" id="docs" role="tabpanel">
                <label class="form-label d-block">Upload Private Docs</label>
                <div id="docsDropZone" class="docs-drop-zone text-center">
                  <input type="file" id="privateDocsInput" name="private_docs[]" accept="image/*,application/pdf" class="d-none" multiple>
                  <div class="dz-hint">
                    <i class="bx bx-upload fs-1 d-block"></i>
                    <span class="fw-medium">Drag & drop files here</span>
                    <small class="d-block text-muted">PNG, JPG, PDF (multiple allowed) — or click to browse</small>
                  </div>
                  <div id="docsPreview" class="row g-3 mt-2"></div>
                </div>

                {{-- Existing docs list (below) --}}
                <div class="mt-4">
                  <h6 class="mb-3">Private Documents</h6>
                  @php $docs = $user->privateDocs ?? []; @endphp
                  @if (!empty($docs) && count($docs))
                    <div class="list-group">
                      @foreach ($docs as $doc)
                        <a href="{{ $doc->file_path }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center">
                          <i class="bx bx-file me-2"></i> {{ $doc->original_name ?? basename($doc->file_path) }}
                          <span class="ms-auto text-muted small">{{ number_format(($doc->size ?? 0)/1024, 1) }} KB</span>
                        </a>
                      @endforeach
                    </div>
                  @else
                    <p class="text-muted mb-0">No documents uploaded yet.</p>
                  @endif
                </div>
              </div>
            </div>
          </div>

          <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('attendee-users.index') }}" class="btn btn-outline-primary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Save</button>
          </div>
        </div>
      </div>

      {{-- RIGHT: Social Links panel --}}
      <aside class="col-lg-4">
        <div class="card mb-4">
          <div class="card-header d-flex align-items-center">
            <i class="bx bx-share-alt fs-4 me-2"></i><span class="fw-semibold">Social Links</span>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Website</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bx-link"></i></span>
                <input type="text" class="form-control" name="website_url" value="{{ $user->website_url ?? old('website_url') }}" placeholder="https://example.com">
              </div>
              @error('website_url') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">LinkedIn</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bxl-linkedin"></i></span>
                <input type="text" class="form-control" name="linkedin_url" value="{{ $user->linkedin_url ?? old('linkedin_url') }}" placeholder="https://linkedin.com/in/username">
              </div>
              @error('linkedin_url') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Facebook</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bxl-facebook"></i></span>
                <input type="text" class="form-control" name="facebook_url" value="{{ $user->facebook_url ?? old('facebook_url') }}" placeholder="https://facebook.com/username">
              </div>
              @error('facebook_url') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Instagram</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bxl-instagram"></i></span>
                <input type="text" class="form-control" name="instagram_url" value="{{ $user->instagram_url ?? old('instagram_url') }}" placeholder="https://instagram.com/username">
              </div>
              @error('instagram_url') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div>
              <label class="form-label">Twitter</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="bx bxl-twitter"></i></span>
                <input type="text" class="form-control" name="twitter_url" value="{{ $user->twitter_url ?? old('twitter_url') }}" placeholder="https://twitter.com/username">
              </div>
              @error('twitter_url') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
          </div>
        </div>
      </aside>
    </div>
  </form>
</div>
@endsection

@section('scripts')
<style>
  .object-fit-cover{object-fit:cover}
  .profile-drop-zone{
    height:220px; background:#f8f9fa; border:2px dashed #cfd4da; border-radius:.5rem; position:relative; overflow:hidden; cursor:pointer;
  }
  .cover-drop-zone{
    height:220px; background:#f8f9fa; border:2px dashed #cfd4da; border-radius:.5rem; position:relative; overflow:hidden; cursor:pointer;
  }
  .docs-drop-zone{
    min-height:140px; background:#f8f9fa; border:2px dashed #cfd4da; border-radius:.5rem; padding:16px; cursor:pointer;
  }
  .profile-drop-zone.dragover,
  .cover-drop-zone.dragover,
  .docs-drop-zone.dragover{ background:#eef6ff; border-color:#6ea8fe; }
  .profile-drop-zone .dz-hint,
  .cover-drop-zone .dz-hint{ position:absolute; inset:0; display:flex; align-items:center; justify-content:center; flex-direction:column; }
</style>

<script>
  // Keep your slug generator behavior
  function slugify(str){str=str.replace(/^\s+|\s+$/g,'').toLowerCase().replace(/[^a-z0-9 -]/g,'').replace(/\s+/g,'-').replace(/-+/g,'-');return str.replace(/^-+|-+$/g,'')}
  $(function(){
    $("#last-name-target,#slug-source").on('keyup',function(){
      var Text=$('#slug-source').val(); var Last=$('#last-name-target').val();
      if(Last!==undefined && Text!==undefined){ $("#slug-target").val(slugify(Text+" "+Last)); }
    });
  });

  // Helpers
  function fileToDataURL(file,cb){const r=new FileReader(); r.onload=e=>cb(e.target.result); r.readAsDataURL(file);}
  function setShadowFile(visibleEl, shadowEl, file){
    const dt=new DataTransfer(); dt.items.add(file); shadowEl.files=dt.files;
    const dt2=new DataTransfer(); dt2.items.add(file); visibleEl.files=dt2.files;
  }
  function setMultipleShadowFiles(visibleEl, shadowName, files){
    let hidden=document.querySelector(`input[name="${shadowName}[]"]`);
    if(!hidden){ hidden=document.createElement('input'); hidden.type='file'; hidden.name=`${shadowName}[]`; hidden.multiple=true; hidden.classList.add('d-none'); document.querySelector('form').appendChild(hidden); }
    const dt=new DataTransfer(); Array.from(files).forEach(f=>dt.items.add(f)); hidden.files=dt.files;
    const dt2=new DataTransfer(); Array.from(files).forEach(f=>dt2.items.add(f)); visibleEl.files=dt2.files;
  }

  // Profile (left)
  (function(){
    const zone=document.getElementById('profileDropZone');
    const visible=document.getElementById('profileImageInput');
    const shadow=document.getElementById('profileImageInputShadow');
    const preview=document.getElementById('profileImagePreview');

    function handle(files){
      const f=files[0]; if(!f||!f.type.startsWith('image/')) return;
      fileToDataURL(f,(url)=>{ preview.src=url; preview.classList.remove('d-none'); zone.querySelector('.dz-hint')?.classList.add('d-none'); });
      setShadowFile(visible,shadow,f);
    }
    zone.addEventListener('click',()=>visible.click());
    ['dragover','dragleave','drop'].forEach(evt=>{
      zone.addEventListener(evt,(e)=>{ e.preventDefault(); if(evt==='dragover') zone.classList.add('dragover'); if(evt!=='dragover') zone.classList.remove('dragover'); if(evt==='drop') handle(e.dataTransfer.files); });
    });
    visible.addEventListener('change',(e)=>handle(e.target.files));
  })();

  // Cover (right)
  (function(){
    const zone=document.getElementById('coverDropZone');
    const visible=document.getElementById('coverImageInput');
    const shadow=document.getElementById('coverImageInputShadow');
    const preview=document.getElementById('coverImagePreview');

    function handle(files){
      const f=files[0]; if(!f||!f.type.startsWith('image/')) return;
      fileToDataURL(f,(url)=>{ preview.src=url; preview.classList.remove('d-none'); zone.querySelector('.dz-hint')?.classList.add('d-none'); });
      setShadowFile(visible,shadow,f);
    }
    zone.addEventListener('click',()=>visible.click());
    ['dragover','dragleave','drop'].forEach(evt=>{
      zone.addEventListener(evt,(e)=>{ e.preventDefault(); if(evt==='dragover') zone.classList.add('dragover'); if(evt!=='dragover') zone.classList.remove('dragover'); if(evt==='drop') handle(e.dataTransfer.files); });
    });
    visible.addEventListener('change',(e)=>handle(e.target.files));
  })();

  // Private Docs
  (function(){
    const zone=document.getElementById('docsDropZone');
    const input=document.getElementById('privateDocsInput');
    const previewWrap=document.getElementById('docsPreview');

    function makeCard(file, dataURL){
      const col=document.createElement('div'); col.className='col-12 col-sm-6 col-md-4 col-lg-3';
      const card=document.createElement('div'); card.className='card h-100';
      const body=document.createElement('div'); body.className='card-body d-flex flex-column align-items-center';
      if(file.type.startsWith('image/')){ const img=document.createElement('img'); img.src=dataURL; img.className='img-fluid mb-2 rounded'; img.style.maxHeight='140px'; body.appendChild(img); }
      else { const ico=document.createElement('i'); ico.className='bx bx-file fs-1 mb-2'; body.appendChild(ico); }
      const name=document.createElement('div'); name.className='small text-center text-truncate w-100'; name.title=file.name; name.textContent=file.name;
      body.appendChild(name); card.appendChild(body); col.appendChild(card); return col;
    }

    function handle(files){
      const accepted=Array.from(files).filter(f=>f.type.startsWith('image/')||f.type==='application/pdf');
      if(!accepted.length) return;
      setMultipleShadowFiles(input,'private_docs',accepted);
      previewWrap.innerHTML=''; accepted.forEach(f=>{ if(f.type.startsWith('image/')) fileToDataURL(f,(url)=>previewWrap.appendChild(makeCard(f,url))); else previewWrap.appendChild(makeCard(f,null)); });
    }

    zone.addEventListener('click',()=>input.click());
    ['dragover','dragleave','drop'].forEach(evt=>{
      zone.addEventListener(evt,(e)=>{ e.preventDefault(); if(evt==='dragover') zone.classList.add('dragover'); if(evt!=='dragover') zone.classList.remove('dragover'); if(evt==='drop') handle(e.dataTransfer.files); });
    });
    input.addEventListener('change',(e)=>handle(e.target.files));
  })();
</script>
@endsection

