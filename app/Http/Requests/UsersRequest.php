<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => 'required',
            'username' => 'required',
            'user_id' => 'required',
            'gender' => 'required',
            'email' => 'required',
            'phone_number' => 'required',
            'branch_id' => 'required',
            'department_id' => 'required',
            'position_id' => 'required',
            'start_work_date' => 'required',
            'password' => 'required|min:8',
            'role_id' => 'required',
            'profile_pic' => 'required|image|mimes:jpeg,png,jpg',
        ];
    }

    public function messages(): array
    {
        return [
            'full_name' => 'Enter a valid Full Name!',
            'username' => 'Enter a valid Username!',
            'user_id' => 'Enter a valid User-ID!',
            'gender' => 'Please select Gender!',
            'email' => 'Enter a valid Email!',
            'phone_number' => 'Enter a valid Phone Numeber!',
            'branch_id' => 'Please Select Branch Name!',
            'department_id' => 'Please Select Department Name!',
            'position_id' => 'Please Select Position Name!',
            'start_work_date' => 'Please Select Start Work Date!',
            'password' => 'Enter a valid Password!',
            'password.min' => 'Password must be 8 charater or later',
            'user_role_id' => 'Please Select UserRole!',
            'profile_pic' => 'Please upload a profile picture.',
            'profile_pic.image' => 'The uploaded file must be an image.',
            'profile_pic.mimes' => 'Only JPEG, PNG, and JPG formats are allowed.',
        ];
    }
}
