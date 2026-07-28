<?php

namespace App\Exports;

use App\Models\SaleReportModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Carbon\Carbon;

class SaleReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithCustomStartCell, WithDefaultStyles
{
    protected $dateFrom;
    protected $dateTo;

    public function __construct($dateFrom = null, $dateTo = null) {
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
    }

    public function startCell(): string {
        return 'A5';
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return [
            'font' => [
                'name' => 'Khmer OS Siemreap', 
                'size' => 10,
            ],
        ];
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
                'sale_reports.*', 
                'branches.branch_name',
                'car_models.model_name as car_model_name',
                'logistics.vin_number',
                'logistics.engine_number',
                'warranty_mg.name as warranty_name',
                'warranty_leap_motor.name as leap_warranty_name',
                'employees.full_name as employee_name',
                'provinces.name as province_name'
            );

        if (!empty($this->dateFrom) && !empty($this->dateTo)) {
            $query->whereBetween('sale_reports.invoice_date', [$this->dateFrom, $this->dateTo]);
        }

        return $query->get();
    }

    public function headings(): array {
        return [
            "MEMBER CARD", "BRAND NAME", "CUSTOMER NAME", "PHONE NUMBER", "GENDER", "DATE OF BIRTH", "SOURCE", 
            "BRANCH", "VEHICLE MODEL", "VIN NUMBER", "ENGINE NUMBER", "PLATE NUMBER", "INVOICE NO",
            "INVOICE DATE", "DELIVERY DATE", "STATUS", "FOLLOW-UP", "FEEDBACK COMMENTS", "REMARK SA", 
            "WARRANTY (MG)", "WARRANTY (LEAP)", "SALES PERSON", "PROVINCE", "CONTRACT NO", "CONTRACT DATE",
            "VPC & VDR", "OFFER LICENSE PLATE", "OFFER UV FILM", "OFFER FLOOR MAT", "SIDE STEP", 
            "AUTO TAILGATE", "REAR SPOILER", "CERTIFICATE ENGINE OIL", "PRO OFFER - OIL", "PRO OFFER OIL - DATE",
            "VEHICLE BATTERY INSPECTION", "PRICE"
        ];
    }

    public function map($row): array
    {
        // 🌟 មុខងារឆែកមើល បើទទេ ដាក់សញ្ញា -
        $checkEmpty = function($value) {
            return (empty($value) || trim($value) === '' || strtoupper(trim($value)) === 'N/A') ? '-' : $value;
        };

        return [
            $checkEmpty($row->member_card_id),
            $checkEmpty($row->brand_name),
            $checkEmpty($row->customer_name),
            $checkEmpty($row->phone_number),
            $checkEmpty($row->gender),
            $row->dob ? \Carbon\Carbon::parse($row->dob)->format('Y-m-d') : '-',
            $checkEmpty($row->source_id),
            $checkEmpty($row->branch_name),
            $checkEmpty($row->car_model_name), 
            $checkEmpty($row->vin_number),
            $checkEmpty($row->engine_number),
            $checkEmpty($row->plate_number),
            $checkEmpty($row->invoice_no),
            $row->invoice_date ? \Carbon\Carbon::parse($row->invoice_date)->format('Y-m-d') : '-',
            $row->delivery_date ? \Carbon\Carbon::parse($row->delivery_date)->format('Y-m-d') : '-',
            $row->status == 1 ? 'Active' : 'Inactive',
            $checkEmpty($row->follow_up),
            $checkEmpty($row->feedback_command),
            $checkEmpty($row->remark_sa),
            $checkEmpty($row->warranty_name),
            $checkEmpty($row->leap_warranty_name),
            $checkEmpty($row->employee_name),
            $checkEmpty($row->province_name),
            $checkEmpty($row->contract_no),
            $row->contract_date ? \Carbon\Carbon::parse($row->contract_date)->format('Y-m-d') : '-',
            $checkEmpty($row->vpc_vdr),
            $checkEmpty($row->offer_license_plate),
            $checkEmpty($row->offer_uv_film),
            $checkEmpty($row->offer_floor_mat),
            $checkEmpty($row->side_step),
            $checkEmpty($row->auto_tailgate),
            $checkEmpty($row->rear_spoiler),
            $checkEmpty($row->certificate_engine_oil),
            $checkEmpty($row->pro_offer_oil),
            $row->pro_offer_oil_date ? \Carbon\Carbon::parse($row->pro_offer_oil_date)->format('Y-m-d') : '-',
            $row->vihicle_battery ? \Carbon\Carbon::parse($row->vihicle_battery)->format('Y-m-d') : '-',
            $row->price ? '$' . number_format((float)$row->price, 2) : '-',
        ];
    }

    public function styles(Worksheet $sheet) {
        $lastRow = $sheet->getHighestRow();
        $lastCol = 'AK';

        $sheet->setCellValue('A1', 'TF MOTORS (CAMBODIA) CO., LTD');
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FF002663']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);

        $sheet->setCellValue('A2', 'SALE REPORT SUMMARY DATA');
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FF333333']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);

        $dateTime = Carbon::now()->format('d-M-Y h:i A');
        $sheet->setCellValue('A3', "Exported On: {$dateTime}");
        $sheet->mergeCells("A3:{$lastCol}3");
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF64748B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);

        $sheet->freezePane('A6'); 
        $sheet->setAutoFilter("A5:{$lastCol}{$lastRow}");

        $sheet->getRowDimension(5)->setRowHeight(30);
        $sheet->getStyle("A5:{$lastCol}5")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            // 🌟 ពណ៌ Background (FF + BF9000)
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFBF9000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF334155']]]
        ]);

        if ($lastRow > 5) {
            $sheet->getStyle("A6:{$lastCol}{$lastRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCBD5E1']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            
            for ($row = 6; $row <= $lastRow; $row++) {
                $sheet->getRowDimension($row)->setRowHeight(22);
                if ($row % 2 == 0) {
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8FAFC']]
                    ]);
                }
            }
        }
        return [];
    }
}