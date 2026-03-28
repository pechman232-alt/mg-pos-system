<?php

namespace App\Exports;

use App\Models\EmployeeModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EmployeeExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
    * ទាញយកទិន្នន័យទាំងអស់
    */
    public function collection()
    {
        // ប្រើជាមួយ Relationships ដើម្បីឱ្យទិន្នន័យចេញមកគ្រប់ជ្រុងជ្រោយ
        return EmployeeModel::with(['company', 'department', 'position', 'branch'])->get();
    }

    /**
    * កំណត់ក្បាលជួរឈរ (Headers)
    */
    public function headings(): array
    {
        return [
            'គោលត្តនាម', 'នាម', 'First Name', 'Last Name', 'Full Name',
            'Sex', 'Position', 'Date of Birth', 'User.ID', 'Company',
            'Branch', 'Department', 'Phone Number', 'Status'
        ];
    }

    /**
    * ផ្គូផ្គងទិន្នន័យពី Database ទៅកាន់ Excel
    */
    public function map($emp): array
    {
        return [
            $emp->first_name_kh,
            $emp->last_name_kh,
            $emp->first_name,
            $emp->last_name,
            $emp->full_name,
            $emp->gender == 'Male' ? 'M' : 'F',
            $emp->position->name ?? 'N/A',
            $emp->dob,
            $emp->user_id,
            $emp->company->company_name ?? 'N/A',
            $emp->branch->branch_name ?? 'N/A',
            $emp->department->name ?? 'N/A',
            $emp->phone_number,
            $emp->status == 1 ? 'Active' : 'Inactive',
        ];
    }
}