<?php

namespace App\Exports;

use App\Models\EmployeeModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
    * ទាញយកទិន្នន័យទាំងអស់ (យកតែអ្នកកំពុងធ្វើការ ឬចង់យកទាំងអស់ក៏បាន)
    */
    public function collection()
    {
        // ប្រើជាមួយ Relationships ដើម្បីឱ្យទិន្នន័យចេញមកគ្រប់ជ្រុងជ្រោយ
        return EmployeeModel::with(['company', 'department', 'position', 'branch'])
            ->orderBy('id', 'desc') // តម្រៀបអ្នកថ្មីនៅខាងលើ
            ->get();
    }

    /**
    * កំណត់ក្បាលជួរឈរ (Headers)
    */
    public function headings(): array
    {
        return [
            'គោត្តនាម (Last Name KH)', 'នាម (First Name KH)', 'First Name', 'Last Name', 'Full Name',
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
            $emp->last_name_kh ?? 'N/A',   // ជាទូទៅ គោត្តនាម គឺត្រកូល (Last Name)
            $emp->first_name_kh ?? 'N/A',  // នាម គឺឈ្មោះខ្លួន (First Name)
            $emp->first_name ?? 'N/A',
            $emp->last_name ?? 'N/A',
            $emp->full_name ?? 'N/A',
            (strtolower($emp->gender) == 'male' || $emp->gender == 'M') ? 'M' : 'F',
            $emp->position->name ?? 'N/A',
            
            // ការពារ Error ពេល Export បើ dob មានតម្លៃ វាបម្លែងជា Y-m-d, បើអត់ដាក់ N/A
            $emp->dob ? $emp->dob->format('Y-m-d') : 'N/A', 
            
            $emp->user_id ?? 'N/A',
            $emp->company->company_name ?? 'N/A',
            $emp->branch->branch_name ?? 'N/A',
            $emp->department->name ?? 'N/A',
            $emp->phone_number ?? 'N/A',
            $emp->status == 1 ? 'Active' : 'Inactive',
        ];
    }

    /**
    * តុបតែងក្បាល Excel (Header) ឱ្យមានអក្សរដិត និងពណ៌ផ្ទៃ
    */
    public function styles(Worksheet $sheet)
    {
        return [
            // ជួរទី ១ (Header)
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 
                'fill' => ['fillType' => 'solid', 'color' => ['argb' => 'FF4F46E5']] // ពណ៌ខៀវ (Indigo) ស៊ីនឹង Theme របស់បង
            ],
        ];
    }
}