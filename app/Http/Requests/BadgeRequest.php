<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BadgeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'selected_fields' => 'required|array|min:1',
            'selected_fields.*' => 'in:name,company_name,designation,logo,qr_code',
        ];

        // Add conditional validation based on selected fields
        if (in_array('name', $this->selected_fields ?? [])) {
            $rules['name'] = 'required|string|max:255';
        }

        if (in_array('company_name', $this->selected_fields ?? [])) {
            $rules['company_name'] = 'required|string|max:255';
        }

        if (in_array('designation', $this->selected_fields ?? [])) {
            $rules['designation'] = 'required|string|max:255';
        }

        if (in_array('logo', $this->selected_fields ?? [])) {
            $rules['logo'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        if (in_array('qr_code', $this->selected_fields ?? [])) {
            $rules['qr_code_data'] = 'required|string|max:500';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'selected_fields.required' => 'Please select at least one field for the badge.',
            'name.required_if' => 'Name is required when selected.',
            'company_name.required_if' => 'Company name is required when selected.',
            'designation.required_if' => 'Designation is required when selected.',
            'logo.required_if' => 'Logo is required when selected.',
            'qr_code_data.required_if' => 'QR code data is required when selected.',
        ];
    }
}