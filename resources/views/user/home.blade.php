@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- SUCCESS --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">Please fix the highlighted errors.</div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif


    @if($errors->has('general'))
    <div class="alert alert-danger">
        {{ $errors->first('general') }}
    </div>
    @endif


    {{-- =========================
      CARD 1: EDIT PROFILE
    ========================= --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Edit Profile</h5>
        </div>

        <div class="card-body">
            @php
            $selectedTags = old('tags', $user->tags ?? []);
            if (is_string($selectedTags)) $selectedTags = array_filter(array_map('trim', explode(',', $selectedTags)));
            if (!is_array($selectedTags)) $selectedTags = [];
            @endphp

            <form action="{{ route('user.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">First name</label>
                        <input type="text" name="first_name"
                            value="{{ old('first_name', $user->name ?? '') }}"
                            class="form-control @error('first_name') is-invalid @enderror">
                        @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Last name</label>
                        <input type="text" name="last_name"
                            value="{{ old('last_name', $user->lastname ?? '') }}"
                            class="form-control @error('last_name') is-invalid @enderror">
                        @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Designation</label>
                        <input type="text" name="designation"
                            value="{{ old('designation', $user->designation ?? '') }}"
                            class="form-control @error('designation') is-invalid @enderror">
                        @error('designation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Company name</label>
                        <input type="text" name="company_name"
                            value="{{ old('company_name', !empty($user->usercompany) ? $user->usercompany->name : $user->company) }}"
                            class="form-control @error('company_name') is-invalid @enderror">
                        @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Company website</label>
                        <input type="text" name="company_website"
                            placeholder="https://example.com"
                            value="{{ old('company_website', !empty($user->usercompany) ? $user->usercompany->website : $user->website_url) }}"
                            class="form-control @error('company_website') is-invalid @enderror">
                        @error('company_website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label d-block mb-2">Tags</label>
                        <div class="tag-grid">
                            @foreach($availableTags as $tag)
                            @php $isChecked = in_array($tag, $selectedTags); @endphp
                            <input type="checkbox" class="tag-check" id="tag_{{ $loop->index }}" name="tags[]" value="{{ $tag }}" {{ $isChecked ? 'checked' : '' }}>
                            <label class="tag-box" for="tag_{{ $loop->index }}">{{ $tag }}</label>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email address</label>
                        <input type="email" name="email"
                            value="{{ old('email', $user->email ?? '') }}"
                            class="form-control @error('email') is-invalid @enderror">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone number</label>
                        <input type="text" name="phone"
                            value="{{ old('phone', $user->mobile ?? '') }}"
                            class="form-control @error('phone') is-invalid @enderror">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Bio</label>
                        <textarea name="bio" rows="5"
                            class="form-control @error('bio') is-invalid @enderror">{{ old('bio', $user->bio ?? '') }}</textarea>
                        @error('bio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">Save Profile</button>
                </div>
            </form>
        </div>
    </div>


    {{-- =========================
      CARD 2: COMPANY DETAILS EDIT
    ========================= --}}
    @if($user->access_exhibitor_ids)
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Company Details (Edit)</h5>
        </div>

        <div class="card-body">
            <form action="{{ route('user.company.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Exhibitor Name</label>
                        <input type="text" name="exhibitor_name"
                            value="{{ old('exhibitor_name', !empty($user->usercompany) ? $user->usercompany->name : $user->company) }}"
                            class="form-control @error('exhibitor_name') is-invalid @enderror">
                        @error('exhibitor_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Booth No</label>
                        <input type="text" name="booth_no"
                            value="{{ old('booth_no', $user->usercompany->booth ?? '') }}"
                            class="form-control @error('booth_no') is-invalid @enderror">
                        @error('booth_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Banner --}}
                    <div class="col-md-12">
                        <label class="form-label">Uploaded Banner</label>

                        @php
                        $bannerUrl = $user->usercompany->quickLinkIconFile?->file_path ?? asset('images/eventify-banner.jpg');
                        @endphp

                        <img id="bannerPreview"
                            src="{{ $bannerUrl }}"
                            style="width:100%;max-height:220px;object-fit:cover;border-radius:10px;background:#eee;" />

                        <div class="mt-2">
                            <label class="form-label">Upload New Banner / Replace Existing Banner</label>
                            <input type="file" name="banner" id="bannerInput"
                                accept="image/png,image/jpeg,image/jpg"
                                class="form-control @error('banner') is-invalid @enderror">
                            <div class="form-text">Only JPG, JPEG, PNG. Max 10MB.</div>
                            @error('banner') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email Id</label>
                        <input type="email" name="company_emailid"
                            value="{{ old('company_emailid', $user->usercompany->email ?? '') }}"
                            class="form-control @error('company_emailid') is-invalid @enderror">
                        @error('company_emailid') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="company_phone"
                            value="{{ old('company_phone', $user->usercompany->phone ?? '') }}"
                            class="form-control @error('company_phone') is-invalid @enderror">
                        @error('company_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Address</label>
                        <input type="text" name="company_address"
                            value="{{ old('company_address', $user->usercompany->booth ?? '') }}"
                            class="form-control @error('company_address') is-invalid @enderror">
                        @error('company_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Website</label>
                        <input type="text" name="company_website2"
                            placeholder="https://example.com"
                            value="{{ old('company_website2', $user->usercompany->website ?? '') }}"
                            class="form-control @error('company_website2') is-invalid @enderror">
                        @error('company_website2') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Social Links --}}
                    <div class="col-md-6">
                        <label class="form-label">LinkedIn</label>
                        <input type="text" name="linkedin"
                            value="{{ old('linkedin', $companySocialMap['linkedin'] ?? '') }}"
                            class="form-control">
                        @error('linkedin') <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Facebook Link</label>
                        <input type="text" name="facebook"
                            value="{{ old('facebook', $companySocialMap['facebook'] ?? '') }}"
                            class="form-control">
                        @error('facebook') <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Instagram Link</label>
                        <input type="text" name="instagram"
                            value="{{ old('instagram', $companySocialMap['instagram'] ?? '') }}"
                            class="form-control">
                            @error('instagram') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Twitter Link</label>
                        <input type="text" name="twitter"
                            value="{{ old('twitter', $companySocialMap['twitter'] ?? '') }}"
                            class="form-control">
                            @error('twitter') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">About</label>
                        <textarea name="company_about" rows="5"
                            class="form-control @error('company_about') is-invalid @enderror">{{ old('company_about', $user->usercompany->description ?? '') }}</textarea>
                        @error('company_about') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Upload Files --}}
                    <div class="col-md-12">
                        <label class="form-label">Upload New File</label>
                        <input type="file" name="company_files[]" multiple class="form-control">
                        <div class="form-text">Max 10MB each file.</div>
                        @error('company_files.*') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                    </div>

                    {{-- Existing Files --}}
                    <div class="col-md-12">
                        <h6 class="mt-3 mb-2">Uploaded Files</h6>

                        @if(!empty($companyFiles))
                        <ul class="list-group">
                            @foreach($companyFiles as $f)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $f['name'] ?? 'File' }}</strong>
                                    @if(!empty($f['url']))
                                    <div><a class="small" href="{{ $f['url'] }}" target="_blank">View</a></div>
                                    @endif
                                </div>

                                <button type="button"
                                    class="btn btn-danger btn-sm"
                                    onclick="deleteCompanyFile('{{ $f['fileID'] ?? '' }}', this)">
                                    Delete
                                </button>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <div class="text-muted">No files uploaded yet.</div>
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">Save Company Details</button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>

<style>
    /* Tag boxes */
    .tag-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .tag-check {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .tag-box {
        border: 1px solid #d0d5dd;
        border-radius: 10px;
        padding: 10px 14px;
        cursor: pointer;
        user-select: none;
        background: #fff;
        font-size: 14px;
        line-height: 1;
        transition: all .15s ease-in-out;
    }

    .tag-check:checked+.tag-box {
        border-color: #0d6efd;
        background: rgba(13, 110, 253, .08);
        box-shadow: 0 0 0 2px rgba(13, 110, 253, .15);
    }
</style>

<script>
    // Banner preview
    document.getElementById('bannerInput')?.addEventListener('change', (e) => {
        const file = e.target.files?.[0];
        if (!file) return;
        document.getElementById('bannerPreview').src = URL.createObjectURL(file);
    });

    async function deleteCompanyFile(fileId, btn) {
        if (!fileId) return alert('File id missing');
        if (!confirm('Are you sure you want to delete this file?')) return;

        btn.disabled = true;

        const payload = {
            fileId: fileId,
            type: "exhibitor",
            company_id: "{{ $user->access_exhibitor_ids }}" // or first id if comma separated
        };

        try {
            const res = await fetch("{{ route('user.company.file.delete') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (!res.ok || data?.success === false) {
                btn.disabled = false;
                return alert(data?.message || 'Failed to delete file');
            }

            btn.closest('li')?.remove();
        } catch (e) {
            console.error(e);
            btn.disabled = false;
            alert('Something went wrong');
        }
    }
</script>
@endsection

@push('styles')

@endpush

@push('scripts')

@endpush