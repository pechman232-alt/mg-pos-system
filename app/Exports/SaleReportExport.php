<?php

namespace App\Exports;

use App\Models\SaleReportModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class SaleReportExport implements FromCollection, WithHeadings, WithStyles
{
    protected $dateFrom;
    protected $dateTo;

    public function __construct($dateFrom = null, $dateTo = null) {
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
    }

    public function collection() {
        $query = SaleReportModel::query()
        ->join('logistics','logistics.id', '=', 'sale_reports.logistics_id')
        ->join('warranty as warranty_mg', 'warranty_mg.id', '=', 'sale_reports.warranty_id')
        ->join('warranty as warranty_leap_motor', 'warranty_leap_motor.id', '=', 'sale_reports.warranty_leap_motor')
        ->join('employees', 'employees.id', '=', 'sale_reports.employee_id')
        ->join('provinces', 'provinces.id', '=', 'sale_reports.province_id')
        ->select(
            'sale_reports.customer_name',
            'sale_reports.phone_number',
            'sale_reports.gender',
            'sale_reports.dob',
            'sale_reports.age',
            'sale_reports.source_id',

            'logistics.model_name',
            'logistics.vin_number',
            'logistics.engine_number',

            'sale_reports.invoice_no',
            'sale_reports.invoice_date',
            'sale_reports.delivery_date',
            'sale_reports.follow_up',
            'sale_reports.feedback_command',
            'sale_reports.remark_sa',
            
            'warranty_mg.name as warranty',
            'warranty_leap_motor.name as warranty_leap_motor',

            'employees.full_name',
            'provinces.name',

            'sale_reports.contract_no'
        );

        if (!empty($this->dateFrom) && !empty($this->dateTo)) {
            $query->whereBetween('sale_reports.invoice_date', [$this->dateFrom, $this->dateTo]);
        } elseif (!empty($this->dateFrom)) {
            $query->whereDate('sale_reports.invoice_date', '>=', $this->dateFrom);
        } elseif (!empty($this->dateTo)) {
            $query->whereDate('sale_reports.invoice_date', '<=', $this->dateTo);
        }

        return $query->get();
    }

    public function headings(): array {
        return [
            "Customer's Name",
            "Phone Number",
            "Gender",
            "Date of Birth",
            "Age",
            "Source Customer",
            "Vehicle Model",
            "VIN Number",
            "Engine Number",
            "Invoice No",
            "Invoice Date",
            "Delivery Date",
            "Follow-up",
            "Follow-Up Comments",
            "Remark",
            "Warranty",
            "Battery Warranty For LeapMotor",
            "Sales Person",
            "Address",
            "Contract No."
        ];
    }

    public function styles(Worksheet $sheet) {
        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:T1')->applyFromArray([
            'font' => ['color' => ['rgb' => '000000']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F5C7A9'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        for ($row = 2; $row <= $lastRow; $row++) {
            $fillColor = ($row % 2 == 0) ? 'FFFFFF' : 'E8F0FE';
            $sheet->getStyle("A{$row}:T{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $fillColor],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);
        }

        // return [
        //     1 => [ // 1 means first row
        //         'font' => ['color' => ['rgb' => '000000']],
        //         'fill' => [
        //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        //             'startColor' => ['rgb' => 'F5C7A9'], // dark blue background
        //         ],
        //         'alignment' => [
        //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        //             'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        //         ],
        //         'borders' => [
        //             'allBorders' => [
        //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        //                 'color' => ['rgb' => '000000'], // black border
        //             ],
        //         ],
        //     ],
        // ];
    }
}
