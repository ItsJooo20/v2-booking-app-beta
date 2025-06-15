<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFacilityItemRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Update with your authorization logic if needed
    }

    public function rules()
    {
        return [
            'facility_id' => 'required|exists:facilities,id',
            'item_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('facility_items')->ignore($this->route('facility_item'))
            ],
            'status' => 'required|in:available,booked,under_maintenance',
            'notes' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'facility_id.required' => 'The facility is required',
            'facility_id.exists' => 'The selected facility is invalid',
            'item_code.unique' => 'This item code already exists',
            'status.required' => 'The status field is required',
        ];
    }
}