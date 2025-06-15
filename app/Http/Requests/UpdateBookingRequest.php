<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'facility_item_id' => 'required|exists:facility_items,id',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'purpose' => 'required|string|max:255',
            'status' => 'sometimes|in:pending,approved,rejected,completed,cancelled',
        ];
    }
}