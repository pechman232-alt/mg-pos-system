<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogisticRequest extends FormRequest{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'model_name' => 'required|string|max:255',
            'exterior_color' => 'required|string|max:255',
            'interior_color' => 'required|string|max:255',
            'model_year' => 'required|string|max:4',
            'vin_number' => 'required|string|max:255|unique:logistics,vin_number',
            'engine_number' => 'required|string|max:255|unique:logistics,engine_number',
            'remark' => 'nullable|string|max:1000'
        ];
    }

    public function messages(): array
    {
        return [
            'model_name.required' => 'enter a valid model name!.',
            'exterior_color.required' => 'enter a valid exterior color!.',
            'interior_color.required' => 'enter a valid interior color!.',
            'model_year.required' => 'enter a valid model year!.',
            'model_year.max' => 'model year can not enter more than 4!',
            'vin_number.required' => 'enter a valid vin number!.',
            'vin_number.unique' => 'the vin number already exists.',
            'engine_number.required' => 'enter a valid engine number!.',
            'engine_number.unique' => 'the engine number already exists.',
        ];
    }
}
