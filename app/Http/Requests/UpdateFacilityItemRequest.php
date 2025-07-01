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
            'item_code' => 'required|string|max:255',
            'facility_id' => 'required|exists:facilities,id',
            'serial_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            // 'status' => 'required|in:available,in_use,maintenance,retired',
            // 'purchase_date' => 'nullable|date',
            // 'purchase_price' => 'nullable|numeric|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'primary_image' => 'nullable|integer',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer|exists:facility_item_images,id'
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