<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiStoreBookingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'facility_item_id' => 'required|exists:facility_items,id',
            'start_datetime' => 'required|date|after:now',
            'end_datetime' => 'required|date|after:start_datetime',
            'purpose' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'facility_item_id.required' => 'Facility item is required',
            'start_datetime.after' => 'Start time must be in the future',
            'end_datetime.after' => 'End time must be after start time'
        ];
    }
}