<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacilityRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Update with your authorization logic if needed
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:facility_categories,id',
        ];
    }

    public function messages()
    {
        return [
            'category_id.required' => 'The facility category is required',
            'category_id.exists' => 'The selected facility category is invalid',
        ];
    }
}