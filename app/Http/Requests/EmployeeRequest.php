<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'required|integer|exists:companies,id',
            'department_id' => 'required|integer|exists:departments,id',
            'position_id' => 'required|integer|exists:positions,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'first_name_kh' => 'required|string|max:255',
            'last_name_kh' => 'required|string|max:255',
            'gender' => 'required',
            'dob' => 'required',
            'user_id' => 'required',
            'branch_id' => 'required|integer|exists:branches,id',
            'contract' => 'required',
            'official_userid' => 'required',
            'official_pass' => 'required',
            'group_domain' => 'required',
            's_user_id' => 'required',
            's_pass' => 'required',
            'm_user_id' => 'required',
            'm_pass' => 'required',
            'hr_user_id' => 'required',
            'hr_pass' => 'required',
            'rapp_user_id' => 'required',
            'rapp_pass' => 'required',
            'user_id_data_sheet' => 'required',
            'data_sheet_pass' => 'required',
            'extension_id' => 'required',
            'register_pass' => 'required',
            'activate' => 'required',
            'phone_number' => 'required',
            'access_door' => 'required',
            'note' => 'nullable|string|max:1000',
            'full_name' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:1',
            'image_employee' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Enter a valid Full Name!',
            'image_employee.required' => 'Please upload a Image Users.',
        ];
    }
}
