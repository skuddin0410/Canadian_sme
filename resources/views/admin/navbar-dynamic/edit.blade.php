@extends('layouts.admin')

@section('title')
Admin | Edit Navbar Item
@endsection

@section('content')
@php
    $previewHeaderHtml = view('partials_new.header', ['dynamicNavs' => $dynamicNavs ?? []])->render();
    $previewFooterHtml = view('partials_new.footer')->render();
@endphp
<div class="container-xxl container-p-y">

    <h4 class="mb-4">Edit Page Builder</h4>

    <div class="row">
        <div class="col-12">
            <div class="builder-col">
                <form action="{{ route('admin.navbar-dynamic.update', $navbar->id) }}" method="POST" id="main-form">
                    @csrf

                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" id="title" name="title" class="form-control" value="{{ $navbar->title }}">
                    </div>

                    <input type="hidden" name="slug" id="slug" value="{{ $navbar->slug }}">
                    <input type="text" id="slug_preview" class="form-control mb-3" value="{{ $navbar->slug }}" readonly>

                    <div class="mb-3">
                        <select class="form-select" id="category_select" name="category">
                            <option value="">No Category</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ $navbar->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                            <option value="NEW_CATEGORY">+ Add New</option>
                        </select>
                        <input type="text" id="category_new" class="form-control mt-2 d-none" placeholder="New category">
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="active" {{ $navbar->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $navbar->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Order</label>
                        <input type="number" name="order_by" class="form-control" value="{{ $navbar->order_by }}" required>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-4 mb-3">
                        <h5 class="mb-0">Page Builder</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <!-- <button type="button" class="btn btn-sm btn-primary"   onclick="addSection('hero')">+ Hero</button> -->
                            <button type="button" class="btn btn-sm btn-secondary" onclick="addSection('text')">+ Text</button>
                            <button type="button" class="btn btn-sm btn-success"   onclick="addSection('image')">+ Image</button>
                            <button type="button" class="btn btn-sm btn-warning"   onclick="addSection('cards')">+ Section</button>
                        </div>
                    </div>

                    <div id="builder" class="border p-3 rounded"></div>
                    <input type="hidden" name="content" id="content_json">
                    <button class="btn btn-primary mt-4 w-100">💾 Update Page</button>
                </form>
            </div>
        </div>
    </div>

    <button class="floating-preview-btn" onclick="togglePreviewModal()">
        <i class="fas fa-eye"></i>
    </button>

    <div class="preview-modal" id="preview-modal" style="display:none;">
        <div class="preview-modal-header">
            <span class="preview-panel-title">
                <span class="preview-dot"></span>
                Live Preview
                <span class="preview-dnd-tip">
                    <i class="fas fa-arrows-alt" style="font-size:10px;margin-left:4px;color:#22d3ee;"></i>
                    Drag sections &amp; cards to reorder
                </span>
            </span>
            <button class="btn btn-sm btn-link text-white" onclick="closePreviewModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="preview-modal-body">
            <div class="preview-frame-wrap" id="preview-frame-wrap">
                <iframe id="preview-modal-frame" src="about:blank" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<style>
.floating-preview-btn{position:fixed;bottom:24px;right:24px;width:52px;height:52px;border-radius:50%;background:#4361ee;color:#fff;border:none;box-shadow:0 4px 18px rgba(67,97,238,.45);font-size:1.3rem;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:1000;transition:transform .2s,box-shadow .2s;}
.floating-preview-btn:hover{transform:scale(1.12);}
.preview-modal{position:fixed;top:0;left:0;width:100%;height:100%;background:#13131f;z-index:9999;flex-direction:column;}
.preview-modal-header{display:flex;align-items:center;justify-content:space-between;padding:10px 18px;background:#1e1e2e;border-bottom:1px solid #2a2a3e;flex-shrink:0;height:52px;}
.preview-modal-body{flex:1;display:flex;align-items:flex-start;justify-content:center;background:#1a1a2a;padding:20px;overflow:auto;}
.preview-frame-wrap{width:100%;height:calc(100vh - 92px);display:flex;transition:max-width .3s ease;}
#preview-modal-frame{width:100%;height:100%;border:none;border-radius:8px;box-shadow:0 8px 48px rgba(0,0,0,.5);background:#fff;}
.preview-panel-title{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#e2e8f0;}
.preview-dnd-tip{font-size:11px;font-weight:400;color:#64748b;}
.preview-dot{width:8px;height:8px;border-radius:50%;background:#22d3ee;box-shadow:0 0 6px #22d3ee;animation:pulse-dot 2s infinite;}
@keyframes pulse-dot{0%,100%{opacity:1}50%{opacity:.4}}
.builder-section-card{border:1px solid #d0d5dd;border-radius:10px;padding:16px;margin-bottom:16px;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.06);}
.builder-section-card .section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;}
.builder-section-card .section-badge{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;padding:3px 10px;border-radius:20px;}
.drag-handle{cursor:grab;color:#adb5bd;font-size:18px;margin-right:8px;user-select:none;}
.drag-handle:active{cursor:grabbing;}
.color-swatch{width:48px;height:34px;padding:2px;border-radius:6px;border:1px solid #dee2e6;cursor:pointer;}
.controls-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-bottom:14px;}
.ctrl-label{font-size:12px;font-weight:600;color:#6c757d;display:block;margin-bottom:4px;}
.ctrl-select{padding:6px 10px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;width:100%;background:#fff;}
.cards-builder-list{display:flex;flex-direction:column;gap:12px;margin-top:12px;}
.card-item-builder{border:1px dashed #ced4da;border-radius:8px;padding:12px;background:#f8f9fa;}
.card-item-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;font-weight:600;font-size:13px;color:#495057;cursor:grab;user-select:none;}
.card-item-header:active{cursor:grabbing;}
.card-fields{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.card-fields .full-width{grid-column:1/-1;}
.card-fields label{font-size:12px;font-weight:600;color:#6c757d;display:block;margin-bottom:4px;}
.card-fields input[type="text"],.card-fields input[type="url"],.card-fields select,.card-fields textarea{width:100%;padding:6px 10px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;background:#fff;box-sizing:border-box;}
.card-fields textarea{min-height:70px;resize:vertical;}
.card-img-preview{width:80px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #dee2e6;margin-top:6px;display:block;}
.btn-add-card{border:2px dashed #4361ee;color:#4361ee;background:transparent;border-radius:8px;padding:8px 16px;font-size:13px;font-weight:600;cursor:pointer;width:100%;margin-top:8px;transition:all .2s;}
.btn-add-card:hover{background:#4361ee;color:#fff;}
.sortable-ghost{opacity:.35;background:#e8edff !important;border:2px dashed #4361ee !important;}
.sortable-chosen{box-shadow:0 8px 24px rgba(67,97,238,.22);}
</style>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
<script src="https://cdn.tiny.cloud/1/g5uikhrm5sqmr752tl583kxgkjacajfjzfjhxsuuft3uo7ex/tinymce/6/tinymce.min.js"></script>



<script>
let sections = @json(
    is_array(json_decode($navbar->content, true))
        ? json_decode($navbar->content, true)
        : []
);

if (!Array.isArray(sections)) sections = [];
let previewDebounce = null;
const previewHeaderHtml = @json($previewHeaderHtml);
const previewFooterHtml = @json($previewFooterHtml);
const SECTION_WIDTH_OPTIONS = ['25', '30', '40', '50', '60', '70', '75', '100', 'auto'];
const CARD_WIDTH_OPTIONS = ['25', '30', '40', '50', '60', '70', '75', '100', 'auto'];
const HEIGHT_OPTIONS = ['auto', ...Array.from({ length: 20 }, (_, index) => `${(index + 1) * 50}px`)];

function escHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function escPreview(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function normalizeWidthValue(value, fallback = '100') {
    if (value === 'auto') return 'auto';
    let normalized = parseFloat(value);
    if (Number.isFinite(normalized)) return String(normalized);
    return fallback;
}

function normalizeHeightValue(value, fallback = 'auto') {
    if (!value || value === 'auto') return 'auto';
    if (/^\d+px$/.test(String(value))) return String(value);
    let normalized = parseInt(value, 10);
    return Number.isFinite(normalized) ? `${normalized}px` : fallback;
}

function formatWidthStyle(value, fallback = '100') {
    let normalized = normalizeWidthValue(value, fallback);
    return normalized === 'auto' ? 'auto' : `${normalized}%`;
}

function renderWidthOptions(selectedValue, options = SECTION_WIDTH_OPTIONS) {
    let normalized = normalizeWidthValue(selectedValue);
    return options.map(option => {
        let label = option === 'auto' ? 'Auto' : `${option}%`;
        return `<option value="${option}" ${normalized === option ? 'selected' : ''}>${label}</option>`;
    }).join('');
}

function renderHeightOptions(selectedValue) {
    let normalized = normalizeHeightValue(selectedValue);
    return HEIGHT_OPTIONS.map(option => {
        let label = option === 'auto' ? 'Auto' : option;
        return `<option value="${option}" ${normalized === option ? 'selected' : ''}>${label}</option>`;
    }).join('');
}

function normalizeSections() {
    sections = sections.map(section => {
        section.data = section.data || {};

        if (section.type === 'hero') {
            section.data.bg = section.data.bg || '#f8f9fa';
            section.data.textColor = section.data.textColor || '#1a1a2e';
            section.data.subtitleColor = section.data.subtitleColor || '#6b7280';
            section.data.alignment = section.data.alignment || 'center';
            section.data.height = normalizeHeightValue(section.data.height || 'auto');
            section.data.btnText = section.data.btnText || '';
            section.data.btnLink = section.data.btnLink || '';
            section.data.btnColor = section.data.btnColor || '#4361ee';
            section.data.btnTextColor = section.data.btnTextColor || '#ffffff';
            section.data.title = section.data.title || '';
            section.data.subtitle = section.data.subtitle || '';
            section.data.sectionWidth = normalizeWidthValue(section.data.sectionWidth || '100');
        }

        if (section.type === 'text') {
            section.data.bg = section.data.bg || '#ffffff';
            section.data.textColor = section.data.textColor || '#374151';
            section.data.alignment = section.data.alignment || 'left';
            section.data.height = normalizeHeightValue(section.data.height || 'auto');
            section.data.content = section.data.content || '';
            section.data.sectionWidth = normalizeWidthValue(section.data.sectionWidth || '100');
        }

        if (section.type === 'image') {
            section.data.bg = section.data.bg || '#ffffff';
            section.data.image = section.data.image || '';
            section.data.caption = section.data.caption || '';
            section.data.captionColor = section.data.captionColor || '#6b7280';
            section.data.alignment = section.data.alignment || 'center';
            section.data.height = normalizeHeightValue(section.data.height || 'auto');
            section.data.sectionWidth = normalizeWidthValue(section.data.sectionWidth || '100');
        }

        if (section.type === 'cards') {
            section.data.bg = section.data.bg || '#f8f9fa';
            section.data.sectionTitle = section.data.sectionTitle || '';
            section.data.sectionTitleColor = section.data.sectionTitleColor || '#1a1a2e';
            section.data.columns = parseInt(section.data.columns || 3, 10);
            section.data.sectionWidth = normalizeWidthValue(section.data.sectionWidth || '100');
            section.data.alignment = section.data.alignment || 'left';
            section.data.height = normalizeHeightValue(section.data.height || 'auto');
            section.data.cards = Array.isArray(section.data.cards) ? section.data.cards : [];

            section.data.cards = section.data.cards.map(card => ({
                id: card.id || (Date.now() + Math.floor(Math.random() * 1000)),
                type: card.type || ((card.image && !card.title && !card.description && !card.btnText) ? 'image' : 'card'),
                image: card.image || '',
                title: card.title || '',
                titleColor: card.titleColor || '#1a1a2e',
                description: card.description || '',
                descColor: card.descColor || '#6b7280',
                caption: card.caption || '',
                captionColor: card.captionColor || '#6b7280',
                btnText: card.btnText || '',
                btnLink: card.btnLink || '',
                btnColor: card.btnColor || '#4361ee',
                btnTextColor: card.btnTextColor || '#ffffff',
                cardBg: card.cardBg || '#ffffff',
                width: normalizeWidthValue(card.width || '33.333', '33.333'),
                height: normalizeHeightValue(card.height || 'auto'),
                alignment: card.alignment || 'left'
            }));
        }

        return section;
    });
}

normalizeSections();

window.addEventListener('message', function(e) {
    if (!e.data || !e.data.type) return;

    if (e.data.type === 'SECTION_REORDER') {
        let reordered = [];
        (e.data.order || []).forEach(id => {
            let found = sections.find(s => String(s.id) === String(id));
            if (found) reordered.push(found);
        });

        sections.forEach(s => {
            if (!reordered.find(r => String(r.id) === String(s.id))) reordered.push(s);
        });

        sections = reordered;
        saveJSON();
        reorderBuilderDOM();
        return;
    }

    if (e.data.type === 'CARD_REORDER') {
        let fromSectionId = parseInt(e.data.fromSectionId ?? e.data.sectionId, 10);
        let toSectionId = parseInt(e.data.toSectionId ?? e.data.sectionId, 10);

        let fromSection = sections.find(s => s.id === fromSectionId && s.type === 'cards');
        let toSection = sections.find(s => s.id === toSectionId && s.type === 'cards');

        if (!toSection) return;

        let orderedIds = (e.data.order || []).map(id => String(id));
        let movedItems = [];

        if (fromSection && fromSection.id !== toSection.id) {
            movedItems = fromSection.data.cards.filter(card => orderedIds.includes(String(card.id)));
            fromSection.data.cards = fromSection.data.cards.filter(card => !orderedIds.includes(String(card.id)));
        }

        let available = fromSection && fromSection.id !== toSection.id
            ? [...toSection.data.cards, ...movedItems]
            : [...toSection.data.cards];

        let reordered = [];
        orderedIds.forEach(id => {
            let found = available.find(card => String(card.id) === id);
            if (found) reordered.push(found);
        });

        available.forEach(card => {
            if (!reordered.find(r => String(r.id) === String(card.id))) reordered.push(card);
        });

        toSection.data.cards = reordered;
        saveJSON();

        if (fromSection && fromSection.id !== toSection.id) {
            renderCardsEditor(fromSection.id);
        }

        renderCardsEditor(toSection.id);
        schedulePreviewSync();
    }
});

function reorderBuilderDOM() {
    let builder = document.getElementById('builder');
    sections.forEach(sec => {
        let el = builder.querySelector('[data-id="' + sec.id + '"]');
        if (el) builder.appendChild(el);
    });
}

document.getElementById('title').addEventListener('input', function () {
    let slug = this.value.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
    document.getElementById('slug_preview').value = slug;
    document.getElementById('slug').value = slug;
});

document.getElementById('category_select').addEventListener('change', function () {
    if (this.value === 'NEW_CATEGORY') {
        this.classList.add('d-none');
        this.removeAttribute('name');
        let input = document.getElementById('category_new');
        input.classList.remove('d-none');
        input.setAttribute('name', 'category');
    }
});

function getDefaultCard(type) {
    if (type === 'image') {
        return {
            id: Date.now(),
            type: 'image',
            image: '',
            caption: '',
            captionColor: '#6b7280',
            cardBg: '#ffffff',
            width: '33.333',
            height: 'auto',
            alignment: 'center'
        };
    }

    return {
        id: Date.now(),
        type: 'card',
        image: '',
        title: '',
        titleColor: '#1a1a2e',
        description: '',
        descColor: '#6b7280',
        btnText: '',
        btnLink: '',
        btnColor: '#4361ee',
        btnTextColor: '#ffffff',
        cardBg: '#ffffff',
        width: '33.333',
        height: 'auto',
        alignment: 'left'
    };
}

function addSection(type) {
    let defaults = { bg: '#ffffff', textColor: '#000000', alignment: 'center', height: 'auto' };

    if (type === 'hero') {
        Object.assign(defaults, {
            title: '',
            subtitle: '',
            subtitleColor: '#6b7280',
            btnText: '',
            btnLink: '',
            btnColor: '#4361ee',
            btnTextColor: '#ffffff',
            bg: '#f8f9fa',
            sectionWidth: '100'
        });
    }

    if (type === 'text') {
        Object.assign(defaults, {
            content: '',
            alignment: 'left',
            textColor: '#374151',
            sectionWidth: '100'
        });
    }

    if (type === 'image') {
        Object.assign(defaults, {
            image: '',
            caption: '',
            captionColor: '#6b7280',
            sectionWidth: '100'
        });
    }

    if (type === 'cards') {
        Object.assign(defaults, {
            bg: '#f8f9fa',
            sectionTitle: '',
            sectionTitleColor: '#1a1a2e',
            columns: 3,
            sectionWidth: '100',
            cards: [],
            alignment: 'left'
        });
    }

    sections.push({
        id: Date.now(),
        type,
        data: defaults
    });

    render();
}

function removeSection(id) {
    if (!confirm('Delete this section?')) return;
    sections = sections.filter(s => s.id !== id);
    render();
}

function updateSection(id, key, value) {
    let sec = sections.find(s => s.id === id);
    if (!sec) return;
    if (key === 'sectionWidth') value = normalizeWidthValue(value);
    if (key === 'height') value = normalizeHeightValue(value);
    sec.data[key] = value;
    saveJSON();
    schedulePreviewSync();
}

function addCard(sectionId, type = 'card') {
    let sec = sections.find(s => s.id === sectionId && s.type === 'cards');
    if (!sec) return;
    if (!Array.isArray(sec.data.cards)) sec.data.cards = [];
    sec.data.cards.push(getDefaultCard(type));
    saveJSON();
    renderCardsEditor(sectionId);
    schedulePreviewSync();
}

function removeCard(sectionId, cardId) {
    let sec = sections.find(s => s.id === sectionId && s.type === 'cards');
    if (!sec) return;
    sec.data.cards = sec.data.cards.filter(c => c.id !== cardId);
    saveJSON();
    renderCardsEditor(sectionId);
    schedulePreviewSync();
}

function updateCard(sectionId, cardId, key, value) {
    let sec = sections.find(s => s.id === sectionId && s.type === 'cards');
    if (!sec) return;
    let card = sec.data.cards.find(c => c.id === cardId);
    if (!card) return;
    if (key === 'width') value = normalizeWidthValue(value, '33.333');
    if (key === 'height') value = normalizeHeightValue(value);
    card[key] = value;
    saveJSON();
    schedulePreviewSync();
}

function uploadCardImage(input, sectionId, cardId) {
    let file = input.files[0];
    if (!file) return;

    let fd = new FormData();
    fd.append('image', file);

    fetch('/admin/upload-image', {
        method: 'POST',
        body: fd,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(async r => {
        let t = await r.text();
        try { return JSON.parse(t); }
        catch (e) { throw new Error('Invalid JSON'); }
    })
    .then(data => {
        if (!data.success) {
            alert('Upload failed: ' + (data.error || 'Unknown'));
            return;
        }

        updateCard(sectionId, cardId, 'image', data.url);

        let img = document.getElementById('card_img_' + cardId);
        if (img) {
            img.src = data.url;
            img.style.display = '';
        }

        let txt = document.getElementById('card_img_txt_' + cardId);
        if (txt) txt.textContent = 'Image uploaded ✓';
    })
    .catch(err => {
        console.error(err);
        alert('Upload failed.');
    });
}

function uploadSectionImage(input, id) {
    let file = input.files[0];
    if (!file) return;

    let fd = new FormData();
    fd.append('image', file);

    fetch('/admin/upload-image', {
        method: 'POST',
        body: fd,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(async r => {
        let t = await r.text();
        try { return JSON.parse(t); }
        catch (e) { throw new Error('Invalid JSON'); }
    })
    .then(data => {
        if (!data.success) {
            alert('Upload failed: ' + (data.error || 'Unknown'));
            return;
        }

        updateSection(id, 'image', data.url);

        let img = document.getElementById('img_' + id);
        if (img) {
            img.src = data.url;
            img.style.display = '';
        }

        let txt = document.getElementById('img_text_' + id);
        if (txt) txt.textContent = 'Image uploaded ✓';
    })
    .catch(err => {
        console.error(err);
        alert('Upload failed.');
    });
}

function renderCardsEditor(sectionId) {
    let sec = sections.find(s => s.id === sectionId && s.type === 'cards');
    if (!sec) return;

    let container = document.getElementById('cards_list_' + sectionId);
    if (!container) return;

    document.querySelectorAll(`textarea[id^="card_desc_${sectionId}_"]`).forEach(el => {
        const editor = tinymce.get(el.id);
        if (editor) editor.remove();
    });

    container.innerHTML = '';

    (sec.data.cards || []).forEach(card => {
        let isImage = card.type === 'image';
        let div = document.createElement('div');
        div.className = 'card-item-builder';
        div.setAttribute('data-card-id', String(card.id));

        div.innerHTML = `
            <div class="card-item-header">
                <span>⠿ ${isImage ? '🖼 Image' : '🃏 Card'}</span>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeCard(${sectionId},${card.id})">✕ Remove</button>
            </div>
            <div class="card-fields">
                ${isImage ? `
                    <div class="full-width">
                        <label>Caption</label>
                        <input type="text" value="${escHtml(card.caption || '')}" oninput="updateCard(${sectionId},${card.id},'caption',this.value)" placeholder="Image caption...">
                    </div>
                    <div>
                        <label>Caption Color</label>
                        <input type="color" class="color-swatch" value="${card.captionColor || '#6b7280'}" oninput="updateCard(${sectionId},${card.id},'captionColor',this.value)">
                    </div>
                ` : `
                    <div class="full-width">
                        <label>Card Title</label>
                        <input type="text" value="${escHtml(card.title || '')}" oninput="updateCard(${sectionId},${card.id},'title',this.value)" placeholder="Card heading...">
                    </div>
                    <div>
                        <label>Title Color</label>
                        <input type="color" class="color-swatch" value="${card.titleColor || '#1a1a2e'}" oninput="updateCard(${sectionId},${card.id},'titleColor',this.value)">
                    </div>
                    <div class="full-width">
                        <label>Description</label>
                        <textarea id="card_desc_${sectionId}_${card.id}" placeholder="Short description...">${escHtml(card.description || '')}</textarea>
                    </div>
                    <div>
                        <label>Description Color</label>
                        <input type="color" class="color-swatch" value="${card.descColor || '#6b7280'}" oninput="updateCard(${sectionId},${card.id},'descColor',this.value)">
                    </div>
                    <div>
                        <label>Button Text</label>
                        <input type="text" value="${escHtml(card.btnText || '')}" oninput="updateCard(${sectionId},${card.id},'btnText',this.value)" placeholder="e.g. Learn More">
                    </div>
                    <div>
                        <label>Button Link</label>
                        <input type="url" value="${escHtml(card.btnLink || '')}" oninput="updateCard(${sectionId},${card.id},'btnLink',this.value)" placeholder="https://...">
                    </div>
                    <div>
                        <label>Button Background</label>
                        <input type="color" class="color-swatch" value="${card.btnColor || '#4361ee'}" oninput="updateCard(${sectionId},${card.id},'btnColor',this.value)">
                    </div>
                    <div>
                        <label>Button Text Color</label>
                        <input type="color" class="color-swatch" value="${card.btnTextColor || '#ffffff'}" oninput="updateCard(${sectionId},${card.id},'btnTextColor',this.value)">
                    </div>
                `}
                <div>
                    <label>Item Background</label>
                    <input type="color" class="color-swatch" value="${card.cardBg || '#ffffff'}" oninput="updateCard(${sectionId},${card.id},'cardBg',this.value)">
                </div>
                <div>
                    <label>Width</label>
                    <select class="ctrl-select" onchange="updateCard(${sectionId},${card.id},'width',this.value)">
                        ${renderWidthOptions(card.width || '33.333', CARD_WIDTH_OPTIONS)}
                    </select>
                </div>
                <div>
                    <label>Height</label>
                    <select class="ctrl-select" onchange="updateCard(${sectionId},${card.id},'height',this.value)">
                        ${renderHeightOptions(card.height || 'auto')}
                    </select>
                </div>
                <div>
                    <label>Alignment</label>
                    <select class="ctrl-select" onchange="updateCard(${sectionId},${card.id},'alignment',this.value)">
                        <option value="left" ${(card.alignment || 'left') === 'left' ? 'selected' : ''}>Left</option>
                        <option value="center" ${card.alignment === 'center' ? 'selected' : ''}>Center</option>
                        <option value="right" ${card.alignment === 'right' ? 'selected' : ''}>Right</option>
                    </select>
                </div>
                <div class="full-width">
                    <label>${isImage ? 'Image' : 'Card Image'} (Optional)</label>
                    <input type="file" accept="image/*" onchange="uploadCardImage(this,${sectionId},${card.id})">
                    <img id="card_img_${card.id}" src="${card.image || ''}" class="card-img-preview" style="${card.image ? '' : 'display:none'}">
                    <small id="card_img_txt_${card.id}" class="text-success">${card.image ? 'Image uploaded ✓' : 'No image selected'}</small>
                </div>
            </div>
        `;

        container.appendChild(div);
    });

    initCardDescriptionEditors(sectionId);

    new Sortable(container, {
        animation: 150,
        handle: '.card-item-header',
        draggable: '.card-item-builder',
        group: { name: 'builder-cards', pull: true, put: true },
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        onEnd: function(evt) {
            let fromSectionId = parseInt(evt.from.id.replace('cards_list_', ''), 10);
            let toSectionId = parseInt(evt.to.id.replace('cards_list_', ''), 10);

            let fromSection = sections.find(s => s.id === fromSectionId && s.type === 'cards');
            let toSection = sections.find(s => s.id === toSectionId && s.type === 'cards');
            if (!toSection) return;

            let orderedIds = Array.from(evt.to.querySelectorAll('.card-item-builder[data-card-id]'))
                .map(el => String(el.getAttribute('data-card-id')));

            let movedItems = [];
            if (fromSection && fromSection.id !== toSection.id) {
                movedItems = fromSection.data.cards.filter(card => orderedIds.includes(String(card.id)));
                fromSection.data.cards = fromSection.data.cards.filter(card => !orderedIds.includes(String(card.id)));
            }

            let available = fromSection && fromSection.id !== toSection.id
                ? [...toSection.data.cards, ...movedItems]
                : [...toSection.data.cards];

            let reordered = [];
            orderedIds.forEach(id => {
                let found = available.find(card => String(card.id) === id);
                if (found) reordered.push(found);
            });

            available.forEach(card => {
                if (!reordered.find(r => String(r.id) === String(card.id))) reordered.push(card);
            });

            toSection.data.cards = reordered;
            saveJSON();

            if (fromSection && fromSection.id !== toSection.id) renderCardsEditor(fromSection.id);
            renderCardsEditor(toSection.id);
            schedulePreviewSync();
        }
    });
}


function buildSectionEditorHTML(sec) {
    let badgeColors = {
        hero: 'background:#cfe2ff;color:#084298',
        text: 'background:#d1ecf1;color:#0c5460',
        image: 'background:#d4edda;color:#155724',
        cards: 'background:#fff3cd;color:#856404'
    };

    let d = sec.data || {};

    let headerHtml = `
        <div class="section-header">
            <div style="display:flex;align-items:center">
                <span class="drag-handle" title="Drag to reorder">⠿</span>
                <span class="section-badge" style="${badgeColors[sec.type] || ''}">${sec.type === 'cards' ? 'SECTION' : sec.type.toUpperCase()}</span>
            </div>
            <button type="button" onclick="removeSection(${sec.id})" class="btn btn-sm btn-danger">✕ Delete</button>
        </div>`;

    if (sec.type === 'hero') {
        return headerHtml + `
            <div class="controls-grid">
                <div><span class="ctrl-label">Section Background</span><input type="color" class="color-swatch" value="${d.bg || '#f8f9fa'}" oninput="updateSection(${sec.id},'bg',this.value)"></div>
                <div><span class="ctrl-label">Title Color</span><input type="color" class="color-swatch" value="${d.textColor || '#1a1a2e'}" oninput="updateSection(${sec.id},'textColor',this.value)"></div>
                <div><span class="ctrl-label">Subtitle Color</span><input type="color" class="color-swatch" value="${d.subtitleColor || '#6b7280'}" oninput="updateSection(${sec.id},'subtitleColor',this.value)"></div>
                <div><span class="ctrl-label">Alignment</span>
                    <select class="ctrl-select" onchange="updateSection(${sec.id},'alignment',this.value)">
                        <option value="left" ${d.alignment === 'left' ? 'selected' : ''}>Left</option>
                        <option value="center" ${(d.alignment === 'center' || !d.alignment) ? 'selected' : ''}>Center</option>
                        <option value="right" ${d.alignment === 'right' ? 'selected' : ''}>Right</option>
                    </select>
                </div>
                <div><span class="ctrl-label">Section Width</span>
                    <select class="ctrl-select" onchange="updateSection(${sec.id},'sectionWidth',this.value)">
                        ${renderWidthOptions(d.sectionWidth || '100')}
                    </select>
                </div>
                <div><span class="ctrl-label">Height</span>
                    <select class="ctrl-select" onchange="updateSection(${sec.id},'height',this.value)">
                        ${renderHeightOptions(d.height || 'auto')}
                    </select>
                </div>
            </div>
            <label class="ctrl-label">Hero Title</label>
            <input class="form-control mt-1 mb-2" value="${escHtml(d.title || '')}" oninput="updateSection(${sec.id},'title',this.value)">
            <label class="ctrl-label">Hero Subtitle</label>
            <input class="form-control mt-1 mb-2" value="${escHtml(d.subtitle || '')}" oninput="updateSection(${sec.id},'subtitle',this.value)">
            <div class="controls-grid mt-2">
                <div><label class="ctrl-label">Button Text</label><input type="text" class="form-control" value="${escHtml(d.btnText || '')}" oninput="updateSection(${sec.id},'btnText',this.value)"></div>
                <div><label class="ctrl-label">Button Link</label><input type="url" class="form-control" value="${escHtml(d.btnLink || '')}" oninput="updateSection(${sec.id},'btnLink',this.value)"></div>
                <div><span class="ctrl-label">Button Background</span><input type="color" class="color-swatch" value="${d.btnColor || '#4361ee'}" oninput="updateSection(${sec.id},'btnColor',this.value)"></div>
                <div><span class="ctrl-label">Button Text Color</span><input type="color" class="color-swatch" value="${d.btnTextColor || '#ffffff'}" oninput="updateSection(${sec.id},'btnTextColor',this.value)"></div>
            </div>`;
    }

    if (sec.type === 'text') {
        return headerHtml + `
            <div class="controls-grid">
                <div><span class="ctrl-label">Section Background</span><input type="color" class="color-swatch" value="${d.bg || '#ffffff'}" oninput="updateSection(${sec.id},'bg',this.value)"></div>
                <div><span class="ctrl-label">Text Color</span><input type="color" class="color-swatch" value="${d.textColor || '#374151'}" oninput="updateSection(${sec.id},'textColor',this.value)"></div>
                <div><span class="ctrl-label">Alignment</span>
                    <select class="ctrl-select" onchange="updateSection(${sec.id},'alignment',this.value)">
                        <option value="left" ${(d.alignment === 'left' || !d.alignment) ? 'selected' : ''}>Left</option>
                        <option value="center" ${d.alignment === 'center' ? 'selected' : ''}>Center</option>
                        <option value="right" ${d.alignment === 'right' ? 'selected' : ''}>Right</option>
                    </select>
                </div>
                <div><span class="ctrl-label">Section Width</span>
                    <select class="ctrl-select" onchange="updateSection(${sec.id},'sectionWidth',this.value)">
                        ${renderWidthOptions(d.sectionWidth || '100')}
                    </select>
                </div>
                <div><span class="ctrl-label">Height</span>
                    <select class="ctrl-select" onchange="updateSection(${sec.id},'height',this.value)">
                        ${renderHeightOptions(d.height || 'auto')}
                    </select>
                </div>
            </div>
            <textarea id="text_${sec.id}">${d.content || ''}</textarea>`;
    }

    if (sec.type === 'image') {
    return headerHtml + `
        <div class="controls-grid">
            <div><span class="ctrl-label">Section Background</span><input type="color" class="color-swatch" value="${d.bg || '#ffffff'}" oninput="updateSection(${sec.id},'bg',this.value)"></div>
            <div><span class="ctrl-label">Caption Color</span><input type="color" class="color-swatch" value="${d.captionColor || '#6b7280'}" oninput="updateSection(${sec.id},'captionColor',this.value)"></div>
            <div><span class="ctrl-label">Alignment</span>
                <select class="ctrl-select" onchange="updateSection(${sec.id},'alignment',this.value)">
                    <option value="left" ${d.alignment === 'left' ? 'selected' : ''}>Left</option>
                    <option value="center" ${(d.alignment === 'center' || !d.alignment) ? 'selected' : ''}>Center</option>
                    <option value="right" ${d.alignment === 'right' ? 'selected' : ''}>Right</option>
                </select>
            </div>
            <div><span class="ctrl-label">Section Width</span>
                <select class="ctrl-select" onchange="updateSection(${sec.id},'sectionWidth',this.value)">
                    ${renderWidthOptions(d.sectionWidth || '100')}
                </select>
            </div>
            <div><span class="ctrl-label">Height</span>
                <select class="ctrl-select" onchange="updateSection(${sec.id},'height',this.value)">
                    ${renderHeightOptions(d.height || 'auto')}
                </select>
            </div>
        </div>
        <label class="ctrl-label">Caption</label>
        <input type="text" class="form-control mt-1 mb-2" value="${escHtml(d.caption || '')}" oninput="updateSection(${sec.id},'caption',this.value)">
        <label class="ctrl-label">Image Upload</label>
        <input type="file" accept="image/*" class="form-control mt-1 mb-2" onchange="uploadSectionImage(this,${sec.id})">
        <img src="${d.image || ''}" id="img_${sec.id}" style="width:120px;height:80px;object-fit:cover;border-radius:6px;border:1px solid #dee2e6;${d.image ? '' : 'display:none'}">
        <small id="img_text_${sec.id}" class="text-success d-block mt-1">${d.image ? 'Image uploaded ✓' : 'No image selected'}</small>`;
}


    if (sec.type === 'cards') {
        return headerHtml + `
            <div class="controls-grid">
                <div><span class="ctrl-label">Section Background</span><input type="color" class="color-swatch" value="${d.bg || '#f8f9fa'}" oninput="updateSection(${sec.id},'bg',this.value)"></div>
                <div><span class="ctrl-label">Section Title Color</span><input type="color" class="color-swatch" value="${d.sectionTitleColor || '#1a1a2e'}" oninput="updateSection(${sec.id},'sectionTitleColor',this.value)"></div>
                <div><span class="ctrl-label">Columns</span>
                    <select class="ctrl-select" onchange="updateSection(${sec.id},'columns',parseInt(this.value,10))">
                        <option value="1" ${(parseInt(d.columns || 3, 10) === 1) ? 'selected' : ''}>1</option>
                        <option value="2" ${(parseInt(d.columns || 3, 10) === 2) ? 'selected' : ''}>2</option>
                        <option value="3" ${(parseInt(d.columns || 3, 10) === 3) ? 'selected' : ''}>3</option>
                        <option value="4" ${(parseInt(d.columns || 3, 10) === 4) ? 'selected' : ''}>4</option>
                        <option value="5" ${(parseInt(d.columns || 3, 10) === 5) ? 'selected' : ''}>5</option>
                    </select>
                </div>
                <div><span class="ctrl-label">Section Width</span>
                    <select class="ctrl-select" onchange="updateSection(${sec.id},'sectionWidth',this.value)">
                        ${renderWidthOptions(d.sectionWidth || '100')}
                    </select>
                </div>
                <div><span class="ctrl-label">Alignment</span>
                    <select class="ctrl-select" onchange="updateSection(${sec.id},'alignment',this.value)">
                        <option value="left" ${(d.alignment || 'left') === 'left' ? 'selected' : ''}>Left</option>
                        <option value="center" ${d.alignment === 'center' ? 'selected' : ''}>Center</option>
                        <option value="right" ${d.alignment === 'right' ? 'selected' : ''}>Right</option>
                    </select>
                </div>
                <div><span class="ctrl-label">Height</span>
                    <select class="ctrl-select" onchange="updateSection(${sec.id},'height',this.value)">
                        ${renderHeightOptions(d.height || 'auto')}
                    </select>
                </div>
                <div><span class="ctrl-label">Section Title</span>
                    <input type="text" class="form-control" value="${escHtml(d.sectionTitle || '')}" oninput="updateSection(${sec.id},'sectionTitle',this.value)">
                </div>
            </div>
            <div class="d-flex gap-2 mb-3">
                <button type="button" class="btn btn-sm btn-warning" onclick="addCard(${sec.id}, 'card')">+ Card</button>
                <button type="button" class="btn btn-sm btn-success" onclick="addCard(${sec.id}, 'image')">+ Image</button>
            </div>
            <div id="cards_list_${sec.id}" class="cards-builder-list"></div>`;
    }

    return headerHtml;
}

function render() {
    tinymce.remove();
    normalizeSections();

    let builder = document.getElementById('builder');
    builder.innerHTML = '';

    sections.forEach(sec => {
        let wrapper = document.createElement('div');
        wrapper.className = 'builder-section-card';
        wrapper.setAttribute('data-id', String(sec.id));
        wrapper.innerHTML = buildSectionEditorHTML(sec);
        builder.appendChild(wrapper);

        if (sec.type === 'cards') renderCardsEditor(sec.id);
    });

    initEditor();
    saveJSON();
    schedulePreviewSync();
    attachBuilderSortable();
}

function attachBuilderSortable() {
    let builder = document.getElementById('builder');

    new Sortable(builder, {
        animation: 150,
        handle: '.drag-handle',
        draggable: '.builder-section-card',
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        onEnd: function() {
            let newOrder = [];
            builder.querySelectorAll('.builder-section-card[data-id]').forEach(el => {
                let id = el.getAttribute('data-id');
                let found = sections.find(s => String(s.id) === String(id));
                if (found) newOrder.push(found);
            });
            sections = newOrder;
            saveJSON();
            schedulePreviewSync();
        }
    });
}
function uploadTinyMceImage(blobInfo) {
    return new Promise((resolve, reject) => {
        let fd = new FormData();
        fd.append('image', blobInfo.blob(), blobInfo.filename());

        fetch('/admin/upload-image', {
            method: 'POST',
            body: fd,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            let text = await response.text();
            try {
                return JSON.parse(text);
            } catch (error) {
                throw new Error('Invalid JSON response');
            }
        })
        .then(data => {
            if (!data.success || !data.url) {
                reject(data.error || 'Image upload failed');
                return;
            }

            resolve(data.url);
        })
        .catch(error => reject(error.message || 'Image upload failed'));
    });
}

function getTinyMceConfig(selector, height, onContentChange) {
    return {
        selector,
        height,
        min_height: height,
        promotion: false,
        branding: false,
        menubar: 'file edit view insert format table tools help',
        statusbar: true,
        resize: true,
        elementpath: true,
        browser_spellcheck: true,
        contextmenu: 'undo redo | inserttable | cell row column deletetable | link image',
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount',
            'emoticons', 'quickbars', 'autoresize'
        ],
        toolbar: [
            'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor',
            'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | blockquote',
            'link image media table | hr emoticons charmap | removeformat code preview fullscreen'
        ],
        quickbars_selection_toolbar: 'bold italic underline | blocks | forecolor backcolor | quicklink blockquote',
        quickbars_insert_toolbar: 'image media table hr',
        block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6; Preformatted=pre',
        font_size_formats: '12px 14px 16px 18px 20px 24px 30px 36px 48px',
        line_height_formats: '1 1.2 1.4 1.6 1.8 2 2.4',
        image_title: true,
        automatic_uploads: true,
        images_upload_handler: uploadTinyMceImage,
        paste_data_images: true,
        link_default_target: '_blank',
        link_assume_external_targets: true,
        convert_urls: false,
        relative_urls: false,
        remove_script_host: false,
        toolbar_sticky: true,
        content_style: `
            body { font-family: DM Sans, sans-serif; font-size: 15px; line-height: 1.7; }
            p { margin: 0 0 12px; }
            img { max-width: 100%; height: auto; }
        `,
        setup: function(editor) {
            editor.on('init', function() {
                const source = document.querySelector(selector);
                editor.setContent(source ? (source.value || '') : '');
            });

            editor.on('change input undo redo keyup SetContent', function() {
                onContentChange(editor.getContent());
            });
        }
    };
}

function initCardDescriptionEditors(sectionId = null) {
    const selector = sectionId
        ? `textarea[id^="card_desc_${sectionId}_"]`
        : 'textarea[id^="card_desc_"]';

    document.querySelectorAll(selector).forEach(el => {
        if (tinymce.get(el.id)) return;

        tinymce.init(getTinyMceConfig(`#${el.id}`, 320, function(content) {
            const parts = el.id.split('_');
            const targetSectionId = parseInt(parts[2], 10);
            const targetCardId = parseInt(parts[3], 10);
            updateCard(targetSectionId, targetCardId, 'description', content);
        }));
    });
}

function initEditor() {
    sections.forEach(sec => {
        if (sec.type === 'text') {
            let selector = `#text_${sec.id}`;
            if (!document.querySelector(selector) || tinymce.get(`text_${sec.id}`)) return;

            tinymce.init(getTinyMceConfig(selector, 420, function(content) {
                updateSection(sec.id, 'content', content);
            }));
        }

        if (sec.type === 'cards') {
            initCardDescriptionEditors(sec.id);
        }
    });
}


function saveJSON() {
    document.getElementById('content_json').value = JSON.stringify(sections);
}

function togglePreviewModal() {
    let modal = document.getElementById('preview-modal');
    if (modal.style.display === 'flex') closePreviewModal();
    else {
        modal.style.display = 'flex';
        syncPreviewModal();
    }
}

function closePreviewModal() {
    document.getElementById('preview-modal').style.display = 'none';
}

function syncPreviewModal() {
    let html = buildPreviewHTML(sections);
    let frame = document.getElementById('preview-modal-frame');
    let doc = frame.contentDocument || frame.contentWindow.document;
    doc.open();
    doc.write(html);
    doc.close();
}

function schedulePreviewSync() {
    clearTimeout(previewDebounce);
    previewDebounce = setTimeout(() => {
        let modal = document.getElementById('preview-modal');
        if (modal.style.display === 'flex') syncPreviewModal();
    }, 300);
}

function buildPreviewHTML(sections) {
    let sectionsHTML = '';

    sections.forEach(sec => {
        let d = sec.data || {};
        let sectionWidth = formatWidthStyle(d.sectionWidth || '100');
        let sectionHeight = d.height || 'auto';
        let sectionAlign = d.alignment || 'left';
        let sectionJustify = sectionAlign === 'center' ? 'center' : sectionAlign === 'right' ? 'flex-end' : 'flex-start';

        if (sec.type === 'hero') {
            sectionsHTML += `
            <div class="pb-section" data-section-id="${sec.id}" style="width:${sectionWidth};margin:0 auto;">
                <div class="pb-section-handle pb-drag-bar"><span class="pb-grip">⠿⠿</span> HERO</div>
                <section style="background:${d.bg || '#f8f9fa'};padding:70px 20px;text-align:${sectionAlign};min-height:${sectionHeight};display:flex;flex-direction:column;justify-content:center;align-items:${sectionJustify};">
                    <h1 style="font-size:2.2rem;font-weight:800;color:${d.textColor || '#1a1a2e'};margin:0 0 12px;line-height:1.2;">${escPreview(d.title || 'Hero Title')}</h1>
                    ${d.subtitle ? `<p style="font-size:1.05rem;color:${d.subtitleColor || '#6b7280'};max-width:560px;margin:0 0 24px;line-height:1.7;text-align:${sectionAlign};">${escPreview(d.subtitle)}</p>` : ''}
                    ${d.btnText ? `<a href="${escPreview(d.btnLink || 'javascript:void(0)')}" style="display:inline-block;padding:12px 28px;border-radius:10px;font-size:.95rem;font-weight:600;text-decoration:none;background:${d.btnColor || '#4361ee'};color:${d.btnTextColor || '#fff'};">${escPreview(d.btnText)}</a>` : ''}
                </section>
            </div>`;
        }

        if (sec.type === 'text') {
            sectionsHTML += `
            <div class="pb-section" data-section-id="${sec.id}" style="width:${sectionWidth};margin:0 auto;">
                <div class="pb-section-handle pb-drag-bar"><span class="pb-grip">⠿⠿</span> TEXT</div>
                <section style="background:${d.bg || '#ffffff'};padding:50px 20px;min-height:${sectionHeight};display:flex;justify-content:${sectionJustify};align-items:flex-start;">
                    <div style="max-width:800px;width:100%;font-size:1rem;line-height:1.8;color:${d.textColor || '#374151'};text-align:${sectionAlign};">
                        ${d.content || '<p style="color:#aaa;font-style:italic;">Text section</p>'}
                    </div>
                </section>
            </div>`;
        }

       if (sec.type === 'image') {
    let fullWidthImage = parseFloat(d.sectionWidth || '100') >= 100;
    sectionsHTML += `
    <div class="pb-section" data-section-id="${sec.id}" style="width:${sectionWidth};margin:0 auto;">
        <div class="pb-section-handle pb-drag-bar"><span class="pb-grip">⠿⠿</span> IMAGE</div>
        <section style="
            background:${d.bg || '#ffffff'};
            padding:48px 20px;
            min-height:${sectionHeight};
            display:flex;
            flex-direction:column;
            justify-content:center;
        ">
            ${d.image ? `
                <div style="
                    width:100%;
                    display:flex;
                    justify-content:${sectionJustify};
                    align-items:center;
                ">
                    <img
                        src="${d.image}"
                        style="
                            display:block;
                            width:${fullWidthImage ? '100%' : 'auto'};
                            max-width:${fullWidthImage ? '100%' : '70%'};
                            max-height:${sectionHeight === 'auto' ? '520px' : sectionHeight};
                            border-radius:18px;
                            box-shadow:0 6px 28px rgba(0,0,0,.10);
                            object-fit:${fullWidthImage ? 'cover' : 'contain'};
                        "
                    >
                </div>
            ` : ``}

            ${d.caption ? `
                <div style="
                    width:100%;
                    display:flex;
                    justify-content:${sectionJustify};
                ">
                    <p style="
                        margin:12px 0 0;
                        font-size:.875rem;
                        color:${d.captionColor || '#6b7280'};
                        max-width:${fullWidthImage ? '100%' : '70%'};
                        text-align:${sectionAlign};
                    ">
                        ${escPreview(d.caption)}
                    </p>
                </div>
            ` : ''}
        </section>
    </div>`;
}



        if (sec.type === 'cards') {
            let cards = Array.isArray(d.cards) ? d.cards : [];
            let cols = parseInt(d.columns || 3, 10);
            let defaultWidth = cols === 1 ? '100%' : `calc(${(100 / cols).toFixed(4)}% - 14px)`;

            let cardsHTML = cards.length === 0
                ? `<div class="pb-cards-empty">This section is empty. Add a card or image from the section controls.</div>`
                : cards.map(card => {
                    let normalizedCardWidth = normalizeWidthValue(card.width || '33.333', '33.333');
                    let isImage = card.type === 'image';
                    let isFullWidthImage = isImage && normalizedCardWidth !== 'auto' && parseFloat(normalizedCardWidth) >= 100;
                    let itemWidth = normalizedCardWidth === 'auto' ? 'auto' : (isFullWidthImage ? '100%' : `calc(${normalizedCardWidth}% - 14px)`);
                    let itemHeight = card.height || 'auto';
                    let itemAlign = card.alignment || 'left';
                    let itemJustify = itemAlign === 'center' ? 'center' : itemAlign === 'right' ? 'flex-end' : 'flex-start';

                    return `
                    <div class="pb-card" data-card-id="${card.id}" data-section-id="${sec.id}" style="width:${itemWidth};flex:0 0 ${itemWidth};border-radius:18px;overflow:hidden;">
                        <div class="pb-card-handle"><span class="pb-grip">⠿⠿</span> ${isImage ? 'IMAGE' : 'CARD'}</div>
                        <div class="pb-card-body" style="background:${card.cardBg || '#fff'};min-height:${itemHeight};text-align:${itemAlign};">
                            ${card.image ? `
                                <div class="pb-card-img" style="
                                    ${itemHeight !== 'auto' ? `height:${itemHeight};` : 'min-height:180px;'}
                                    display:flex;
                                    align-items:center;
                                    justify-content:${itemJustify};
                                    padding:${isFullWidthImage ? '0' : '12px'};
                                    background:${card.cardBg || '#fff'};
                                ">
                                    <img src="${card.image}" alt="" style="
                                        width:${isFullWidthImage ? '100%' : 'auto'};
                                        max-width:100%;
                                        max-height:100%;
                                        object-fit:${isFullWidthImage ? 'cover' : 'contain'};
                                        display:block;
                                        border-radius:18px;
                                    ">
                                </div>
                            ` : ''}
                            ${isImage
                                ? `${card.caption ? `<div class="pb-card-content" style="text-align:${itemAlign};align-items:${itemJustify};"><p style="color:${card.captionColor || '#6b7280'};">${escPreview(card.caption)}</p></div>` : ''}`
                                : `<div class="pb-card-content" style="text-align:${itemAlign};align-items:${itemJustify};">
                                    ${card.title ? `<h3 style="color:${card.titleColor || '#1a1a2e'};">${escPreview(card.title)}</h3>` : ''}
                                    ${card.description ? `<div style="color:${card.descColor || '#6b7280'};">${card.description}</div>` : ''}

                                    ${card.btnText ? `<a href="${escPreview(card.btnLink || 'javascript:void(0)')}" class="pb-card-btn" style="background:${card.btnColor || '#4361ee'};color:${card.btnTextColor || '#fff'};">${escPreview(card.btnText)}</a>` : ''}
                                   </div>`
                            }
                        </div>
                    </div>`;
                }).join('');

            sectionsHTML += `
            <div class="pb-section" data-section-id="${sec.id}" style="width:${sectionWidth};margin:0 auto;">
                <div class="pb-section-handle pb-drag-bar"><span class="pb-grip">⠿⠿</span> SECTION</div>
                <section style="background:${d.bg || '#f8f9fa'};padding:60px 20px;min-height:${sectionHeight};text-align:${sectionAlign};">
                    ${d.sectionTitle ? `<h2 style="text-align:${sectionAlign};font-size:1.8rem;font-weight:800;color:${d.sectionTitleColor || '#1a1a2e'};margin:0 0 40px;">${escPreview(d.sectionTitle)}</h2>` : ''}
                    <div class="pb-cards-grid" data-section-id="${sec.id}">
                        ${cardsHTML}
                    </div>
                </section>
            </div>`;
        }
    });

    return `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Preview</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/frontend/css/style.css">
<link rel="stylesheet" href="/frontend/css/style_new.css">
<link rel="stylesheet" href="/frontend/css/developer.css">
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"><\/script>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#fff;overflow-x:hidden;padding-bottom:40px}
img{max-width:100%}
.pb-section{position:relative;border:2px solid transparent;outline:2px solid transparent;outline-offset:4px;transition:border-color .15s,outline-color .15s,box-shadow .15s;margin-bottom:20px;border-radius:22px}
.pb-section:hover{border-color:rgba(67,97,238,.22);outline-color:rgba(67,97,238,.55);box-shadow:0 0 0 6px rgba(67,97,238,.12)}
.pb-drag-bar{display:flex;align-items:center;justify-content:center;gap:8px;padding:7px 16px;background:#4361ee;color:#fff;font-size:11px;font-weight:700;letter-spacing:.06em;cursor:grab;user-select:none;opacity:0;transition:opacity .15s;position:relative;z-index:30}
.pb-section:hover .pb-drag-bar{opacity:1}
.pb-grip{font-size:15px;letter-spacing:-3px}
.pb-cards-grid{display:flex;flex-wrap:wrap;gap:20px;align-items:flex-start}
.pb-cards-empty{width:100%;padding:32px;border:2px dashed #e2e8f0;border-radius:12px;text-align:center;color:#94a3b8;font-size:14px}
.pb-card{display:flex;flex-direction:column;position:relative;border-radius:18px;border:2px solid transparent;transition:border-color .15s,box-shadow .15s;min-width:0}
.pb-card:hover{border-color:transparent !important;box-shadow:none !important}
.pb-section .pb-card{border-color:transparent !important;box-shadow:none !important}
.pb-card-handle{display:flex;align-items:center;justify-content:center;gap:5px;padding:6px 10px;background:#4361ee;color:#fff;font-size:9px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;border-radius:16px 16px 0 0;cursor:grab;user-select:none;opacity:0;transition:opacity .15s ease;flex-shrink:0;position:relative;z-index:20;touch-action:none}
.pb-card:hover .pb-card-handle{opacity:1}
.pb-card-body{border-radius:0 0 16px 16px;overflow:hidden;box-shadow:0 2px 14px rgba(0,0,0,.07);display:flex;flex-direction:column;flex:1}
.pb-card-img{width:100%;overflow:hidden;flex-shrink:0}
.pb-card-img img{display:block}
.pb-card-content{padding:18px;display:flex;flex-direction:column;flex:1}
.pb-card-content h3{font-size:1rem;font-weight:700;margin:0 0 8px;line-height:1.3}
.pb-card-content p{font-size:.875rem;line-height:1.65;flex:1;margin:0 0 14px}
.pb-card-content ul,
.pb-card-body ul,
.pb-section ul{margin:0;padding:0}
.pb-card-content li,
.pb-card-body li,
.pb-section li{position:relative;padding-left:32px;margin-bottom:12px;list-style:none}
.pb-card-content li::before,
.pb-card-body li::before,
.pb-section li::before{content:"\\f00c";font-family:"Font Awesome 6 Free";font-weight:900;position:absolute;top:5px;left:0;width:20px;height:20px;border-radius:50%;background:#004fb8;color:#fff;display:flex;align-items:center;justify-content:center;font-size:10px;line-height:1}
.pb-card-content a,
.pb-card-body a,
.pb-section a{display:inline-flex;align-items:center;justify-content:center;padding:10px 22px;border-radius:10px;background:#004fb8;color:#fff !important;font-size:inherit;font-weight:inherit;line-height:inherit;text-decoration:none !important;transition:background .2s,transform .15s}
.pb-card-content a:hover,
.pb-card-body a:hover,
.pb-section a:hover{background:#005fdb;color:#fff !important;transform:translateY(-1px)}
.pb-card-btn{display:inline-block;padding:8px 18px;border-radius:10px;font-size:.8rem;font-weight:600;text-decoration:none}
.pb-ghost-section{opacity:.2;outline:3px dashed #4361ee;background:#eef0ff !important}
.pb-chosen-section{outline:2px solid #4361ee;box-shadow:0 8px 32px rgba(67,97,238,.2)}
.pb-ghost-card{opacity:.25;background:#eef0ff !important;border:2px dashed #4361ee !important;border-radius:18px}
.pb-chosen-card{outline:2px solid #4361ee;box-shadow:0 6px 24px rgba(67,97,238,.18)}
.sortable-fallback{opacity:.85 !important;box-shadow:0 12px 40px rgba(67,97,238,.35) !important;border-radius:18px !important;z-index:99999 !important;pointer-events:none !important}
</style>
</head>
<body>
${previewHeaderHtml}
<main class="pb-preview-main">
<div id="pb-wrapper">${sectionsHTML}</div>
</main>
${previewFooterHtml}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"><\/script>
<script src="/frontend/js/script_new.js"><\/script>
<script>
(function(){
    var wrapper = document.getElementById('pb-wrapper');

    if (wrapper) {
        Sortable.create(wrapper, {
            animation:220,
            handle:'.pb-drag-bar',
            draggable:'.pb-section',
            filter:'.pb-cards-grid,.pb-card,.pb-card-handle',
            ghostClass:'pb-ghost-section',
            chosenClass:'pb-chosen-section',
            fallbackClass:'sortable-fallback',
            forceFallback:true,
            fallbackOnBody:true,
            fallbackTolerance:3,
            onEnd:function(){
                var order = [];
                wrapper.querySelectorAll(':scope > .pb-section[data-section-id]').forEach(function(el){
                    order.push(el.getAttribute('data-section-id'));
                });
                window.parent.postMessage({ type:'SECTION_REORDER', order:order }, '*');
            }
        });
    }

    function sendCardOrder(grid, fromGrid) {
        var order = [];
        grid.querySelectorAll(':scope > .pb-card[data-card-id]').forEach(function(el){
            order.push(el.getAttribute('data-card-id'));
        });

        window.parent.postMessage({
            type:'CARD_REORDER',
            fromSectionId: fromGrid ? fromGrid.getAttribute('data-section-id') : grid.getAttribute('data-section-id'),
            toSectionId: grid.getAttribute('data-section-id'),
            order: order
        }, '*');
    }

    document.querySelectorAll('.pb-cards-grid[data-section-id]').forEach(function(grid){
        Sortable.create(grid, {
            animation:200,
            handle:'.pb-card-handle',
            draggable:'.pb-card',
            ghostClass:'pb-ghost-card',
            chosenClass:'pb-chosen-card',
            fallbackClass:'sortable-fallback',
            forceFallback:true,
            fallbackOnBody:true,
            fallbackTolerance:3,
            swapThreshold:0.65,
            group:{ name:'preview-cards', pull:true, put:true },
            onAdd:function(evt){ sendCardOrder(grid, evt.from); },
            onUpdate:function(evt){ sendCardOrder(grid, evt.from); }
        });
    });
})();
<\/script>
</body>
</html>`;
}

render();
</script>
@endsection
