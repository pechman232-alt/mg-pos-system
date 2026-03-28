<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // បង្កើត Full Name អូតូ ដើម្បីផ្ទៀងផ្ទាត់
        $this->merge([
            'full_name' => trim($this->first_name . ' ' . $this->last_name),
        ]);
    }

    public function rules(): array
    {
        $employeeId = $this->route('id') ? decrypt($this->route('id')) : null;

        return [
            // --- ផ្នែកដែលត្រូវតែមាន (Mandatory) ---
            'company_id'    => 'required|integer|exists:companies,id',
            'branch_id'     => 'required|integer|exists:branches,id',
            'department_id' => 'required|integer', 
            'position_id'   => 'required|integer',
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'gender'        => 'required|in:Male,Female',
            
            // --- ផ្នែកអត្តសញ្ញាណ (Check Unique) ---
            'user_id' => [
                'nullable',
                'string',
                Rule::unique('employees', 'user_id')->ignore($employeeId),
            ],
            'official_userid' => [
                'nullable',
                'string',
                Rule::unique('employees', 'official_userid')->ignore($employeeId),
            ],

            // --- ផ្នែកព័ត៌មានបន្ថែម (Nullable) ---
            'first_name_kh'      => 'nullable|string|max:255',
            'last_name_kh'       => 'nullable|string|max:255',
            'dob'                => 'nullable|date',
            'contract'           => 'nullable|date',
            'official_pass'      => 'nullable|string',
            'group_domain'       => 'nullable|string',
            's_user_id'          => 'nullable|string',
            's_pass'             => 'nullable|string',
            'm_user_id'          => 'nullable|string',
            'm_pass'             => 'nullable|string',
            'hr_user_id'         => 'nullable|string',
            'hr_pass'            => 'nullable|string',
            'rapp_user_id'       => 'nullable|string',
            'rapp_pass'          => 'nullable|string',
            'user_id_data_sheet' => 'nullable|string',
            'data_sheet_pass'    => 'nullable|string',
            'extension_id'       => 'nullable|string',
            'register_pass'      => 'nullable|string',
            'activate'           => 'nullable|date',
            'deactivate'         => 'nullable|date',
            'phone_number'       => 'nullable|string|max:20',
            'access_door'        => 'nullable|string',
            'note'               => 'nullable|string|max:2000',
            'full_name'          => 'nullable|string|max:255',
            'status'             => 'nullable|in:0,1',
            'age'                => 'nullable|integer|min:18|max:100',
            'image_employee'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required'      => 'សូមបញ្ចូលនាមត្រកូលភាសាអង់គ្លេស!',
            'last_name.required'       => 'សូមបញ្ចូលនាមខ្លួនភាសាអង់គ្លេស!',
            'company_id.required'      => 'សូមជ្រើសរើសក្រុមហ៊ុន!',
            'branch_id.required'       => 'សូមជ្រើសរើសសាខា!',
            'department_id.required'   => 'សូមជ្រើសរើសផ្នែក!',
            'position_id.required'     => 'សូមជ្រើសរើសតួនាទី!',
            'gender.required'          => 'សូមជ្រើសរើសភេទ!',
            'user_id.unique'           => 'អត្តសញ្ញាណប័ណ្ណបុគ្គលិកនេះមានរួចហើយ!',
            'official_userid.unique'   => 'ឈ្មោះគណនីប្រព័ន្ធនេះមានរួចហើយ!',
            'image_employee.max'       => 'រូបភាពមិនអាចធំជាង 2MB ឡើយ!',
        ];
    }
}