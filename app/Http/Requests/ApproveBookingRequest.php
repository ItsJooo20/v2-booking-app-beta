<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ApproveBookingRequest extends FormRequest
{
    public function authorize()
    {
        return in_array(Auth::user()->role, ['admin', 'headmaster']);
    }

    public function rules()
    {
        return [
            'notes' => 'nullable|string|max:500',
        ];
    }
}