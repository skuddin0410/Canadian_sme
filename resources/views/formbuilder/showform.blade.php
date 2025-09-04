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
                                $idBase      = "f{$index}_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
                                $isRequired  = in_array('required', $field['validation'] ?? []);
                                $min         = $field['min'] ?? null;
                                $max         = $field['max'] ?? null;
                                $logic       = $field['conditional_logic'] ?? null;
                                $hasLenRule  = in_array($type, ['text','email','textarea','number']) && ($min || $max);

                                // Fixed: More appropriate autocomplete values
                                $autocomplete = match($type) {
                                    'text' => 'name',
                                    'email' => 'email',
                                    'number' => 'off',
                                    'date' => 'bday',
                                    'password' => 'new-password',
                                    'textarea' => 'off',
                                    'select' => 'off',
                                    'radio' => 'off',
                                    'checkbox' => 'off',
                                    default => 'off',
                                };
                            @endphp

                            <div class="mb-3 field-wrapper"
                                 id="field-{{ $index }}"
                                 @if(!empty($logic)) data-logic='@json($logic)' @endif
                                 data-target-label="{{ $label }}">

                                @switch($type)
                                    @case('text')
                                    @case('email')
                                    @case('number')
                                    @case('date')
                                    @case('password')
                                        <label class="form-label" for="{{$idBase}}">
                                            {{ $label }}
                                            @if($isRequired) <span class="text-danger" aria-hidden="true">*</span> @endif
                                        </label>
                                        <input
                                            type="{{ $type }}"
                                            id="{{ $idBase }}"
                                            name="{{ $name }}"
                                            class="form-control"
                                            placeholder="Enter {{ strtolower($label) }}"
                                            autocomplete="{{ $autocomplete }}"
                                            @if($isRequired) required @endif
                                            @if($min && in_array($type,['text','email','textarea'])) minlength="{{ $min }}" @endif
                                            @if($max && in_array($type,['text','email','textarea'])) maxlength="{{ $max }}" @endif
                                            @if(!is_null($min) && $type==='number') min="{{ $min }}" @endif
                                            @if(!is_null($max) && $type==='number') max="{{ $max }}" @endif
                                        >
                                        @break

                                    @case('textarea')
                                        <label class="form-label" for="{{$idBase}}">
                                            {{ $label }}
                                            @if($isRequired) <span class="text-danger" aria-hidden="true">*</span> @endif
                                        </label>
                                        <textarea
                                            id="{{ $idBase }}"
                                            name="{{ $name }}"
                                            class="form-control"
                                            rows="4"
                                            placeholder="Type {{ strtolower($label) }} here"
                                            autocomplete="{{ $autocomplete }}"
                                            @if($isRequired) required @endif
                                            @if($min) minlength="{{ $min }}" @endif
                                            @if($max) maxlength="{{ $max }}" @endif
                                        ></textarea>
                                        @break

                                    @case('select')
                                        <label class="form-label" for="{{$idBase}}">
                                            {{ $label }}
                                            @if($isRequired) <span class="text-danger" aria-hidden="true">*</span> @endif
                                        </label>
                                        <select
                                            id="{{ $idBase }}"
                                            name="{{ $name }}"
                                            class="form-select"
                                            autocomplete="{{ $autocomplete }}"
                                            @if($isRequired) required @endif
                                        >
                                            <option value="" selected disabled>Choose an option</option>
                                            @foreach($field['options'] ?? [] as $opt)
                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                        @break

                                    @case('radio')
                                        {{-- Fixed: Proper fieldset structure with legend --}}
                                        <fieldset class="border-0 p-0">
                                            <legend class="form-label mb-2">
                                                {{ $label }}
                                                @if($isRequired) <span class="text-danger" aria-hidden="true">*</span> @endif
                                            </legend>
                                            @foreach($field['options'] ?? [] as $i => $opt)
                                                @php $rid = "{$idBase}_r{$i}"; @endphp
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                           id="{{ $rid }}"
                                                           name="{{ $name }}"
                                                           value="{{ $opt }}"
                                                           @if($isRequired) required @endif>
                                                    <label class="form-check-label" for="{{ $rid }}">{{ $opt }}</label>
                                                </div>
                                            @endforeach
                                        </fieldset>
                                        @break

                                    @case('checkbox')
                                        {{-- Fixed: Proper fieldset structure with legend --}}
                                        <fieldset class="border-0 p-0">
                                            <legend class="form-label mb-2">
                                                {{ $label }}
                                                @if($isRequired) <span class="text-danger" aria-hidden="true">*</span> @endif
                                            </legend>
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
    // Add this debugging version to your script section to identify the issue

(function(){
    const form = document.getElementById('dynamic-form');
    const alertBox = document.getElementById('form-alert');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnSpin = submitBtn.querySelector('.spinner-border');

    // Debug: Check if elements exist
    console.log('Form element:', form);
    console.log('Alert box:', alertBox);
    console.log('Submit button:', submitBtn);

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

    const slug = (s='') => (s || '')
        .toString()
        .normalize('NFKD').replace(/[\u0300-\u036F]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g,'_')
        .replace(/^_+|_+$/g,'')
        .replace(/_+/g,'_');

    const toggleWrapper = (wrap, show) => {
        wrap.classList.toggle('d-none', !show);
        wrap.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = !show;
            if (!show) {
                if (el.type === 'radio' || el.type === 'checkbox') el.checked = false;
                else el.value = '';
            }
        });
    };

    const initConditionalLogic = () => {
        const wrappers = [...document.querySelectorAll('.field-wrapper[data-logic]')];
        const sourcesMap = {};

        wrappers.forEach(wrap => {
            try {
                const logic = JSON.parse(wrap.dataset.logic || 'null');
                if (!logic || !logic.condition || logic.condition === 'none') return;

                const sf = logic.source_field || '';
                const dash = sf.indexOf('-');
                const srcLabel = dash >= 0 ? sf.slice(dash + 1) : sf;
                const srcName = slug(srcLabel);

                if (!sourcesMap[srcName]) sourcesMap[srcName] = [];
                sourcesMap[srcName].push({wrap, logic, srcName});
            } catch(e){ console.error(e); }
        });

        Object.keys(sourcesMap).forEach(srcName => {
            const targets = sourcesMap[srcName];

            const listener = () => {
                const curVal = getFieldValue(srcName);
                targets.forEach(({wrap, logic}) => {
                    const ok = evalOp(curVal, logic.operator, logic.value);
                    const shouldShow = logic.condition === 'show' ? ok : !ok;
                    toggleWrapper(wrap, shouldShow);
                });
            };

            const elems = document.querySelectorAll(`[name="${srcName}"], [name="${srcName}[]"]`);
            elems.forEach(el => {
                el.addEventListener('change', listener);
                if (['text','email','number','date','textarea'].includes((el.type||el.tagName).toLowerCase())) {
                    el.addEventListener('input', listener);
                }
            });

            listener(); // run once
        });
    };

    const getFieldValue = (name) => {
        const el = document.querySelector(`[name="${name}"], [name="${name}[]"]`);
        if (!el) return null;
        if (el.type === 'checkbox') {
            return [...document.querySelectorAll(`[name="${name}[]"]:checked`)].map(e => e.value);
        } else if (el.type === 'radio') {
            const checked = document.querySelector(`[name="${name}"]:checked`);
            return checked ? checked.value : null;
        } else {
            return el.value;
        }
    };

    const evalOp = (val, operator, compare) => {
        switch(operator){
            case '==': return val == compare;
            case '!=': return val != compare;
            case '>': return val > compare;
            case '<': return val < compare;
            case '>=': return val >= compare;
            case '<=': return val <= compare;
            case 'in': return (Array.isArray(compare) ? compare : [compare]).includes(val);
            case 'not_in': return !(Array.isArray(compare) ? compare : [compare]).includes(val);
            default: return false;
        }
    };

    form.addEventListener('submit', async function(e){
        e.preventDefault();
        console.log('Form submit event triggered');
        
        clearAlert();

        if (!form.checkValidity()) {
            console.log('Form validation failed');
            form.classList.add('was-validated');
            showAlert('Please correct the highlighted fields and try again.', 'warning');
            return;
        }

        console.log('Form validation passed');

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

        console.log('Payload to send:', payload);
        console.log('Route URL:', "{{ route('forms.submit', $form->id) }}");
        console.log('CSRF Token:', '{{ csrf_token() }}');

        setSubmitting(true);
        
        try {
            console.log('Making fetch request...');
            
            const res = await fetch("{{ route('forms.submit', $form->id) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            console.log('Response received:', res);
            console.log('Response status:', res.status);
            console.log('Response ok:', res.ok);

            const result = await res.json().catch((err) => {
                console.error('JSON parse error:', err);
                return {};
            });

            console.log('Parsed result:', result);

            if (res.ok) {
                showAlert('Form submitted successfully!', 'success');
                form.reset();
                form.classList.remove('was-validated');
                initConditionalLogic();
            } else {
                const msg = result?.errors ? JSON.stringify(result.errors) : (result?.message || 'Submission failed.');
                showAlert(msg, 'danger');
            }
        } catch (err) {
            console.error('Fetch error:', err);
            showAlert('Something went wrong. Please try again.', 'danger');
        } finally {
            setSubmitting(false);
        }
    });

    // Debug: Check if event listener is attached
    console.log('Form submit event listener attached');

    initConditionalLogic();
})();
// (function(){
//     const form = document.getElementById('dynamic-form');
//     const alertBox = document.getElementById('form-alert');
//     const submitBtn = document.getElementById('submit-btn');
//     const btnText = submitBtn.querySelector('.btn-text');
//     const btnSpin = submitBtn.querySelector('.spinner-border');

//     const showAlert = (msg, type='success') => {
//         alertBox.className = `alert alert-${type}`;
//         alertBox.textContent = msg;
//         alertBox.classList.remove('d-none');
//         alertBox.scrollIntoView({behavior: 'smooth', block: 'center'});
//     };

//     const clearAlert = () => {
//         alertBox.className = 'alert d-none';
//         alertBox.textContent = '';
//     };

//     const setSubmitting = (is) => {
//         submitBtn.disabled = is;
//         btnText.classList.toggle('d-none', is);
//         btnSpin.classList.toggle('d-none', !is);
//     };

//     const slug = (s='') => (s || '')
//         .toString()
//         .normalize('NFKD').replace(/[\u0300-\u036F]/g, '')
//         .toLowerCase()
//         .replace(/[^a-z0-9]+/g,'_')
//         .replace(/^_+|_+$/g,'')
//         .replace(/_+/g,'_');

//     const toggleWrapper = (wrap, show) => {
//         wrap.classList.toggle('d-none', !show);
//         wrap.querySelectorAll('input, select, textarea').forEach(el => {
//             el.disabled = !show;
//             if (!show) {
//                 if (el.type === 'radio' || el.type === 'checkbox') el.checked = false;
//                 else el.value = '';
//             }
//         });
//     };

//     const initConditionalLogic = () => {
//         const wrappers = [...document.querySelectorAll('.field-wrapper[data-logic]')];
//         const sourcesMap = {};

//         wrappers.forEach(wrap => {
//             try {
//                 const logic = JSON.parse(wrap.dataset.logic || 'null');
//                 if (!logic || !logic.condition || logic.condition === 'none') return;

//                 const sf = logic.source_field || '';
//                 const dash = sf.indexOf('-');
//                 const srcLabel = dash >= 0 ? sf.slice(dash + 1) : sf;
//                 const srcName = slug(srcLabel);

//                 if (!sourcesMap[srcName]) sourcesMap[srcName] = [];
//                 sourcesMap[srcName].push({wrap, logic, srcName});
//             } catch(e){ console.error(e); }
//         });

//         Object.keys(sourcesMap).forEach(srcName => {
//             const targets = sourcesMap[srcName];

//             const listener = () => {
//                 const curVal = getFieldValue(srcName);
//                 targets.forEach(({wrap, logic}) => {
//                     const ok = evalOp(curVal, logic.operator, logic.value);
//                     const shouldShow = logic.condition === 'show' ? ok : !ok;
//                     toggleWrapper(wrap, shouldShow);
//                 });
//             };

//             const elems = document.querySelectorAll(`[name="${srcName}"], [name="${srcName}[]"]`);
//             elems.forEach(el => {
//                 el.addEventListener('change', listener);
//                 if (['text','email','number','date','textarea'].includes((el.type||el.tagName).toLowerCase())) {
//                     el.addEventListener('input', listener);
//                 }
//             });

//             listener(); // run once
//         });
//     };

//     const getFieldValue = (name) => {
//         const el = document.querySelector(`[name="${name}"], [name="${name}[]"]`);
//         if (!el) return null;
//         if (el.type === 'checkbox') {
//             return [...document.querySelectorAll(`[name="${name}[]"]:checked`)].map(e => e.value);
//         } else if (el.type === 'radio') {
//             const checked = document.querySelector(`[name="${name}"]:checked`);
//             return checked ? checked.value : null;
//         } else {
//             return el.value;
//         }
//     };

//     const evalOp = (val, operator, compare) => {
//         switch(operator){
//             case '==': return val == compare;
//             case '!=': return val != compare;
//             case '>': return val > compare;
//             case '<': return val < compare;
//             case '>=': return val >= compare;
//             case '<=': return val <= compare;
//             case 'in': return (Array.isArray(compare) ? compare : [compare]).includes(val);
//             case 'not_in': return !(Array.isArray(compare) ? compare : [compare]).includes(val);
//             default: return false;
//         }
//     };

//     form.addEventListener('submit', async function(e){
//         e.preventDefault();
//         clearAlert();

//         if (!form.checkValidity()) {
//             form.classList.add('was-validated');
//             showAlert('Please correct the highlighted fields and try again.', 'warning');
//             return;
//         }

//         const fd = new FormData(form);
//         const payload = {};
//         fd.forEach((value, key) => {
//             if (payload[key] !== undefined) {
//                 if (Array.isArray(payload[key])) payload[key].push(value);
//                 else payload[key] = [payload[key], value];
//             } else {
//                 payload[key] = value;
//             }
//         });

//         setSubmitting(true);
//         try {
//             const res = await fetch("{{ route('forms.submit', $form->id) }}", {
//                 method: 'POST',
//                 headers: {
//                     'X-CSRF-TOKEN': '{{ csrf_token() }}',
//                     'Accept': 'application/json',
//                     'Content-Type': 'application/json'
//                 },
//                 body: JSON.stringify(payload)
//             });

//             const result = await res.json().catch(() => ({}));

//             if (res.ok) {
//                 showAlert('Form submitted successfully!', 'success');
//                 form.reset();
//                 form.classList.remove('was-validated');
//                 initConditionalLogic();
//             } else {
//                 const msg = result?.errors ? JSON.stringify(result.errors) : (result?.message || 'Submission failed.');
//                 showAlert(msg, 'danger');
//             }
//         } catch (err) {
//             showAlert('Something went wrong. Please try again.', 'danger');
//             console.error(err);
//         } finally {
//             setSubmitting(false);
//         }
//     });

//     initConditionalLogic();
// })();
</script>

{{-- Additional CSS for better fieldset styling --}}
<style>
fieldset.border-0 {
    border: none !important;
    padding: 0 !important;
    margin: 0;
}

fieldset legend.form-label {
    font-size: 1rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
    padding: 0;
    width: auto;
    border: none;
    float: none;
}
</style>
@endsection