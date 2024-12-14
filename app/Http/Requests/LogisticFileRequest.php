<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class LogisticFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
 
    public function rules(): array
    {
        return [
            'file' => 'required|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Import File Excel.',
        ];
    }

//     public function failedValidation(Validator $validator)
//     {
//         throw new HttpResponseException(response()->json([
//             'success'   => false,
//             // 'message'   => 'Validation errors',
//             'data'      => $validator->errors()
//        ]));
//    }
}
