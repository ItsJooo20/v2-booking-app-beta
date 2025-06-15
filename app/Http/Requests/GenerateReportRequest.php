<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class GenerateReportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'date_range_type' => 'required|in:day,range,month,year',
            'date_day' => 'required_if:date_range_type,day|date',
            'date_range_start' => 'required_if:date_range_type,range|date',
            'date_range_end' => 'required_if:date_range_type,range|date|after_or_equal:date_range_start',
            'date_month' => 'required_if:date_range_type,month|date_format:Y-m',
            'date_year' => 'required_if:date_range_type,year|date_format:Y',
            'status' => 'nullable|in:pending,approved,rejected,completed,cancelled',
            'user_id' => 'nullable|exists:users,id',
            'facility_id' => 'nullable|exists:facilities,id',
            'category_id' => 'nullable|exists:facility_category,id',
            'facility_item_id' => 'nullable|exists:facility_items,id',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if ($this->date_range_type === 'range') {
                $start = Carbon::parse($this->date_range_start);
                $end = Carbon::parse($this->date_range_end);
                
                if ($end->diffInDays($start) > 31) {
                    $validator->errors()->add(
                        'date_range_end', 
                        'The date range cannot exceed 31 days.'
                    );
                }
            }
        });
    }
}