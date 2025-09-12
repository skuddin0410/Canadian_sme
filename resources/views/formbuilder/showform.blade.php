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
                    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

                    <form id="dynamic-form" 
                       method="POST" 
                       action="{{ route('forms.submit', $form->id) }}" 
                       novalidate>
                       @csrf
                       @foreach($form->form_data as $index => $field)
                    {{-- <form id="dynamic-form" novalidate>
                        @foreach($form->form_data as $index => $field) --}}
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
            Submit
        </button>
                            {{-- <button type="submit" id="submit-btn" class="btn btn-primary">
                                <span class="btn-text">Submit</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button> --}}
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection



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
