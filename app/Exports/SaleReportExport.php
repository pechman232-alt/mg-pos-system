<?php

namespace App\Exports;

use App\Models\SaleReportModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class SaleReportExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithCustomStartCell
{
    protected $dateFrom;
    protected $dateTo;

    public function __construct($dateFrom = null, $dateTo = null) {
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
    }

    public function startCell(): string {
        return 'A6';
    }

    public function collection() {
        $query = SaleReportModel::query()
            ->leftJoin('logistics','logistics.id', '=', 'sale_reports.logistics_id')
            ->leftJoin('car_models', 'car_models.id', '=', 'logistics.model_name_id')
            ->leftJoin('warranty as warranty_mg', 'warranty_mg.id', '=', 'sale_reports.warranty_id')
            ->leftJoin('warranty as warranty_leap_motor', 'warranty_leap_motor.id', '=', 'sale_reports.warranty_leap_motor')
            ->leftJoin('employees', 'employees.id', '=', 'sale_reports.employee_id')
            ->leftJoin('provinces', 'provinces.id', '=', 'sale_reports.province_id')
            ->leftJoin('branches', 'branches.id', '=', 'sale_reports.branch_id')
            ->select(
                'sale_reports.customer_name',
                'sale_reports.phone_number',
                'sale_reports.gender',
                'sale_reports.dob',
                'sale_reports.source_id',
                'branches.branch_name',
                'car_models.model_name',
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
                'provinces.name as province',
                'sale_reports.contract_no'
            );

        if (!empty($this->dateFrom) && !empty($this->dateTo)) {
            $query->whereBetween('sale_reports.invoice_date', [$this->dateFrom, $this->dateTo]);
        }

        return $query->get();
    }

    public function headings(): array {
        return [
            "CUSTOMER NAME", "PHONE", "GENDER", "DOB", "SOURCE", "BRANCH",
            "VEHICLE MODEL", "VIN NUMBER", "ENGINE NUMBER", "INVOICE NO",
            "INVOICE DATE", "DELIVERY DATE", "STATUS", "FEEDBACK COMMENTS",
            "REMARK SA", "WARRANTY (MG)", "WARRANTY (LEAP)", "SALES PERSON",
            "PROVINCE", "CONTRACT NO"
        ];
    }

    public function styles(Worksheet $sheet) {
        $lastRow = $sheet->getHighestRow();
        $lastCol = 'T';

        // --- 1. TOP HEADER BRANDING ---
        
        // Company Name: Using #009EFB (Vibrant Blue)
        $sheet->setCellValue('A1', 'TF MOTORS (CAMBODIA) CO., LTD');
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true, 
                'size' => 18, 
                'color' => ['rgb' => '009EFB'] // Changed to 009efb
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);

        // Report Title
        $sheet->setCellValue('A3', 'Sales Report Summary');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('333333'));

        // Admin Timestamp: Using #17A2B8 (Teal/Info)
        $dateTime = Carbon::now()->format('m/d/Y h:i:s A');
        $sheet->setCellValue('A4', "ADMIN [$dateTime]");
        $sheet->getStyle('A4')->applyFromArray([
            'font' => [
                'italic' => true, 
                'size' => 10, 
                'color' => ['rgb' => '17A2B8'] // Changed to 17a2b8
            ]
        ]);


        // --- 2. TABLE DATA STYLING ---

        $sheet->freezePane('A7');

        // Table Heading Row (Row 6): Gradient Blue/Teal look
        $sheet->getRowDimension(6)->setRowHeight(35);
        $sheet->getStyle("A6:{$lastCol}6")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => ['rgb' => '009EFB'], // Start Blue
                'endColor'   => ['rgb' => '17A2B8'], // End Teal
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Zebra Content Rows
        for ($row = 7; $row <= $lastRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(20);
            $fillColor = ($row % 2 == 0) ? 'FFFFFF' : 'F4F9FD'; // Very light blue tint
            
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fillColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DEE2E6']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
        }

        return [];
    }
}