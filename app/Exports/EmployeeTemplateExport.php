<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeTemplateExport implements WithHeadings, WithStyles, ShouldAutoSize
{
    /**
     * កំណត់ក្បាលជួរឈរ (Headers) ឱ្យទទេស្អាតសម្រាប់បំពេញថ្មី
     */
    public function headings(): array
    {
        return [
            'គោលត្តនាម',      // Last Name KH
            'នាម',            // First Name KH
            'First Name',     // First Name EN
            'Last Name',      // Last Name EN
            'Full Name',      
            'Sex',            // M/F
            'Position',       
            'Date of Birth',  // YYYY-MM-DD
            'User.ID',        
            'Company',        
            'Branch',         
            'Department',     
            'Phone Number',   
            'Status'          // 1 for Active, 0 for Inactive
        ];
    }

    /**
     * រៀបចំរចនាបថ Header ឱ្យស្អាត និងមានវិជ្ជាជីវៈ
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // កំណត់ជួរទី ១ ឱ្យដិត និងមានពណ៌ Indigo (ដូចក្នុង Theme របស់បង)
            1 => [
                'font' => [
                    'bold' => true, 
                    'color' => ['argb' => 'FFFFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4F46E5']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}