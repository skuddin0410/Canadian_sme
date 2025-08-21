// public/js/form-builder.js

class FormBuilder {
    constructor() {
        this.formData = [];
        this.selectedField = null;
        this.fieldCounter = 0;
        this.validationRules = {};
        this.conditionalLogic = [];
        
        this.initializeEventListeners();
        this.initializeDragAndDrop();
    }

    initializeEventListeners() {
        // Save form button
        document.getElementById('save-form').addEventListener('click', () => {
            this.saveForm();
        });

        // Preview form button
        document.getElementById('preview-form').addEventListener('click', () => {
            this.previewForm();
        });

        // Close preview modal
        document.getElementById('close-preview').addEventListener('click', () => {
            document.getElementById('preview-modal').classList.add('hidden');
        });

        // Click outside modal to close
        document.getElementById('preview-modal').addEventListener('click', (e) => {
            if (e.target.id === 'preview-modal') {
                document.getElementById('preview-modal').classList.add('hidden');
            }
        });
    }

    initializeDragAndDrop() {
        const formElements = document.getElementById('form-elements');
        const formCanvas = document.getElementById('form-canvas');

        // Make form elements draggable
        new Sortable(formElements, {
            group: {
                name: 'form-builder',
                pull: 'clone',
                put: false
            },
            animation: 150,
            sort: false
        });

        // Make form canvas droppable and sortable
        new Sortable(formCanvas, {
            group: {
                name: 'form-builder',
                pull: false,
                put: true
            },
            animation: 150,
            onAdd: (evt) => {
                this.addFormField(evt);
            },
            onUpdate: (evt) => {
                this.updateFieldOrder();
            }
        });
    }

    addFormField(evt) {
        const fieldType = evt.item.getAttribute('data-field-type');
        this.fieldCounter++;
        
        const fieldId = `field_${this.fieldCounter}`;
        const fieldData = {
            id: fieldId,
            type: fieldType,
            name: fieldId,
            label: this.getDefaultLabel(fieldType),
            required: false,
            placeholder: '',
            options: fieldType === 'select' || fieldType === 'radio' || fieldType === 'checkbox' ? 
                     [{ label: 'Option 1', value: 'option1' }] : null,
            maxLength: null,
            validation: {}
        };

        // Remove the dragged element and create the actual form field
        evt.item.remove();
        this.createFormField(fieldData);
        this.formData.push(fieldData);
        this.hideEmptyState();
    }

    getDefaultLabel(fieldType) {
        const labels = {
            text: 'Text Input',
            textarea: 'Textarea',
            select: 'Select Option',
            radio: 'Radio Buttons',
            checkbox: 'Checkboxes',
            email: 'Email Address',
            number: 'Number',
            date: 'Date'
        };
        return labels[fieldType] || 'Field';
    }

    createFormField(fieldData) {
        const formCanvas = document.getElementById('form-canvas');
        const fieldElement = document.createElement('div');
        fieldElement.className = 'form-field';
        fieldElement.setAttribute('data-field-id', fieldData.id);
        
        fieldElement.innerHTML = `
            <div class="field-actions">
                <button class="text-blue-600 hover:text-blue-800 mr-2" onclick="formBuilder.editField('${fieldData.id}')">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="text-red-600 hover:text-red-800" onclick="formBuilder.deleteField('${fieldData.id}')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            ${this.generateFieldHTML(fieldData)}
        `;

        formCanvas.appendChild(fieldElement);
    }

    generateFieldHTML(fieldData) {
        let html = `
            <label class="block text-sm font-medium text-gray-700 mb-1">
                ${fieldData.label}${fieldData.required ? ' <span class="text-red-500">*</span>' : ''}
            </label>
        `;

        switch(fieldData.type) {
            case 'text':
            case 'email':
            case 'number':
            case 'date':
                html += `<input type="${fieldData.type}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50" 
                               placeholder="${fieldData.placeholder || ''}" 
                               ${fieldData.maxLength ? `maxlength="${fieldData.maxLength}"` : ''}
                               disabled>`;
                break;
                
            case 'textarea':
                html += `<textarea class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50" 
                                  rows="3" 
                                  placeholder="${fieldData.placeholder || ''}" 
                                  ${fieldData.maxLength ? `maxlength="${fieldData.maxLength}"` : ''}
                                  disabled></textarea>`;
                break;
                
            case 'select':
                html += `<select class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50" disabled>`;
                if (fieldData.options) {
                    fieldData.options.forEach(option => {
                        html += `<option value="${option.value}">${option.label}</option>`;
                    });
                }
                html += `</select>`;
                break;
                
            case 'radio':
                if (fieldData.options) {
                    html += '<div class="space-y-2">';
                    fieldData.options.forEach(option => {
                        html += `
                            <label class="flex items-center">
                                <input type="radio" class="mr-2" disabled>
                                ${option.label}
                            </label>
                        `;
                    });
                    html += '</div>';
                }
                break;
                
            case 'checkbox':
                if (fieldData.options) {
                    html += '<div class="space-y-2">';
                    fieldData.options.forEach(option => {
                        html += `
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2" disabled>
                                ${option.label}
                            </label>
                        `;
                    });
                    html += '</div>';
                }
                break;
        }

        return html;
    }

    editField(fieldId) {
        const fieldData = this.formData.find(field => field.id === fieldId);
        if (!fieldData) return;

        this.selectedField = fieldData;
        this.showFieldProperties(fieldData);
    }

    showFieldProperties(fieldData) {
        const propertiesContent = document.getElementById('properties-content');
        
        let optionsHTML = '';
        if (fieldData.type === 'select' || fieldData.type === 'radio' || fieldData.type === 'checkbox') {
            optionsHTML = `
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Options</label>
                    <div id="options-list">
                        ${fieldData.options.map((option, index) => `
                            <div class="flex gap-2 mb-2">
                                <input type="text" value="${option.label}" 
                                       class="flex-1 border border-gray-300 rounded px-2 py-1"
                                       onchange="formBuilder.updateOption(${index}, 'label', this.value)">
                                <input type="text" value="${option.value}" 
                                       class="flex-1 border border-gray-300 rounded px-2 py-1"
                                       onchange="formBuilder.updateOption(${index}, 'value', this.value)">
                                <button onclick="formBuilder.removeOption(${index})" 
                                        class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `).join('')}
                    </div>
                    <button onclick="formBuilder.addOption()" 
                            class="text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-plus mr-1"></i>Add Option
                    </button>
                </div>
            `;
        }

        propertiesContent.innerHTML = `
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Label</label>
                    <input type="text" value="${fieldData.label}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2"
                           onchange="formBuilder.updateFieldProperty('label', this.value)">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Field Name</label>
                    <input type="text" value="${fieldData.name}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2"
                           onchange="formBuilder.updateFieldProperty('name', this.value)">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Placeholder</label>
                    <input type="text" value="${fieldData.placeholder || ''}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2"
                           onchange="formBuilder.updateFieldProperty('placeholder', this.value)">
                </div>
                
                ${fieldData.type === 'text' || fieldData.type === 'textarea' ? `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Length</label>
                        <input type="number" value="${fieldData.maxLength || ''}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2"
                               onchange="formBuilder.updateFieldProperty('maxLength', this.value)">
                    </div>
                ` : ''}
                
                ${optionsHTML}
                
                <div class="flex items-center">
                    <input type="checkbox" ${fieldData.required ? 'checked' : ''} 
                           class="mr-2" onchange="formBuilder.updateFieldProperty('required', this.checked)">
                    <label class="text-sm font-medium text-gray-700">Required Field</label>
                </div>
                
                <div class="border-t pt-4">
                    <h4 class="font-medium text-gray-900 mb-3">Conditional Logic</h4>
                    <div id="conditional-rules">
                        ${this.renderConditionalRules(fieldData.id)}
                    </div>
                    <button onclick="formBuilder.addConditionalRule('${fieldData.id}')" 
                            class="text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-plus mr-1"></i>Add Conditional Rule
                    </button>
                </div>
            </div>
        `;
    }

    updateFieldProperty(property, value) {
        if (!this.selectedField) return;
        
        this.selectedField[property] = value;
        this.updateFieldDisplay(this.selectedField);
        this.updateValidationRules();
    }

    updateOption(index, property, value) {
        if (!this.selectedField || !this.selectedField.options) return;
        
        this.selectedField.options[index][property] = value;
        this.updateFieldDisplay(this.selectedField);
    }

    addOption() {
        if (!this.selectedField || !this.selectedField.options) return;
        
        const newIndex = this.selectedField.options.length + 1;
        this.selectedField.options.push({
            label: `Option ${newIndex}`,
            value: `option${newIndex}`
        });
        
        this.showFieldProperties(this.selectedField);
        this.updateFieldDisplay(this.selectedField);
    }

    removeOption(index) {
        if (!this.selectedField || !this.selectedField.options) return;
        
        this.selectedField.options.splice(index, 1);
        this.showFieldProperties(this.selectedField);
        this.updateFieldDisplay(this.selectedField);
    }

    updateFieldDisplay(fieldData) {
        const fieldElement = document.querySelector(`[data-field-id="${fieldData.id}"]`);
        if (!fieldElement) return;
        
        const actionsHTML = fieldElement.querySelector('.field-actions').outerHTML;
        fieldElement.innerHTML = actionsHTML + this.generateFieldHTML(fieldData);
    }

    renderConditionalRules(targetFieldId) {
        const rules = this.conditionalLogic.filter(rule => rule.target_field === targetFieldId);
        
        return rules.map((rule, index) => `
            <div class="conditional-rule">
                <div class="grid grid-cols-2 gap-2 mb-2">
                    <select onchange="formBuilder.updateConditionalRule(${index}, 'condition', this.value)"
                            class="border border-gray-300 rounded px-2 py-1">
                        <option value="show" ${rule.condition === 'show' ? 'selected' : ''}>Show</option>
                        <option value="hide" ${rule.condition === 'hide' ? 'selected' : ''}>Hide</option>
                    </select>
                    
                    <select onchange="formBuilder.updateConditionalRule(${index}, 'source_field', this.value)"
                            class="border border-gray-300 rounded px-2 py-1">
                        <option value="">Select Field</option>
                        ${this.formData.map(field => `
                            <option value="${field.id}" ${rule.source_field === field.id ? 'selected' : ''}>
                                ${field.label}
                            </option>
                        `).join('')}
                    </select>
                </div>
                
                <div class="grid grid-cols-3 gap-2">
                    <select onchange="formBuilder.updateConditionalRule(${index}, 'operator', this.value)"
                            class="border border-gray-300 rounded px-2 py-1">
                        <option value="==" ${rule.operator === '==' ? 'selected' : ''}>Equals</option>
                        <option value="!=" ${rule.operator === '!=' ? 'selected' : ''}>Not Equals</option>
                        <option value="contains" ${rule.operator === 'contains' ? 'selected' : ''}>Contains</option>
                        <option value="not_contains" ${rule.operator === 'not_contains' ? 'selected' : ''}>Not Contains</option>
                    </select>
                    
                    <input type="text" value="${rule.value || ''}" 
                           placeholder="Value"
                           class="border border-gray-300 rounded px-2 py-1"
                           onchange="formBuilder.updateConditionalRule(${index}, 'value', this.value)">
                    
                    <button onclick="formBuilder.removeConditionalRule(${index})" 
                            class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }

    addConditionalRule(targetFieldId) {
        const rule = {
            target_field: targetFieldId,
            source_field: '',
            condition: 'show',
            operator: '==',
            value: ''
        };
        
        this.conditionalLogic.push(rule);
        this.showFieldProperties(this.selectedField);
    }

    updateConditionalRule(index, property, value) {
        if (this.conditionalLogic[index]) {
            this.conditionalLogic[index][property] = value;
        }
    }

    removeConditionalRule(index) {
        this.conditionalLogic.splice(index, 1);
        this.showFieldProperties(this.selectedField);
    }

    deleteField(fieldId) {
        // Remove from form data
        this.formData = this.formData.filter(field => field.id !== fieldId);
        
        // Remove from DOM
        const fieldElement = document.querySelector(`[data-field-id="${fieldId}"]`);
        if (fieldElement) {
            fieldElement.remove();
        }
        
        // Remove related conditional logic
        this.conditionalLogic = this.conditionalLogic.filter(rule => 
            rule.target_field !== fieldId && rule.source_field !== fieldId
        );
        
        // Clear properties panel if this field was selected
        if (this.selectedField && this.selectedField.id === fieldId) {
            this.selectedField = null;
            document.getElementById('properties-content').innerHTML = 
                '<p class="text-gray-500 text-center py-8">Select a field to edit properties</p>';
        }
        
        // Show empty state if no fields
        if (this.formData.length === 0) {
            this.showEmptyState();
        }
    }

    updateFieldOrder() {
        const fieldElements = document.querySelectorAll('#form-canvas .form-field');
        const newOrder = [];
        
        fieldElements.forEach(element => {
            const fieldId = element.getAttribute('data-field-id');
            const fieldData = this.formData.find(field => field.id === fieldId);
            if (fieldData) {
                newOrder.push(fieldData);
            }
        });
        
        this.formData = newOrder;
    }

    updateValidationRules() {
        this.validationRules = {};
        
        this.formData.forEach(field => {
            const rules = [];
            
            if (field.required) {
                rules.push('required');
            }
            
            if (field.type === 'email') {
                rules.push('email');
            }
            
            if (field.type === 'number') {
                rules.push('numeric');
            }
            
            if (field.maxLength) {
                rules.push(`max:${field.maxLength}`);
            }
            
            if (rules.length > 0) {
                this.validationRules[field.name] = rules.join('|');
            }
        });
    }

    hideEmptyState() {
        const emptyState = document.getElementById('empty-state');
        if (emptyState) {
            emptyState.style.display = 'none';
        }
    }

    showEmptyState() {
        const emptyState = document.getElementById('empty-state');
        if (emptyState) {
            emptyState.style.display = 'block';
        }
    }

    previewForm() {
        const modal = document.getElementById('preview-modal');
        const content = document.getElementById('preview-content');
        
        const formTitle = document.getElementById('form-title').value || 'Form Preview';
        const formDescription = document.getElementById('form-description').value;
        
        let previewHTML = `
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900">${formTitle}</h2>
                ${formDescription ? `<p class="text-gray-600 mt-2">${formDescription}</p>` : ''}
            </div>
            <form id="preview-form-element">
        `;
        
        this.formData.forEach(field => {
            previewHTML += `<div class="mb-4">${this.generatePreviewFieldHTML(field)}</div>`;
        });
        
        previewHTML += `
                <div class="mt-6">
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700">
                        Submit Form
                    </button>
                </div>
            </form>
        `;
        
        content.innerHTML = previewHTML;
        modal.classList.remove('hidden');
    }

    generatePreviewFieldHTML(fieldData) {
        let html = `
            <label class="block text-sm font-medium text-gray-700 mb-1">
                ${fieldData.label}${fieldData.required ? ' <span class="text-red-500">*</span>' : ''}
            </label>
        `;

        switch(fieldData.type) {
            case 'text':
            case 'email':
            case 'number':
            case 'date':
                html += `<input type="${fieldData.type}" name="${fieldData.name}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2" 
                               placeholder="${fieldData.placeholder || ''}" 
                               ${fieldData.required ? 'required' : ''}
                               ${fieldData.maxLength ? `maxlength="${fieldData.maxLength}"` : ''}>`;
                break;
                
            case 'textarea':
                html += `<textarea name="${fieldData.name}" rows="4"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2" 
                                  placeholder="${fieldData.placeholder || ''}" 
                                  ${fieldData.required ? 'required' : ''}
                                  ${fieldData.maxLength ? `maxlength="${fieldData.maxLength}"` : ''}></textarea>`;
                break;
                
            case 'select':
                html += `<select name="${fieldData.name}" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                ${fieldData.required ? 'required' : ''}>`;
                html += '<option value="">Choose an option</option>';
                if (fieldData.options) {
                    fieldData.options.forEach(option => {
                        html += `<option value="${option.value}">${option.label}</option>`;
                    });
                }
                html += `</select>`;
                break;
                
            case 'radio':
                if (fieldData.options) {
                    html += '<div class="space-y-2">';
                    fieldData.options.forEach(option => {
                        html += `
                            <label class="flex items-center">
                                <input type="radio" name="${fieldData.name}" value="${option.value}" 
                                       class="mr-2" ${fieldData.required ? 'required' : ''}>
                                ${option.label}
                            </label>
                        `;
                    });
                    html += '</div>';
                }
                break;
                
            case 'checkbox':
                if (fieldData.options) {
                    html += '<div class="space-y-2">';
                    fieldData.options.forEach(option => {
                        html += `
                            <label class="flex items-center">
                                <input type="checkbox" name="${fieldData.name}[]" value="${option.value}" 
                                       class="mr-2">
                                ${option.label}
                            </label>
                        `;
                    });
                    html += '</div>';
                }
                break;
        }

        return html;
    }

    saveForm() {
        const formTitle = document.getElementById('form-title').value;
        const formDescription = document.getElementById('form-description').value;
        
        if (!formTitle) {
            alert('Please enter a form title');
            return;
        }
        
        if (this.formData.length === 0) {
            alert('Please add at least one field to the form');
            return;
        }
        
        this.updateValidationRules();
        
        const formData = {
            title: formTitle,
            description: formDescription,
            form_data: this.formData,
            validation_rules: this.validationRules,
            conditional_logic: this.conditionalLogic
        };
        
        fetch('/form-builder/forms', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.form) {
                alert('Form saved successfully!');
                console.log('Form ID:', data.form.id);
            } else if (data.errors) {
                console.error('Validation errors:', data.errors);
                alert('Please fix the validation errors and try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving form. Please try again.');
        });
    }
}

// Initialize the form builder when the page loads
let formBuilder;
document.addEventListener('DOMContentLoaded', function() {
    formBuilder = new FormBuilder();
});