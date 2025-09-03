@extends('layouts.admin')

@section('title', $form->title)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <div class="row">
    <div class="col-xl">
    <div class="card mb-4">
        <div class="card-body"> 
        <div class="">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ $form->title }}</h1>
                @if($form->description)
                    <p class="text-gray-600 mt-2">{{ $form->description }}</p>
                @endif
            </div>
            
            <form id="dynamic-form" data-form-id="{{ $form->id }}">
                @csrf
                <div id="form-fields"></div>
                
                <div class="mt-6">
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-paper-plane me-2"></i>Submit Form
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
    </div>
    </div> 
</div>

<script>
const formData = @json($form->form_data);
const conditionalLogic = @json($form->conditional_logic);

// Render form dynamically
document.addEventListener('DOMContentLoaded', function() {
    renderForm(formData);
    attachConditionalLogic();
    handleFormSubmission();
});

function renderForm(fields) {
    const container = document.getElementById('form-fields');
    container.innerHTML = '';

    fields.forEach(field => {
        const fieldHtml = createFieldHtml(field);
        container.appendChild(fieldHtml);
    });
}

function createFieldHtml(field) {
    const wrapper = document.createElement('div');
    wrapper.className = 'mb-4';
    wrapper.setAttribute('data-field-id', field.id);

    let fieldHtml = `
        <label class="block text-sm font-medium text-gray-700 mb-1">
            ${field.label}${field.required ? ' <span class="text-red-500">*</span>' : ''}
        </label>
    `;
    switch(field.type) {
        case 'text':
        case 'email':
        case 'number':
        case 'date':
            fieldHtml += `<input type="${field.type}" name="${field.name}" id="${field.id}" 
                                 class="w-full border border-gray-300 rounded-lg px-3 py-2" 
                                 ${field.required ? 'required' : ''}
                                 ${field.placeholder ? `placeholder="${field.placeholder}"` : ''}
                                 ${field.maxLength ? `maxlength="${field.maxLength}"` : ''}>`;
            break;
            
        case 'textarea':
            fieldHtml += `<textarea name="${field.name}" id="${field.id}" rows="4"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                   ${field.required ? 'required' : ''}
                                   ${field.placeholder ? `placeholder="${field.placeholder}"` : ''}
                                   ${field.maxLength ? `maxlength="${field.maxLength}"` : ''}></textarea>`;
            break;
            
        case 'select':
            fieldHtml += `<select name="${field.name}" id="${field.id}" 
                                 class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                 ${field.required ? 'required' : ''}>`;
            fieldHtml += '<option value="">Choose an option</option>';
            if (field.options) {
                field.options.forEach(option => {
                    fieldHtml += `<option value="${option}">${option}</option>`;
                });
            }
            fieldHtml += `</select>`;
            break;
            
        case 'radio':
            if (field.options) {
                fieldHtml += '<div class="space-y-2">';
                field.options.forEach((option, index) => {
                    console.log(option)
                    fieldHtml += `
                        <label class="flex items-center">
                            <input type="radio" name="${field.name}" value="${option.value}" 
                                   class="mr-2" ${field.required ? 'required' : ''}>
                            ${option}
                        </label>
                    `;
                });
                fieldHtml += '</div>';
            }
            break;
            
        case 'checkbox':
            if (field.options) {
                fieldHtml += '<div class="space-y-2">';
                field.options.forEach((option, index) => {
                    fieldHtml += `
                        <label class="flex items-center">
                            <input type="checkbox" name="${field.name}[]" value="${option.value}" 
                                   class="mr-2">
                            ${option}
                        </label>
                    `;
                });
                fieldHtml += '</div>';
            }
            break;
    }

    wrapper.innerHTML = fieldHtml;
    return wrapper;
}

function attachConditionalLogic() {
    if (!conditionalLogic) return;

    conditionalLogic.forEach(rule => {
        const sourceField = document.getElementById(rule.source_field);
        if (sourceField) {
            sourceField.addEventListener('change', function() {
                evaluateCondition(rule);
            });
        }
    });
}

function evaluateCondition(rule) {
    const sourceField = document.getElementById(rule.source_field);
    const targetField = document.querySelector(`[data-field-id="${rule.target_field}"]`);
    
    if (!sourceField || !targetField) return;

    const sourceValue = sourceField.value;
    let conditionMet = false;

    switch(rule.operator) {
        case '==':
            conditionMet = sourceValue == rule.value;
            break;
        case '!=':
            conditionMet = sourceValue != rule.value;
            break;
        case 'contains':
            conditionMet = sourceValue.toLowerCase().includes(rule.value.toLowerCase());
            break;
    }

    if (rule.condition === 'show') {
        targetField.style.display = conditionMet ? 'block' : 'none';
    } else if (rule.condition === 'hide') {
        targetField.style.display = conditionMet ? 'none' : 'block';
    }
}

function handleFormSubmission() {
    document.getElementById('dynamic-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const formId = this.dataset.formId;

        fetch(`/form-builder/forms/${formId}/submit`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                alert('Form submitted successfully!');
                this.reset();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error submitting form. Please try again.');
        });
    });
}
</script>
@endsection
