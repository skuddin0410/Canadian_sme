@extends('layouts.frontendapp')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="text-center mb-4">
                <h2 class="mb-2">{{ $form->title }}</h2>
                @if(!empty($form->description))
                    <p class="text-muted mb-0">{{ $form->description }}</p>
                @endif
            </div>

            <div id="form-alert" class="alert d-none" role="alert"></div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form id="dynamic-form" novalidate>
                        @foreach($form->form_data as $index => $field)
                            @php
                                $type        = $field['type'] ?? 'text';
                                $label       = $field['label'] ?? ucfirst($type);
                                $name        = Str::slug($label, '_');
                                $idBase      = "f{$index}_" . $name;
                                $isRequired  = in_array('required', $field['validation'] ?? []);
                                $min         = $field['min'] ?? null;
                                $max         = $field['max'] ?? null;
                                $logic       = $field['conditional_logic'] ?? null;
                                $hasLenRule  = in_array($type, ['text','email','textarea','number']) && ($min || $max);
                            @endphp

                            <div class="mb-3 field-wrapper"
                                 id="field-{{ $index }}"
                                 @if(!empty($logic)) data-logic='@json($logic)' @endif
                                 data-target-label="{{ $label }}">

                                <label class="form-label" for="{{ $idBase }}">
                                    {{ $label }}
                                    @if($isRequired) <span class="text-danger" aria-hidden="true">*</span> @endif
                                </label>

                                @switch($type)
                                    @case('text')
                                        <input
                                            type="text"
                                            id="{{ $idBase }}"
                                            name="{{ $name }}"
                                            class="form-control"
                                            placeholder="Enter {{ strtolower($label) }}"
                                            @if($isRequired) required @endif
                                            @if($min) minlength="{{ $min }}" @endif
                                            @if($max) maxlength="{{ $max }}" @endif
                                        >
                                        @break

                                    @case('email')
                                        <input
                                            type="email"
                                            id="{{ $idBase }}"
                                            name="{{ $name }}"
                                            class="form-control"
                                            placeholder="you@example.com"
                                            @if($isRequired) required @endif
                                            @if($min) minlength="{{ $min }}" @endif
                                            @if($max) maxlength="{{ $max }}" @endif
                                        >
                                        @break

                                    @case('number')
                                        <input
                                            type="number"
                                            id="{{ $idBase }}"
                                            name="{{ $name }}"
                                            class="form-control"
                                            placeholder="Enter {{ strtolower($label) }}"
                                            @if(!is_null($min)) min="{{ $min }}" @endif
                                            @if(!is_null($max)) max="{{ $max }}" @endif
                                            @if($isRequired) required @endif
                                        >
                                        @break

                                    @case('date')
                                        <input
                                            type="date"
                                            id="{{ $idBase }}"
                                            name="{{ $name }}"
                                            class="form-control"
                                            @if($isRequired) required @endif
                                        >
                                        @break

                                    @case('textarea')
                                        <textarea
                                            id="{{ $idBase }}"
                                            name="{{ $name }}"
                                            class="form-control"
                                            rows="4"
                                            placeholder="Type {{ strtolower($label) }} here"
                                            @if($isRequired) required @endif
                                            @if($min) minlength="{{ $min }}" @endif
                                            @if($max) maxlength="{{ $max }}" @endif
                                        ></textarea>
                                        @break

                                    @case('select')
                                        <select
                                            id="{{ $idBase }}"
                                            name="{{ $name }}"
                                            class="form-select"
                                            @if($isRequired) required @endif
                                        >
                                            <option value="" selected disabled>Choose an option</option>
                                            @foreach($field['options'] ?? [] as $opt)
                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                        @break

                                    @case('radio')
                                        <fieldset>
                                            <legend class="visually-hidden">{{ $label }}</legend>
                                            @foreach($field['options'] ?? [] as $i => $opt)
                                                @php $rid = "{$idBase}_r{$i}"; @endphp
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                           id="{{ $rid }}"
                                                           name="{{ $name }}"
                                                           value="{{ $opt }}"
                                                           @if($isRequired && $i===0) required @endif>
                                                    <label class="form-check-label" for="{{ $rid }}">{{ $opt }}</label>
                                                </div>
                                            @endforeach
                                        </fieldset>
                                        @break

                                    @case('checkbox')
                                        <fieldset>
                                            <legend class="visually-hidden">{{ $label }}</legend>
                                            @foreach($field['options'] ?? [] as $i => $opt)
                                                @php $cid = "{$idBase}_c{$i}"; @endphp
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                           id="{{ $cid }}"
                                                           name="{{ $name }}[]"
                                                           value="{{ $opt }}"
                                                           @if($isRequired && $i===0) required @endif>
                                                    <label class="form-check-label" for="{{ $cid }}">{{ $opt }}</label>
                                                </div>
                                            @endforeach
                                        </fieldset>
                                        @break
                                @endswitch

                                <div class="form-text">
                                    @if($isRequired) This field is required. @endif
                                    @if($hasLenRule)
                                        @if($type === 'number')
                                            @if(!is_null($min) && !is_null($max)) Range: {{ $min }}–{{ $max }}. @elseif(!is_null($min)) Minimum: {{ $min }}. @elseif(!is_null($max)) Maximum: {{ $max }}. @endif
                                        @else
                                            @if($min && $max) {{ $min }}–{{ $max }} characters. @elseif($min) Minimum {{ $min }} characters. @elseif($max) Maximum {{ $max }} characters. @endif
                                        @endif
                                    @endif
                                </div>
                                <div class="invalid-feedback">Please provide a valid {{ strtolower($label) }}.</div>
                            </div>
                        @endforeach

                        <div class="d-grid">
                            <button type="submit" id="submit-btn" class="btn btn-primary">
                                <span class="btn-text">Submit</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function(){
    const form = document.getElementById('dynamic-form');
    const alertBox = document.getElementById('form-alert');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnSpin = submitBtn.querySelector('.spinner-border');

    // ---------- Helpers ----------
    const showAlert = (msg, type='success') => {
        alertBox.className = `alert alert-${type}`;
        alertBox.textContent = msg;
        alertBox.classList.remove('d-none');
        alertBox.scrollIntoView({behavior: 'smooth', block: 'center'});
    };

    const clearAlert = () => {
        alertBox.className = 'alert d-none';
        alertBox.textContent = '';
    };

    const setSubmitting = (is) => {
        submitBtn.disabled = is;
        btnText.classList.toggle('d-none', is);
        btnSpin.classList.toggle('d-none', !is);
    };

    // slugify identical to backend Str::slug(..., '_')
    const slug = (s='') => (s || '')
        .toString()
        .normalize('NFKD').replace(/[\u0300-\u036F]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g,'_')
        .replace(/^_+|_+$/g,'')
        .replace(/_+/g,'_');

    // Get current value(s) of a field by name
    const getFieldValue = (name) => {
        const inputs = [...document.querySelectorAll(`[name="${name}"], [name="${name}[]"]`)];
        if (!inputs.length) return null;

        const type = inputs[0].type;
        if (type === 'radio') {
            const sel = inputs.find(i => i.checked);
            return sel ? sel.value : '';
        }
        if (type === 'checkbox') {
            return inputs.filter(i => i.checked).map(i => i.value);
        }
        if (inputs[0].tagName === 'SELECT') {
            return inputs[0].value;
        }
        return inputs[0].value;
    };

    // Evaluate an operator between left(current) and right(expected)
    const evalOp = (left, op, right) => {
        // array support for contains
        if (Array.isArray(left)) {
            if (op === 'contains') return left.includes(right);
            if (op === 'not_contains') return !left.includes(right);
            left = left.join(','); // fallback
        }

        // try numeric compare if both are numbers
        const ln = Number(left), rn = Number(right);
        const bothNum = !isNaN(ln) && !isNaN(rn);

        switch(op){
            case '==': return bothNum ? ln === rn : String(left) === String(right);
            case '!=': return bothNum ? ln !== rn : String(left) !== String(right);
            case '>':  return bothNum ? ln >  rn : String(left) >  String(right);
            case '<':  return bothNum ? ln <  rn : String(left) <  String(right);
            case '>=': return bothNum ? ln >= rn : String(left) >= String(right);
            case '<=': return bothNum ? ln <= rn : String(left) <= String(right);
            case 'contains':     return String(left).includes(String(right));
            case 'not_contains': return !String(left).includes(String(right));
            default: return false;
        }
    };

    // Apply conditional logic for all fields with data-logic
    const initConditionalLogic = () => {
        const wrappers = [...document.querySelectorAll('.field-wrapper[data-logic]')];
        const sourcesMap = {};

        wrappers.forEach(wrap => {
            try {
                const logic = JSON.parse(wrap.dataset.logic || 'null');
                if (!logic || !logic.condition || logic.condition === 'none') return;

                // logic.source_field comes as "type-label"; we need the label part → slug to get input name
                const sf = logic.source_field || '';
                const dash = sf.indexOf('-');
                const srcLabel = dash >= 0 ? sf.slice(dash + 1) : sf;
                const srcName = slug(srcLabel);

                // store mapping
                if (!sourcesMap[srcName]) sourcesMap[srcName] = [];
                sourcesMap[srcName].push({wrap, logic, srcName});
            } catch(e){}
        });

        // Attach listeners for each source and do initial compute
        Object.keys(sourcesMap).forEach(srcName => {
            const targets = sourcesMap[srcName];

            const listener = () => {
                const curVal = getFieldValue(srcName);
                targets.forEach(({wrap, logic}) => {
                    const ok = evalOp(curVal, logic.operator, logic.value);
                    const shouldShow = logic.condition === 'show' ? ok : !ok; // hide → invert
                    toggleWrapper(wrap, shouldShow);
                });
            };

            // bind to all inputs for this name (handles radio/checkbox/select/text)
            const elems = document.querySelectorAll(`[name="${srcName}"], [name="${srcName}[]"]`);
            elems.forEach(el => el.addEventListener('change', listener));
            // also input event for text-like fields
            elems.forEach(el => { if(['text','email','number','date','textarea'].includes((el.type||'').toLowerCase())) el.addEventListener('input', listener); });

            // initial run
            listener();
        });
    };

    const toggleWrapper = (wrap, show) => {
        wrap.classList.toggle('d-none', !show);
        // disable/enable inputs inside so hidden fields don't submit
        wrap.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = !show;
        });
    };

    // Bootstrap validation styling
    form.addEventListener('submit', async function(e){
        e.preventDefault();
        clearAlert();

        // native HTML5 validation
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            showAlert('Please correct the highlighted fields and try again.', 'warning');
            return;
        }

        // collect data
        const fd = new FormData(form);
        const payload = {};
        fd.forEach((value, key) => {
            if (payload[key] !== undefined) {
                if (Array.isArray(payload[key])) payload[key].push(value);
                else payload[key] = [payload[key], value];
            } else {
                payload[key] = value;
            }
        });

        setSubmitting(true);
        try {
            const res = await fetch("{{ route('forms.submit', $form->id) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const result = await res.json().catch(() => ({}));

            if (res.ok) {
                showAlert('Form submitted successfully!', 'success');
                form.reset();
                form.classList.remove('was-validated');
                // Re-evaluate conditional logic after reset
                initConditionalLogic();
            } else {
                const msg = result?.errors ? JSON.stringify(result.errors) : (result?.message || 'Submission failed.');
                showAlert(msg, 'danger');
            }
        } catch (err) {
            showAlert('Something went wrong. Please try again.', 'danger');
            console.error(err);
        } finally {
            setSubmitting(false);
        }
    }, false);

    // Initialize conditional logic on load
    initConditionalLogic();
})();
</script>
@endsection
