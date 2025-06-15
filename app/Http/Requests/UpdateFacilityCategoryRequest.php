<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacilityCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|max:50|unique:facility_categories,name,'.$this->route('facility_category'),
            'description' => 'nullable|string',
        ];
    }
}