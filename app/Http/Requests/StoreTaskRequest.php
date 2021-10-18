<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'repeat' => ['required', Rule::in(['never', 'day', 'week', 'month', 'year'])],
            'week_days' => ['exclude_unless:repeat,week', 'array', 'min:1'],
            'ends' => ['exclude_if:repeat,never', Rule::in(['never', 'on'])],
            'end_date' => ['exclude_unless:ends,on', 'date_format:Y-m-d'],
        ];
    }
}
