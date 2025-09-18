@extends('layouts.admin')

@section('title', 'Form Builder')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Form Builder</h1>
        <div class="d-flex gap-2">
            <button id="save-form" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Form</button>
            <button id="preview-form" class="btn btn-success"><i class="fas fa-eye me-1"></i> Preview</button>
        </div>
         
    </div>

    <div class="row g-4">
        <!-- Form Elements -->
        <div class="col-lg-2">
            <div class="card shadow-sm p-3">
                <h5>Form Elements</h5>
                <div id="form-elements">
                    <div class="drag-item border p-2 mb-2" data-type="text">Text Input</div>
                    <div class="drag-item border p-2 mb-2" data-type="textarea">Textarea</div>
                    <div class="drag-item border p-2 mb-2" data-type="select">Select</div>
                    <div class="drag-item border p-2 mb-2" data-type="radio">Radio</div>
                    <div class="drag-item border p-2 mb-2" data-type="checkbox">Checkbox</div>
                    <div class="drag-item border p-2 mb-2" data-type="email">Email</div>
                    <div class="drag-item border p-2 mb-2" data-type="number">Number</div>
                    <div class="drag-item border p-2 mb-2" data-type="date">Date</div>
                </div>
            </div>
        </div>

        <!-- Form Canvas -->
        <div class="col-lg-7">
            <div class="card shadow-sm p-3">
                <h5>Form Builder</h5>
                <input type="text" id="form-title" class="form-control mb-2" placeholder="Form Title">
                <textarea id="form-description" class="form-control mb-3" placeholder="Form Description"></textarea>
                <div id="form-canvas" class="drop-zone p-3" style="min-height:300px; border:2px dashed #ccc;">
                    <div id="empty-state" class="text-center text-muted py-5">Drag fields here</div>
                </div>
            </div>
        </div>

        <!-- Properties Panel -->
        <div class="col-lg-3">
            <div class="card shadow-sm p-3" id="properties-panel">
                <h5>Field Properties</h5>
                <div id="properties-content">
                    <p class="text-muted">Select a field to edit</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="preview-modal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="previewModalLabel">Form Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="preview-content"></div>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>

window.__EXISTING_FORM__ = @json($form ?? null);
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('form-canvas');
    const emptyState = document.getElementById('empty-state');
    const previewBtn = document.getElementById('preview-form');
    const saveBtn = document.getElementById('save-form');
    const previewModal = new bootstrap.Modal(document.getElementById('preview-modal'));
    const previewContent = document.getElementById('preview-content');

    const formTitleEl = document.getElementById('form-title');
    const formDescEl  = document.getElementById('form-description');

    // edit mode?
    const existingForm = window.__EXISTING_FORM__;
    const formId = existingForm?.id ?? null;

    let selectedField = null;

    // Make canvas sortable
    new Sortable(canvas, {
        group: 'fields',
        animation: 150,
        onAdd: () => { emptyState.style.display = 'none'; }
    });

    // Make form elements draggable
    document.querySelectorAll('#form-elements .drag-item').forEach(item => {
        item.setAttribute('draggable', true);
        item.addEventListener('dragstart', e => e.dataTransfer.setData('type', item.dataset.type));
    });

    canvas.addEventListener('dragover', e => e.preventDefault());
    canvas.addEventListener('drop', e => {
        e.preventDefault();
        const type = e.dataTransfer.getData('type');
        if(!type) return;
        emptyState.style.display = 'none';
        const field = createField(type /* no preset data on new */);
        canvas.appendChild(field);
        selectField(field);
    });

    /**
     * Create field wrapper. If "data" supplied (when loading existing form),
     * the field is initialized from it (label, options, validation, conditional, etc.)
     */
    function createField(type, data = null){
        const wrapper = document.createElement('div');
        wrapper.className = 'form-field border p-2 mb-2';
        wrapper.dataset.type = type;

        // Label input (always the first text input in wrapper)
        const label = document.createElement('input');
        label.type = 'text';
        label.className = 'form-control mb-2';
        label.placeholder = 'Field Label';
        label.value = data?.label ?? (type.charAt(0).toUpperCase()+type.slice(1));

        // Input/Control area
        let input;

        switch(type){
            case 'text':
            case 'email':
            case 'number':
            case 'date':
                input = document.createElement('input');
                input.type = type;
                input.className='form-control';
                break;

            case 'textarea':
                input = document.createElement('textarea');
                input.className='form-control';
                break;

            case 'select':
                input = document.createElement('select');
                input.className='form-select';
                (data?.options?.length ? data.options : ['Option 1', 'Option 2']).forEach(o => {
                    const opt = document.createElement('option');
                    opt.textContent = o;
                    input.appendChild(opt);
                });
                break;

            case 'radio':
            case 'checkbox':
                input = document.createElement('div');
                const opts = data?.options?.length ? data.options : ['Option 1'];
                input.innerHTML = '';
                opts.forEach((o, idx) => {
                    const id = `${type}-${cryptoRandomId()}`;
                    input.insertAdjacentHTML('beforeend', `
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="${type === 'radio' ? 'radio' : 'checkbox'}" name="${id}">
                            <label class="form-check-label">${o}</label>
                        </div>
                    `);
                });
                break;
        }

        // Actions
        const actions = document.createElement('div');
        actions.className = 'field-actions mt-1 d-flex gap-2';

        const delBtn = document.createElement('button');
        delBtn.type = 'button';
        delBtn.className = 'btn btn-sm btn-danger';
        delBtn.textContent = 'Delete';
        delBtn.addEventListener('click', e => {
            e.stopPropagation();
            wrapper.remove();
            selectedField = null;
            document.getElementById('properties-content').innerHTML = '<p class="text-muted">Select a field to edit</p>';
            if(!canvas.querySelector('.form-field')) emptyState.style.display = '';
        });

        // (Optional) duplicate helper to speed up editing
        const dupBtn = document.createElement('button');
        dupBtn.type = 'button';
        dupBtn.className = 'btn btn-sm btn-outline-secondary';
        dupBtn.textContent = 'Duplicate';
        dupBtn.addEventListener('click', e => {
            e.stopPropagation();
            const cloneData = serializeField(wrapper);
            const f = createField(cloneData.type, cloneData);
            canvas.insertBefore(f, wrapper.nextSibling);
        });
        
        if (data && data.is_delete_able !== undefined && data.is_delete_able == 0) {
            
        }else{
           actions.appendChild(delBtn);
           actions.appendChild(dupBtn);
        }

        wrapper.appendChild(label);
        wrapper.appendChild(input);
        wrapper.appendChild(actions);

        // Persist datasets from existing data
        if (data) {
            // Validation
            if (Array.isArray(data.validation) && data.validation.length) {
                wrapper.dataset.validation = data.validation.join(',');
            }
            if (data.min != null) wrapper.dataset.min = data.min;
            if (data.max != null) wrapper.dataset.max = data.max;

            // Conditional
            if (data.conditional_logic) {
                wrapper.dataset.conditionalLogic = JSON.stringify(data.conditional_logic);
            }
        }

        wrapper.addEventListener('click', e => {
            if(e.target.closest('.field-actions')) return;
            selectField(wrapper);
        });

        return wrapper;
    }

    function selectField(field){
        if(selectedField) selectedField.style.borderColor = '#dee2e6';
        selectedField = field;
        field.style.borderColor = '#0d6efd';
        showProperties(field);
    }

    function showProperties(field) {
        const panel = document.getElementById('properties-panel');
        panel.innerHTML = `
            <h5>Field Properties</h5>
            <div id="properties-content"></div>
        `;
        const content = document.getElementById('properties-content');

        // Field type info
        const typeInfo = document.createElement('p');
        typeInfo.textContent = 'Field Type: ' + field.dataset.type;
        content.appendChild(typeInfo);

        // Field label
        content.appendChild(document.createTextNode('Field Label:'));
        const labelInput = document.createElement('input');
        labelInput.type = 'text';
        labelInput.className = 'form-control mb-2';
        labelInput.value = field.querySelector('input[type=text]')?.value || '';
        labelInput.addEventListener('input', e => {
            field.querySelector('input[type=text]').value = e.target.value;
            // Update target_field in conditional logic if exists
            if(field.dataset.conditionalLogic){
                const logic = JSON.parse(field.dataset.conditionalLogic);
                logic.target_field = e.target.value;
                field.dataset.conditionalLogic = JSON.stringify(logic);
            }
        });
        content.appendChild(labelInput);

        // Options editors
        const kind = field.dataset.type;

        if(kind === 'select'){
            const optionsLabel = document.createElement('label');
            optionsLabel.textContent = 'Options (comma separated):';
            const optionsInput = document.createElement('input');
            optionsInput.type = 'text';
            optionsInput.className = 'form-control mb-2';
            const selectEl = field.querySelector('select');
            if(selectEl){
                optionsInput.value = Array.from(selectEl.options).map(o => o.text).join(',');
            }
            optionsInput.addEventListener('input', e => {
                const values = e.target.value.split(',').map(v => v.trim()).filter(Boolean);
                selectEl.innerHTML = '';
                values.forEach(v => {
                    const opt = document.createElement('option');
                    opt.textContent = v;
                    selectEl.appendChild(opt);
                });
            });
            content.appendChild(optionsLabel);
            content.appendChild(optionsInput);
        }

        if(kind === 'radio' || kind === 'checkbox'){
            const optionsLabel = document.createElement('label');
            optionsLabel.textContent = 'Options (comma separated):';
            const optionsInput = document.createElement('input');
            optionsInput.type = 'text';
            optionsInput.className = 'form-control mb-2';

            const current = Array.from(field.querySelectorAll('.form-check label')).map(l => l.textContent);
            optionsInput.value = current.join(',');

            optionsInput.addEventListener('input', e => {
                const holder = field.querySelector('div');
                holder.innerHTML = '';
                e.target.value.split(',').map(v => v.trim()).filter(Boolean).forEach(v => {
                    const id = `${kind}-${cryptoRandomId()}`;
                    holder.insertAdjacentHTML('beforeend', `
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="${kind === 'radio' ? 'radio' : 'checkbox'}" name="${id}">
                            <label class="form-check-label">${v}</label>
                        </div>
                    `);
                });
            });

            content.appendChild(optionsLabel);
            content.appendChild(optionsInput);
        }

        // Validation
        content.appendChild(document.createTextNode('Validation Rules:'));
        const validationDiv = document.createElement('div');

        // Required
        const requiredDiv = document.createElement('div');
        requiredDiv.className = 'form-check mb-2';
        const requiredInput = document.createElement('input');
        requiredInput.type = 'checkbox';
        requiredInput.className = 'form-check-input';
        requiredInput.checked = (field.dataset.validation || '').split(',').includes('required');
        const requiredLabel = document.createElement('label');
        requiredLabel.textContent = 'Required';
        requiredLabel.className = 'form-check-label';
        requiredDiv.appendChild(requiredInput);
        requiredDiv.appendChild(requiredLabel);
        validationDiv.appendChild(requiredDiv);

        // Min length
        const minInput = document.createElement('input');
        minInput.type = 'number';
        minInput.className = 'form-control mb-2';
        minInput.placeholder = 'Min length';
        minInput.value = field.dataset.min || '';
        validationDiv.appendChild(minInput);

        // Max length
        const maxInput = document.createElement('input');
        maxInput.type = 'number';
        maxInput.className = 'form-control mb-2';
        maxInput.placeholder = 'Max length';
        maxInput.value = field.dataset.max || '';
        validationDiv.appendChild(maxInput);

        content.appendChild(validationDiv);

        // Conditional Logic
        content.appendChild(document.createTextNode('Conditional Logic:'));
        const condDiv = document.createElement('div');
        condDiv.className = 'mb-2';

        const conditionSelect = document.createElement('select');
        conditionSelect.className = 'form-select mb-2';
        ['none','show','hide'].forEach(opt => {
            const option = document.createElement('option');
            option.value = opt;
            option.textContent = opt.charAt(0).toUpperCase() + opt.slice(1);
            conditionSelect.appendChild(option);
        });
        condDiv.appendChild(conditionSelect);

        const sourceSelect = document.createElement('select');
        sourceSelect.className = 'form-select mb-2';
        sourceSelect.innerHTML = '<option value="">Select field</option>';
        document.querySelectorAll('#form-canvas .form-field').forEach(f => {
            if(f !== field){
                const text = f.querySelector('input[type=text]')?.value || f.dataset.type;
                const opt = document.createElement('option');
                opt.value = f.dataset.type + '-' + text;
                opt.textContent = text;
                sourceSelect.appendChild(opt);
            }
        });
        condDiv.appendChild(sourceSelect);

        const operatorSelect = document.createElement('select');
        operatorSelect.className = 'form-select mb-2';
        ['==','!=','>','<','>=','<=','contains','not_contains'].forEach(op => {
            const option = document.createElement('option');
            option.value = op;
            option.textContent = op;
            operatorSelect.appendChild(option);
        });
        condDiv.appendChild(operatorSelect);

        const valueInput = document.createElement('input');
        valueInput.type = 'text';
        valueInput.className = 'form-control mb-2';
        valueInput.placeholder = 'Value';
        condDiv.appendChild(valueInput);

        content.appendChild(condDiv);

        // Load existing conditional logic
        if(field.dataset.conditionalLogic){
            try {
                const logic = JSON.parse(field.dataset.conditionalLogic);
                conditionSelect.value = logic.condition ?? 'none';
                sourceSelect.value = logic.source_field ?? '';
                operatorSelect.value = logic.operator ?? '==';
                valueInput.value = logic.value ?? '';
            } catch (_) {}
        }

        // Save changes to dataset
        [requiredInput, minInput, maxInput, conditionSelect, sourceSelect, operatorSelect, valueInput].forEach(el => {
            el.addEventListener('change', () => {
                const rules = [];
                if(requiredInput.checked) rules.push('required');
                field.dataset.validation = rules.join(',');
                field.dataset.min = minInput.value;
                field.dataset.max = maxInput.value;

                field.dataset.conditionalLogic = JSON.stringify({
                    condition: conditionSelect.value,
                    source_field: sourceSelect.value,
                    operator: operatorSelect.value,
                    value: valueInput.value,
                    target_field: field.querySelector('input[type=text]').value
                });
            });
        });
    }

    // Serialize one field node to JSON (used by save & duplicate)
    function serializeField(f){
        const type = f.dataset.type;
        const label = f.querySelector('input[type=text]')?.value || '';

        const validation = (f.dataset.validation || '')
            .split(',').map(s => s.trim()).filter(Boolean);

        const min = f.dataset.min || null;
        const max = f.dataset.max || null;

        const conditional_logic = f.dataset.conditionalLogic ? JSON.parse(f.dataset.conditionalLogic) : null;

        // Options for select, radio, checkbox
        let options = [];
        if(type === 'select'){
            const selectEl = f.querySelector('select');
            options = Array.from(selectEl.options).map(opt => opt.text);
        } else if(type === 'radio' || type === 'checkbox'){
            options = Array.from(f.querySelectorAll('.form-check label')).map(l => l.textContent);
        }

        return { type, label, validation, min, max, conditional_logic, options };
    }

    // Preview
    previewBtn.addEventListener('click', () => {
        let html = '';
        canvas.querySelectorAll('.form-field').forEach(f => {
            const label = f.querySelector('input[type=text]')?.value || '';
            const type = f.dataset.type;
            html += `<div class="mb-3"><label>${label}</label>`;
            switch(type){
                case 'text':
                case 'email':
                case 'number':
                case 'date':
                    html += `<input class="form-control" type="${type}">`; break;
                case 'textarea': html += '<textarea class="form-control"></textarea>'; break;
                case 'select':
                    html += '<select class="form-select">';
                    Array.from(f.querySelector('select').options).forEach(opt => html += `<option>${opt.text}</option>`);
                    html += '</select>'; break;
                case 'radio':
                    f.querySelectorAll('.form-check').forEach(opt => {
                        const optLabel = opt.querySelector('label')?.textContent || '';
                        html += `<div class="form-check">
                                    <input type="radio" class="form-check-input" name="${f.dataset.type}-${cryptoRandomId()}">
                                    <label class="form-check-label">${optLabel}</label>
                                 </div>`;
                    }); break;
                case 'checkbox':
                    f.querySelectorAll('.form-check').forEach(opt => {
                        const optLabel = opt.querySelector('label')?.textContent || '';
                        html += `<div class="form-check">
                                    <input type="checkbox" class="form-check-input">
                                    <label class="form-check-label">${optLabel}</label>
                                 </div>`;
                    }); break;
            }
            html += '</div>';
        });
        previewContent.innerHTML = html || '<p class="text-muted">No fields added yet.</p>';
        previewModal.show();
    });

    // SAVE (create or update)
    saveBtn.addEventListener('click', () => {
        const formData = [];
        canvas.querySelectorAll('.form-field').forEach(f => formData.push(serializeField(f)));

        const payload = {
            title: formTitleEl.value,
            description: formDescEl.value,
            form_data: formData
        };

        // Decide endpoint/method
        const endpoint = formId
            ? `{{ route('forms.store') }}`                // forms.update
            : `{{ route('forms.store') }}`;                  // forms.store

        const method = 'POST';

        fetch(endpoint, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if(!res.ok){
                throw data?.errors || data || { message: 'Request failed' };
            }
            return data;
        })
        .then(data => {
            alert(formId ? 'Form updated successfully!' : 'Form saved successfully!');
            // Optional: if API returns {form: {id}} on create, redirect to edit
            // if(!formId && data.form?.id){ window.location.href = `{{ url('/forms') }}/${data.form.id}/edit`; }
        })
        .catch(err => {
            console.error(err);
            alert('Validation failed. Check console.');
        });
    });

    // If editing, hydrate UI from existing data
    if (existingForm) {
        formTitleEl.value = existingForm.title ?? '';
        formDescEl.value  = existingForm.description ?? '';

        const rows = Array.isArray(existingForm.form_data) ? existingForm.form_data : [];
        if (rows.length) {
            emptyState.style.display = 'none';
            rows.forEach(r => {
                const f = createField(r.type, r);
                canvas.appendChild(f);
            });
        }
    }

    // util
    function cryptoRandomId(){
        if(window.crypto?.randomUUID) return crypto.randomUUID();
        return 'id-' + Math.random().toString(36).slice(2,10);
    }
});
</script>
@endsection
