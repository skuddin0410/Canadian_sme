@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <h2>{{ $form->title }}</h2>
    <p>{{ $form->description }}</p>

    <form id="dynamic-form">
        @foreach($form->form_data as $field)
            <div class="mb-3" id="field-{{ $loop->index }}">
                <label>{{ $field['label'] }}</label>

                @php $type = $field['type']; @endphp

                @switch($type)
                    @case('text')
                    <input type="text" name="{{ $field['label'] }}" class="form-control" 
                        @if(in_array('required', $field['validation'] ?? [])) required @endif>
                    @break

                    @case('email')
                    <input type="email" name="{{ $field['label'] }}" class="form-control"
                        @if(in_array('required', $field['validation'] ?? [])) required @endif>
                    @break

                    @case('number')
                    <input type="number" name="{{ $field['label'] }}" class="form-control"
                        min="{{ $field['min'] ?? '' }}" max="{{ $field['max'] ?? '' }}"
                        @if(in_array('required', $field['validation'] ?? [])) required @endif>
                    @break

                    @case('date')
                    <input type="date" name="{{ $field['label'] }}" class="form-control"
                        @if(in_array('required', $field['validation'] ?? [])) required @endif>
                    @break

                    @case('textarea')
                    <textarea name="{{ $field['label'] }}" class="form-control"
                        @if(in_array('required', $field['validation'] ?? [])) required @endif></textarea>
                    @break

                    @case('select')
                    <select name="{{ $field['label'] }}" class="form-select"
                        @if(in_array('required', $field['validation'] ?? [])) required @endif>
                        @foreach($field['options'] ?? [] as $opt)
                            <option>{{ $opt }}</option>
                        @endforeach
                    </select>
                    @break

                    @case('radio')
                        @foreach($field['options'] ?? [] as $opt)
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="{{ $field['label'] }}" value="{{ $opt }}">
                            <label class="form-check-label">{{ $opt }}</label>
                        </div>
                        @endforeach
                    @break

                    @case('checkbox')
                        @foreach($field['options'] ?? [] as $opt)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="{{ $field['label'] }}[]" value="{{ $opt }}">
                            <label class="form-check-label">{{ $opt }}</label>
                        </div>
                        @endforeach
                    @break
                @endswitch
            </div>
        @endforeach

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('dynamic-form').addEventListener('submit', async function(e){
    e.preventDefault();

    const formData = new FormData(this);
    const data = {};
    formData.forEach((value, key) => {
        if(data[key]){
            if(Array.isArray(data[key])) data[key].push(value);
            else data[key] = [data[key], value];
        } else data[key] = value;
    });

    const response = await fetch("{{ route('forms.submit', $form->id) }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    });

    const result = await response.json();
    if(response.ok){
        alert('Form submitted successfully!');
        this.reset();
    } else {
        alert('Submission failed: ' + JSON.stringify(result.errors));
    }
});
</script>
@endsection
