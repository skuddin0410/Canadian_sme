@php 
    $src = asset('storage/' . $item->file_path); 
    $filename = $item->file_name;
@endphp

<div class="card gallery-card shadow-sm border-0 h-100 position-relative overflow-hidden {{ isset($is_review) && $is_review ? 'border-primary' : '' }}">
    {{-- Delete Button (Available for uploader or Superadmin) --}}
    @if(isSuperAdmin() || $item->added_by == Auth::id())
    <form action="{{ route('event-guides.deleteGalleryImage') }}" method="POST"
          class="position-absolute top-0 end-0 m-2 z-3">
        @csrf
        @method('DELETE')
        <input type="hidden" name="id" value="{{ $item->id }}">
        <button type="submit"
                class="btn btn-sm btn-danger rounded-circle d-flex align-items-center justify-content-center shadow"
                title="Delete file" aria-label="Delete file"
                style="width:26px; height:26px;">
            <i class="bi bi-x-lg" style="font-size:10px;"></i>
        </button>
    </form>
    @endif

    {{-- Thumbnail / Icon --}}
    <div class="ratio ratio-4x3">
        @if($item->file_type === 'image')
            <img src="{{ $src }}" class="gallery-thumb" alt="{{ $filename }}">
        @elseif($item->file_type === 'video')
            <div class="file-preview-placeholder">
                <i class="bi bi-play-circle text-danger"></i>
            </div>
        @else
            <div class="file-preview-placeholder">
                <i class="{{ getFileIcon($item->file_path) }}"></i>
            </div>
        @endif
    </div>

    {{-- Footer --}}
    <div class="card-body p-2 d-flex flex-column h-100">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <small class="text-truncate pe-1" title="{{ $filename }}" style="max-width: 120px;">{{ $filename }}</small>
            <div class="d-flex gap-1">
                @if($item->file_type === 'image')
                    <button type="button" class="btn btn-xs btn-outline-primary p-1 leading-none" data-bs-toggle="modal" data-bs-target="#previewModal" data-src="{{ $src }}">
                        <i class="bi bi-arrows-fullscreen" style="font-size: 10px;"></i>
                    </button>
                @else
                    <a href="{{ $src }}" target="_blank" class="btn btn-xs btn-outline-primary p-1 leading-none">
                        <i class="bi bi-box-arrow-up-right" style="font-size: 10px;"></i>
                    </a>
                @endif
            </div>
        </div>
        
        <div class="mt-auto pt-1 border-top">
            @if($item->event)
            <small class="text-muted d-block text-truncate mb-1" title="{{ $item->event->title }}" style="font-size: 9px;">
                <i class="bi bi-calendar-event"></i> {{ $item->event->title }}
            </small>
            @endif
            <div class="d-flex justify-content-between align-items-center mb-1">
                <small class="text-muted" style="font-size: 9px;">
                    <i class="bi bi-person"></i> {{ $item->user->email ?? 'Admin' }}
                </small>
                @if($item->is_approved)
                    <span class="badge bg-label-success" style="font-size: 8px; padding: 0.2rem 0.4rem;">Approved</span>
                @else
                    <span class="badge bg-label-warning" style="font-size: 8px; padding: 0.2rem 0.4rem;">Pending</span>
                @endif
            </div>

            {{-- Approval UI for Superadmin --}}
            @if(isSuperAdmin() && !$item->is_approved && isset($is_review) && $is_review)
                <form action="{{ route('event-guides.approveGalleryItem') }}" method="POST" class="mt-1">
                    @csrf
                    <input type="hidden" name="id" value="{{ $item->id }}">
                    <button type="submit" class="btn btn-sm btn-success w-100 py-1" style="font-size: 10px;">
                        <i class="bi bi-check-lg"></i> Approve Now
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

<style>
    .btn-xs { padding: 0.25rem 0.4rem; font-size: 0.75rem; line-height: 1; border-radius: 0.2rem; }
    .bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
    .bg-label-warning { background-color: #fff2d6 !important; color: #ffab00 !important; }
</style>
