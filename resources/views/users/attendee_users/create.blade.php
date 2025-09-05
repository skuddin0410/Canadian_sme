@extends('layouts.admin')

@section('title')
Admin | Add Attendee
@endsection

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
<style>
  .select2-container--bootstrap-5 .select2-selection {
  min-height: calc(1.5em + 0.75rem + 2px);
}
</style>

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
    <div class="dz-hint text-center p-3 {{ !empty($user?->photo?->file_path) ? 'd-none' : '' }}">
      <i class="bx bx-user-plus fs-2 d-block mb-1"></i>
      <small class="text-muted">Drag & drop or click</small>
    </div>

    <!-- NEW: Remove button -->
    <button type="button" id="profileRemoveBtn"
            class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 {{ empty($user?->photo?->file_path) ? 'd-none' : '' }}" data-photoid="{{ !empty($user->photo) ? $user->photo->id : '' }}">
      <i class="bx bx-trash"></i> Remove
    </button>
  </div>
</div>

<div class="col-md-8">
  <label class="form-label fw-semibold mb-2">Cover Photo</label>
  <div id="coverDropZone" class="cover-drop-zone rounded border border-2 d-flex align-items-center justify-content-center">
    <input type="file" id="coverImageInput" accept="image/*" class="d-none form-control">
    <img id="coverImagePreview"
         src="{{ $user->cover_photo->file_path ?? '' }}"
         class="w-100 h-100 object-fit-cover rounded {{ empty($user?->cover_photo?->file_path) ? 'd-none' : '' }}">
    <div class="dz-hint text-center {{ !empty($user?->cover_photo?->file_path) ? 'd-none' : '' }}">
      <i class="bx bx-image-add fs-1 d-block mb-1"></i>
      <small class="text-muted">Drag & drop or click</small>
    </div>

    <!-- NEW: Remove button -->
    <button type="button" id="coverRemoveBtn"
            class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 {{ empty($user?->cover_photo?->file_path) ? 'd-none' : '' }}" data-photoid="{{ !empty($user->cover_photo) ? $user->cover_photo->id : '' }}">
      <i class="bx bx-trash"></i> Remove
    </button>
  </div>
</div>

      </div>
    </div>
  </div>

  {{-- MAIN FORM --}}
  <form
    action="{{ route('attendee-users.store') }}"
    method="POST" enctype="multipart/form-data">
    @csrf
    @if(!empty($user)) @method('PUT') @endif

    {{-- Hidden file inputs that actually submit --}}
    <input type="file" id="profileImageInputShadow" class="d-none" name="image" accept="image/*">
    <input type="file" id="coverImageInputShadow" class="d-none" name="cover_image" accept="image/*">

    <div class="row">
      {{-- LEFT: Tabs --}}
      <div class="col-lg-12">
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
                <div class="col-lg-8">
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
                      
                      <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge mb-3">
                          <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                          <input type="text" class="form-control" name="email" id="email" value="{{ $user->email ?? old('email') }}" placeholder="Email">
                        </div>
                        @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                      </div>

                      <div class="col-md-6">
                        <label class="form-label">Mobile</label>
                        <div class="input-group input-group-merge mb-3">
                          <span class="input-group-text"><i class="bx bx-phone"></i></span>
                          <input type="text" class="form-control" name="mobile" id="mobile" value="{{ $user->mobile ?? old('mobile') }}" placeholder="Mobile">
                        </div>
                        @error('mobile') <div class="text-danger">{{ $message }}</div> @enderror
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
                        <label class="form-label">Designation</label>
                        <div class="input-group input-group-merge mb-3">
                          <span class="input-group-text"><i class="bx bx-briefcase"></i></span>
                          <input type="text" class="form-control" name="designation" id="designation" value="{{ $user->designation ?? old('designation') }}" placeholder="Designation">
                        </div>
                        @error('designation') <div class="text-danger">{{ $message }}</div> @enderror
                      </div>

                        {{-- NEW: Primary & Secondary Groups --}}
                      <div class="col-md-6">
                        <label class="form-label">User Primary Group<span class="text-danger">*</span></label>
                        <select class="form-select mb-3" name="primary_group">
                         
                          @foreach(($groups ?? []) as $g)
                          
                            <option value="{{ $g }}" {{ (old('primary_group', $user->primary_group ?? null) == $g) ? 'selected' : '' }}>
                              {{ $g }}
                            </option>
                       
                          @endforeach
                        </select>
                        @error('primary_group') <div class="text-danger">{{ $message }}</div> @enderror
                      </div>

                      <div class="col-md-6">
                        <label class="form-label">User Other Group</label>
                    @php

                      $rawSelected = old('secondary_group', $user->secondary_group ?? []);
                      $selectedSecondary = is_array($rawSelected)
                          ? $rawSelected
                          : (strlen((string) $rawSelected) ? explode(',', (string) $rawSelected) : []);
                    @endphp

                  <label class="form-label">User Secondary Group (multiple)</label>
                  <select class="form-select mb-3" name="secondary_group[]" id="secondary_group" multiple>
                    @foreach(($groups ?? []) as $g)
                      <option value="{{ $g }}" {{ in_array($g, $selectedSecondary, true) ? 'selected' : '' }}>
                        {{ $g }}
                      </option>
                    @endforeach
                  </select>
                   <small class="text-muted">Hold <kbd>Ctrl</kbd>/<kbd>⌘</kbd> to select more than one.</small>
                       @error('secondary_group') <div class="text-danger">{{ $message }}</div> @enderror

                        @error('secondary_group') <div class="text-danger">{{ $message }}</div> @enderror
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


                      <div class="col-12">
                        <label class="form-label">Bio <span class="text-danger">*</span></label>
                        <textarea name="bio" id="bio" class="form-control mb-3" rows="6" placeholder="Speaker Bio">{{ $user->bio ?? old('bio') }}</textarea>
                        @error('bio') <div class="text-danger">{{ $message }}</div> @enderror
                      </div>

                      {{-- NEW: Checkboxes --}}
                      <div class="col-12">
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-check mb-2">
                              <input class="form-check-input" type="checkbox" id="send_email" name="send_email" value="1" {{ old('send_email', $user->send_email ?? false) ? 'checked' : '' }}>
                              <label class="form-check-label" for="send_email">Would you like to send email?</label>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="form-check mb-2">
                              <input class="form-check-input" type="checkbox" id="gdpr_consent" name="gdpr_consent" value="1" {{ old('gdpr_consent', $user->gdpr_consent ?? false) ? 'checked' : '' }}>
                              <label class="form-check-label" for="gdpr_consent">Networking / GDPR Consent (share profile & content with other users)</label>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                </div>

                <div class="col-lg-4">
                  
                   {{--  <div class="d-flex align-items-center">
                      <i class="bx bx-share-alt fs-4 me-2"></i><span class="fw-semibold">Social Links</span>
                    </div> --}}
                  
                      <div class="mb-3">
                        <label class="form-label">Website</label>
                        <div class="input-group input-group-merge">
                          <span class="input-group-text"><i class="bx bx-link"></i></span>
                          <input type="text" class="form-control" name="website_url" value="{{ old('website_url') }}" placeholder="https://example.com">
                        </div>
                        @error('website_url') <div class="text-danger">{{ $message }}</div> @enderror
                      </div>

                      <div class="mb-3">
                        <label class="form-label">LinkedIn</label>
                        <div class="input-group input-group-merge">
                          <span class="input-group-text"><i class="bx bxl-linkedin"></i></span>
                          <input type="text" class="form-control" name="linkedin_url" value="{{  old('linkedin_url') }}" placeholder="https://linkedin.com/in/username">
                        </div>
                        @error('linkedin_url') <div class="text-danger">{{ $message }}</div> @enderror
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Facebook</label>
                        <div class="input-group input-group-merge">
                          <span class="input-group-text"><i class="bx bxl-facebook"></i></span>
                          <input type="text" class="form-control" name="facebook_url" value="{{  old('facebook_url') }}" placeholder="https://facebook.com/username">
                        </div>
                        @error('facebook_url') <div class="text-danger">{{ $message }}</div> @enderror
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Instagram</label>
                        <div class="input-group input-group-merge">
                          <span class="input-group-text"><i class="bx bxl-instagram"></i></span>
                          <input type="text" class="form-control" name="instagram_url" value="{{ old('instagram_url') }}" placeholder="https://instagram.com/username">
                        </div>
                        @error('instagram_url') <div class="text-danger">{{ $message }}</div> @enderror
                      </div>

                       <div>
                        <label class="form-label">Twitter</label>
                        <div class="input-group input-group-merge">
                          <span class="input-group-text"><i class="bx bxl-twitter"></i></span>
                          <input type="text" class="form-control" name="twitter_url" value="{{  old('twitter_url') }}" placeholder="https://twitter.com/username">
                        </div>
                        @error('twitter_url') <div class="text-danger">{{ $message }}</div> @enderror
                      </div>
                
                </div>
              </div>
              </div>

              {{-- ACCESS PERMISSIONS --}}
              <div class="tab-pane fade" id="access" role="tabpanel">
                <div class="row">
                
                    
                    <div class="col-md-12">
                      <label class="form-label">Speaker</label>
                      <select class="form-select select2" name="access_speaker_ids"
                              data-placeholder="Select speaker(s)" data-allow-clear="true">
                              <option value="">Please select</option>
                        @foreach($speakers as $speaker)
                          <option value="{{ $speaker->id }}">
                            {{ $speaker->full_name }}
                          </option>
                        @endforeach
                      </select>
                      @error('access_speaker_ids') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    {{-- EXHIBITORS --}}
                   
                    <div class="col-md-12">
                      <label class="form-label">Exhibitor</label>
                      <select class="form-select select2" name="access_exhibitor_ids"
                              data-placeholder="Select exhibitor(s)" data-allow-clear="true">
                              <option value="">Please select</option>
                        @foreach($exhibitors as $exhibitor)
                          <option value="{{ $exhibitor->id }}">
                            {{ $exhibitor->name }}
                          </option>
                        @endforeach
                      </select>
                      @error('access_exhibitor_ids') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

      
                    <div class="col-md-12">
                      <label class="form-label">Sponsors</label>
                      <select class="form-select select2" name="access_sponsor_ids"
                              data-placeholder="Select sponsor(s)" data-allow-clear="true">
                               <option value="">Please select</option>
                        @foreach($sponsors as $sponsor)
                          <option value="{{ $sponsor->id }}">
                            {{ $sponsor->name }}
                          </option>
                        @endforeach
                      </select>
                      @error('access_sponsor_ids') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                </div>
              </div>

              {{-- PRIVATE DOCS --}}
              <div class="tab-pane fade" id="docs" role="tabpanel">
              <label class="form-label d-block">Upload Private Docs</label>
                  <div id="docsDropZone" class="docs-drop-zone text-center">
                    <input type="file" id="privateDocsInput" name="private_docs[]" accept="image/*" class="d-none" multiple>
                    <div class="dz-hint">
                      <i class="bx bx-upload fs-1 d-block"></i>
                      <span class="fw-medium">Drag & drop files here</span>
                      <small class="d-block text-muted">PNG, JPG, PDF (multiple allowed) — or click to browse</small>
                    </div>
                    <div id="docsPreview" class="row g-3 mt-2"></div>
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

</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const tabKey = 'attendee.activeTab';
  const tabsEl = document.getElementById('attendeeTabs');
  if (!tabsEl) return;

  function showTab(target) {
    const trigger = tabsEl.querySelector(`button[data-bs-target="${target}"]`);
    if (!trigger) return;
    const tab = new bootstrap.Tab(trigger);
    tab.show();
  }

  const candidates = [window.location.hash, localStorage.getItem(tabKey), '#basic'];
  const desired = candidates.find(sel => sel && tabsEl.querySelector(`button[data-bs-target="${sel}"]`));
  if (desired) showTab(desired);

  // When user changes tab, remember it and update the URL hash (without reload)
  tabsEl.addEventListener('shown.bs.tab', function (e) {
    const target = e.target.getAttribute('data-bs-target'); // e.g. "#access"
    localStorage.setItem(tabKey, target);
    history.replaceState(null, '', target); // set hash without jumping/reloading
  });
});
</script>

{{-- Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $('.select2').each(function () {
    const $el = $(this);
    $el.select2({
      theme: 'bootstrap-5',
      width: '100%',
      placeholder: $el.data('placeholder') || '',
      allowClear: !!$el.data('allow-clear'),
      closeOnSelect: false
    });
  });


(function(){
  const zone     = document.getElementById('profileDropZone');
  const visible  = document.getElementById('profileImageInput');
  const shadow   = document.getElementById('profileImageInputShadow'); // if you have one
  const preview  = document.getElementById('profileImagePreview');
  const hint     = zone.querySelector('.dz-hint');
  const removeBtn= document.getElementById('profileRemoveBtn');
  //const removeFg = document.getElementById('remove_profile_image');

  function fileToDataURL(file, cb){ const r=new FileReader(); r.onload=e=>cb(e.target.result); r.readAsDataURL(file); }
  function setShadowFile(file){
    if (!shadow) return;
    const dt = new DataTransfer();
    if (file) dt.items.add(file);
    shadow.files = dt.files;
  }
  function clearInputs(){
    if (visible) visible.value = '';
    if (shadow) shadow.value = '';
  }
  function showRemove(show){
    removeBtn?.classList.toggle('d-none', !show);
  }

  function handle(files){
    const f = files && files[0];
    if (!f || !f.type?.startsWith('image/')) return;
    fileToDataURL(f, url => {
      preview.src = url;
      preview.classList.remove('d-none');
      hint?.classList.add('d-none');
      showRemove(true);
     // not removing anymore because we have a new file
    });
    // mirror to shadow if you use one
    setShadowFile(f);
  }

  // clicking zone opens file picker
  zone.addEventListener('click', e => {
    // don’t trigger file dialog when pressing remove
    if (e.target === removeBtn) return;
    visible.click();
  });

  // DnD behavior
  ['dragover','dragleave','drop'].forEach(evt=>{
    zone.addEventListener(evt,(e)=>{
      e.preventDefault();
      if (evt==='dragover') zone.classList.add('dragover');
      if (evt!=='dragover') zone.classList.remove('dragover');
      if (evt==='drop') handle(e.dataTransfer.files);
    });
  });

  // File chosen
  visible.addEventListener('change', e => handle(e.target.files));

  removeBtn.addEventListener('click', (e) => {
      e.stopPropagation();
  
      preview.src = '';
      preview.classList.add('d-none');
      hint?.classList.remove('d-none');
      clearInputs();
      showRemove(false);
    });

  // If page loads with an existing image, show button
  if (!preview.classList.contains('d-none')) showRemove(true);
})();
</script>

<script>
(function(){
  const zone      = document.getElementById('coverDropZone');
  const visible   = document.getElementById('coverImageInput');          // picker used for UX
  const shadow    = document.getElementById('coverImageInputShadow');    // real <input name="cover_image">
  const preview   = document.getElementById('coverImagePreview');
  const hint      = zone.querySelector('.dz-hint');
  const removeBtn = document.getElementById('coverRemoveBtn');
  //const removeFg  = document.getElementById('remove_cover_image');

  function fileToDataURL(file, cb){ const r=new FileReader(); r.onload=e=>cb(e.target.result); r.readAsDataURL(file); }
  function mirrorToShadow(file){
    if (!shadow) return;
    const dt = new DataTransfer();
    if (file) dt.items.add(file);
    shadow.files = dt.files;
  }
  function clearFiles(){
    if (visible) visible.value = '';
    if (shadow)  shadow.value  = '';
  }
  function showRemove(show){ removeBtn?.classList.toggle('d-none', !show); }

  function handle(files){
    const f = files && files[0];
    if (!f || !f.type?.startsWith('image/')) return;
    fileToDataURL(f, url => {
      preview.src = url;
      preview.classList.remove('d-none');
      hint?.classList.add('d-none');
      showRemove(true);
      //removeFg.value = '0'; // we’re NOT removing if a new file is chosen
    });
    mirrorToShadow(f);
  }

  // open file dialog (don’t trigger when clicking Remove)
  zone.addEventListener('click', e => {
    if (e.target === removeBtn) return;
    visible.click();
  });

  // drag & drop
  ['dragover','dragleave','drop'].forEach(evt=>{
    zone.addEventListener(evt,(e)=>{
      e.preventDefault();
      if (evt==='dragover') zone.classList.add('dragover');
      if (evt!=='dragover') zone.classList.remove('dragover');
      if (evt==='drop') handle(e.dataTransfer.files);
    });
  });

  // chosen via dialog
  visible.addEventListener('change', e => handle(e.target.files));

  // remove button
  removeBtn?.addEventListener('click', e => {
    e.stopPropagation();
    preview.src = '';
    preview.classList.add('d-none');
    hint?.classList.remove('d-none');
    clearFiles();
    showRemove(false);
  });

  // initial state
  if (!preview.classList.contains('d-none')) showRemove(true);
})();
  </script>

  <script>
// File helpers
function fileToDataURL(file, cb) {
  const r = new FileReader();
  r.onload = (e) => cb(e.target.result);
  r.readAsDataURL(file);
}

// Function to remove document preview
function removeDoc(fileId) {
  const fileElement = document.getElementById(`doc-preview-${fileId}`);
  if (fileElement) {
    fileElement.remove(); // Remove the file from the preview
  }
  // Optionally, remove the file from the form data or handle the removal in your backend
}

// Function to create a preview for uploaded documents
function makeCard(file, dataURL) {
  const fileId = file.name + Date.now(); // Use unique identifier (you can use a better method if necessary)

  const col = document.createElement('div');
  col.className = 'col-12 col-sm-6 col-md-4 col-lg-3';
  col.id = `doc-preview-${fileId}`;

  const card = document.createElement('div');
  card.className = 'card h-100';

  const body = document.createElement('div');
  body.className = 'card-body d-flex flex-column align-items-center';

  if (file.type.startsWith('image/')) {
    const img = document.createElement('img');
    img.src = dataURL;
    img.className = 'img-fluid mb-2 rounded';
    img.style.maxHeight = '140px';
    body.appendChild(img);
  } else {
    const ico = document.createElement('i');
    ico.className = 'bx bx-file fs-1 mb-2';
    body.appendChild(ico);
  }

  const name = document.createElement('div');
  name.className = 'small text-center text-truncate w-100';
  name.title = file.name;
  name.textContent = file.name;
  body.appendChild(name);

  // Add remove button
  const removeBtn = document.createElement('button');
  removeBtn.className = 'btn btn-sm btn-danger mt-2';
  removeBtn.textContent = 'Remove';
  
  // Attach the click event listener using `once: true` to ensure it only triggers once
  removeBtn.addEventListener('click', (e) => {
    e.stopPropagation(); // Prevent the click event from reaching the upload area
    removeDoc(fileId);
  }, { once: true });

  body.appendChild(removeBtn);

  card.appendChild(body);
  col.appendChild(card);
  return col;
}

// Handle file selection or drag-and-drop
document.getElementById('privateDocsInput').addEventListener('change', (e) => {
  const files = e.target.files;
  const previewWrap = document.getElementById('docsPreview');

  Array.from(files).forEach((file) => {
    if (file.type.startsWith('image/') || file.type === 'application/pdf') {
      fileToDataURL(file, (url) => {
        const docCard = makeCard(file, url);
        previewWrap.appendChild(docCard);
      });
    }
  });
});

// Drag-and-drop functionality
const zone = document.getElementById('docsDropZone');
zone.addEventListener('click', (e) => {
  // Prevent the file input dialog from opening if the remove button is clicked
  if (e.target.classList.contains('btn-danger')) return;
  document.getElementById('privateDocsInput').click();
});
zone.addEventListener('dragover', (e) => e.preventDefault());
zone.addEventListener('dragleave', (e) => e.preventDefault());
zone.addEventListener('drop', (e) => {
  e.preventDefault();
  const files = e.dataTransfer.files;
  Array.from(files).forEach((file) => {
    if (file.type.startsWith('image/') || file.type === 'application/pdf') {
      fileToDataURL(file, (url) => {
        const docCard = makeCard(file, url);
        document.getElementById('docsPreview').appendChild(docCard);
      });
    }
  });
});

</script>
@endsection

