<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogisticRequest extends FormRequest
{
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
            'model_year' => 'required|string|max:255',
            'vin_number' => 'required|string|max:255',
            'engine_number' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'model_name.required' => 'Enter a valid model name!',
        ];
    }


}
