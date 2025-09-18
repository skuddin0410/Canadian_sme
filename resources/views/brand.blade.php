@extends('layouts.admin')

@section('title')
    Admin | Banners
@endsection

@section('content')
<form id="brandForm" action="{{ route('brand') }}" method="POST" enctype="multipart/form-data" class="container py-4">
    @csrf

    <div class="row g-4">
        @php
            $logo = getKeyValue('logo'); 
            $hasLogoImage = !empty($logo->photo) && !empty($logo->photo->file_path);
            $imgLogoSrc = $hasLogoImage ? (Str::startsWith($logo->photo->file_path, ['http://','https://'])
                        ? $logo->photo->file_path
                        : Storage::url($logo->photo->file_path)) : '';

            $brand = getKeyValue('cover'); 
            $hasBrandImage = !empty($brand->photo) && !empty($brand->photo->file_path);
            $imgBrandSrc = $hasBrandImage ? (Str::startsWith($brand->photo->file_path, ['http://','https://'])
                        ? $brand->photo->file_path
                        : Storage::url($brand->photo->file_path)) : '';
        @endphp

        <div class="col-lg-8">
            <!-- Event Logo -->
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">Event Logo</div>
                <div class="card-body">
                    <div class="dz" id="dz-logo" data-input="event_logo" style="--dz-height:220px;">
                        <div class="dz-placeholder text-center {{ $hasLogoImage ? 'd-none' : '' }}">
                            <div class="mb-2"><i class="bi bi-cloud-arrow-up" style="font-size:2rem;"></i></div>
                            <div class="small text-muted mb-2">Drag & drop your logo here</div>
                            <button type="button" class="btn btn-sm btn-outline-primary dz-browse">Browse</button>
                        </div>
                        <img class="dz-image {{ $hasLogoImage ? '' : 'd-none' }} rounded" 
                             src="{{ $imgLogoSrc }}" 
                             style="max-height:180px; max-width:100%; object-fit:contain;" />
                        <button type="button" class="btn btn-sm btn-danger dz-remove {{ $hasLogoImage ? '' : 'd-none' }}">
                            <i class="bi bi-x-lg"></i> Remove
                        </button>
                        <input type="file" name="event_logo" class="dz-input d-none" accept="image/*">
                    </div>
                    <div class="form-text mt-2">PNG recommended. Transparent background preferred.</div>
                </div>
            </div>

            <!-- Brand Cover -->
            <div class="card shadow-sm mt-4">
                <div class="card-header fw-semibold">Brand Cover</div>
                <div class="card-body">
                    <div class="dz" id="dz-cover" data-input="brand_cover" style="--dz-height:260px;">
                        <div class="dz-placeholder text-center {{ $hasBrandImage ? 'd-none' : '' }}">
                            <div class="mb-2"><i class="bi bi-cloud-arrow-up" style="font-size:2rem;"></i></div>
                            <div class="small text-muted mb-2">Drag & drop a wide cover image</div>
                            <button type="button" class="btn btn-sm btn-outline-primary dz-browse">Browse</button>
                        </div>
                        <img class="dz-image {{ $hasBrandImage ? '' : 'd-none' }} rounded" 
                             src="{{ $imgBrandSrc }}" 
                             style="max-height:180px; max-width:100%; object-fit:contain;" />
                        <button type="button" class="btn btn-sm btn-danger dz-remove {{ $hasBrandImage ? '' : 'd-none' }}">
                            <i class="bi bi-x-lg"></i> Remove
                        </button>
                        <input type="file" name="brand_cover" class="dz-input d-none" accept="image/*">
                    </div>
                    <div class="form-text mt-2">Suggested size: 1600×600 or similar aspect ratio.</div>
                </div>
            </div>
        </div>

        <!-- Theme Color -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">Theme Color</div>
                <div class="card-body">
                    <label for="themeColor" class="form-label">Choose your brand/theme color</label>
                    <div class="d-flex align-items-center gap-3">
                        <input type="color" id="themeColor" name="theme_color" class="form-control form-control-color"
                               value="{{ getKeyValue('color')->value ?? '#0D6EFD' }}" title="Pick a color">
                        <span id="themeColorHex" class="fw-semibold">{{ getKeyValue('color')->value ?? '#0D6EFD' }}</span>
                        <span id="themeSwatch" class="rounded-circle border" style="width:24px;height:24px;background:{{ getKeyValue('color')->value ?? '#0D6EFD' }}"></span>
                    </div>
                    <div class="form-text mt-2">This color accents the UI and outlines the dropzones for a quick preview.</div>
                </div>
                <div class="card-footer text-end">
                    <input type="hidden" name="mode" value="save">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.dz { position: relative; min-height: var(--dz-height,220px); border:2px dashed var(--bs-border-color); border-radius:.75rem; background: var(--bs-body-bg); display:flex; align-items:center; justify-content:center; overflow:hidden; padding:1rem; cursor:pointer; transition:border-color .2s, box-shadow .2s;}
.dz:hover { box-shadow:0 .125rem .5rem rgba(0,0,0,.08);}
.dz.dz-dragover { border-color: var(--bs-primary); background: rgba(13,110,253,.03);}
.dz .dz-placeholder { pointer-events: none;}
.dz .dz-browse { pointer-events: all;}
.dz .dz-image { max-width:100%; max-height: calc(var(--dz-height,220px)-16px); object-fit:contain; border-radius:.5rem;}
.dz .dz-remove { position:absolute; top:.5rem; right:.5rem; }
</style>

<script>
function showAlert(message, type='success'){
    const container = document.createElement('div');
    container.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>`;
    document.querySelector('.container').prepend(container);
}

function makeDropzone(root){
    const input = root.querySelector('.dz-input');
    const placeholder = root.querySelector('.dz-placeholder');
    const browseBtn = root.querySelector('.dz-browse');
    const img = root.querySelector('.dz-image');
    const removeBtn = root.querySelector('.dz-remove');

    const showPreview = (fileOrUrl) => {
        if(typeof fileOrUrl === 'string'){
            img.src = fileOrUrl; img.classList.remove('d-none'); placeholder.classList.add('d-none'); removeBtn.classList.remove('d-none'); return;
        }
        const reader = new FileReader();
        reader.onload = e => { img.src=e.target.result; img.classList.remove('d-none'); placeholder.classList.add('d-none'); removeBtn.classList.remove('d-none'); };
        reader.readAsDataURL(fileOrUrl);
    }

    const clearPreview = () => { img.src=''; img.classList.add('d-none'); placeholder.classList.remove('d-none'); removeBtn.classList.add('d-none'); input.value=''; }

    browseBtn?.addEventListener('click', e=>{ e.stopPropagation(); input.click(); });
    root.addEventListener('click', e=>{ if(e.target.closest('.dz-remove')) return; input.click(); });
    input.addEventListener('change', e=>{ if(e.target.files[0]) showPreview(e.target.files[0]); });

    ['dragenter','dragover'].forEach(ev=>root.addEventListener(ev, e=>{ e.preventDefault(); root.classList.add('dz-dragover'); }));
    ['dragleave','drop'].forEach(ev=>root.addEventListener(ev, e=>{ e.preventDefault(); root.classList.remove('dz-dragover'); }));
    root.addEventListener('drop', e=>{ const file=e.dataTransfer.files[0]; if(file){ const dt=new DataTransfer(); dt.items.add(file); input.files=dt.files; showPreview(file); } });

    // Remove button
    removeBtn.addEventListener('click', e=>{
        e.stopPropagation();
        const key = root.dataset.input;
        $.ajax({
            url: "{{ route('brand.deleteMedia') }}",
            type: 'POST',
            headers: { 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
            data: { key },
            success: res => { clearPreview(); showAlert(res.message,'success'); },
            error: xhr => { showAlert('Error deleting media: '+xhr.responseJSON?.error,'danger'); }
        });
    });

    return { showPreview, clearPreview, input };
}

// Initialize dropzones
makeDropzone(document.getElementById('dz-logo'));
makeDropzone(document.getElementById('dz-cover'));

// Theme color live preview
const colorInput=document.getElementById('themeColor');
const colorHex=document.getElementById('themeColorHex');
const swatch=document.getElementById('themeSwatch');
const applyAccent=hex=>{
    colorHex.textContent=hex.toUpperCase();
    swatch.style.background=hex;
    document.querySelectorAll('.dz').forEach(el=>{ el.style.borderColor=hex; el.style.boxShadow=`0 0 0 .1rem ${hex}20`; });
};
colorInput.addEventListener('input', e=>applyAccent(e.target.value));
applyAccent(colorInput.value);

// AJAX form submission for upload
$('#brandForm').submit(function(e){
    e.preventDefault();
    let formData = new FormData(this);
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: res=>{ showAlert('Brand media uploaded successfully!','success'); },
        error: xhr=>{
            let msg='Error uploading media';
            if(xhr.responseJSON?.errors){ msg = Object.values(xhr.responseJSON.errors).flat().join('<br>'); }
            showAlert(msg,'danger');
        }
    });
});
</script>
@endsection
