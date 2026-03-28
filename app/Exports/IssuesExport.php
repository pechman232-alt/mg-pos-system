<?php

namespace App\Exports;

use App\Models\UsersModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, WithEvents
{
    /**
    * កំណត់ឱ្យតារាងចាប់ផ្ដើមនៅជួរទី ៤
    */
    public function startCell(): string
    {
        return 'A4';
    }

    /**
    * ទាញយកទិន្នន័យ User ទាំងអស់
    */
    public function collection()
    {
        return UsersModel::latest()->get();
    }

    /**
    * កំណត់ចំណងជើងជួរឈរ (Table Headings)
    */
    public function headings(): array
    {
        return [
            'ID',
            'Full Name',
            'Email Address',
            'Role ID',
            'Created Date',
            'Status'
        ];
    }

    /**
    * រៀបចំទិន្នន័យតាមជួរនីមួយៗ
    */
    public function map($user): array
    {
        return [
            $user->id,
            $user->full_name,
            $user->email,
            $user->role_id == 1 ? 'Administrator' : 'General User',
            $user->created_at->format('d-M-Y'),
            'Active' // ឬតាមទិន្នន័យជាក់ស្តែងរបស់បង
        ];
    }

    /**
    * បន្ថែម Header ធំ និងកំណត់ Style
    */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ១. បញ្ចូលចំណងជើងធំ (Merge A1 ដល់ F1)
                $sheet->mergeCells('A1:F1');
                $sheet->setCellValue('A1', 'SYSTEM USER DIRECTORY REPORT');
                
                // ២. បញ្ចូលថ្ងៃខែទាញរបាយការណ៍ (Merge A2 ដល់ F2)
                $sheet->mergeCells('A2:F2');
                $sheet->setCellValue('A2', 'Generated Date: ' . date('d-M-Y H:i A'));

                // ៣. កំណត់ Style ឱ្យ Header (ពណ៌ Indigo, អក្សរដិត, កណ្តាល)
                $styleMainHeader = [
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '4F46E5']],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                ];

                $sheet->getStyle('A1')->applyFromArray($styleMainHeader);

                // ៤. កំណត់ Style ឱ្យ Table Header (ជួរទី ៤)
                $styleTableHeader = [
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1E293B']
                    ],
                ];
                $sheet->getStyle('A4:F4')->applyFromArray($styleTableHeader);
                
                // ៥. កំណត់ឱ្យ Column រីកតាមអក្សរអូតូ
                foreach (range('A', 'F') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}