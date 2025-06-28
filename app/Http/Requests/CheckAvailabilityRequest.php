<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckAvailabilityRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'exclude_booking_id' => 'sometimes|nullable|exists:bookings,id'
        ];
    }

    public function messages()
    {
        return [
            'end_datetime.after' => 'End time must be after start time'
        ];
    }
}