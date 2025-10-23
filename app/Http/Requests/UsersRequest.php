<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'user_id' => 'required',
            'gender' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required',
            'branch_id' => 'required|integer|exists:branches,id',
            'department_id' => 'required|integer|exists:departments,id',
            'position_id' => 'required|integer|exists:positions,id',
            'start_work_date' => 'required',
            'password' => 'required|string|min:8',
            'role_id' => 'required|integer',
            'profile_pic' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Enter a valid Full Name!',
            'username.required' => 'Enter a valid Username!',
            'username.unique' => 'This username is already taken!',
            'user_id.required' => 'Enter a valid User-ID!',
            'gender.required' => 'Please select Gender!',
            'email.required' => 'Enter a valid Email!',
            'email.email' => 'Please provide a valid email address!',
            'email.unique' => 'This email is already registered!',
            'phone_number.required' => 'Enter a valid Phone Number!',
            'branch_id.required' => 'Please Select Branch Name!',
            'branch_id.exists' => 'The selected branch does not exist!',
            'department_id.required' => 'Please Select Department Name!',
            'department_id.exists' => 'The selected department does not exist!',
            'position_id.required' => 'Please Select Position Name!',
            'position_id.exists' => 'The selected position does not exist!',
            'start_work_date.required' => 'Please Select Start Work Date!',
            'start_work_date.date' => 'Start work date must be a valid date!',
            'password.required' => 'Enter a valid Password!',
            'password.min' => 'Password must be 8 characters or more!',
            'role_id.required' => 'Please Select User Role!',
            'role_id.exists' => 'The selected role does not exist!',
            'profile_pic.required' => 'Please upload a profile picture.',
            'profile_pic.image' => 'The uploaded file must be an image.',
            'profile_pic.mimes' => 'Only JPEG, PNG, and JPG formats are allowed.',
            'profile_pic.max' => 'Profile picture must not exceed 2MB.',
        ];
    }
}
