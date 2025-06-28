<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiUpdateBookingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'facility_item_id' => 'sometimes|required|exists:facility_items,id',
            'start_datetime' => 'sometimes|required|date',
            'end_datetime' => 'sometimes|required|date|after:start_datetime',
            'purpose' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:pending,approved,rejected,completed,cancelled'
        ];
    }
}