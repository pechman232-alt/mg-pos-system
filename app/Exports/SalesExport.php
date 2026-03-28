<?php

namespace App\Exports;

use App\Models\SalesContract;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SalesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $search, $branch, $status, $from_date, $to_date, $sale_person;

    /**
     * Constructor synced with your Controller Filters
     */
    public function __construct($search = null, $branch = null, $status = null, $from_date = null, $to_date = null, $sale_person = null) {
        $this->search = $search;
        $this->branch = $branch;
        $this->status = $status;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->sale_person = $sale_person;
    }

    public function query() {
        return SalesContract::query()
            ->when($this->search, function($q) { 
                $q->where('customer_name', 'like', "%{$this->search}%")
                  ->orWhere('contract_number', 'like', "%{$this->search}%");
            })
            ->when($this->branch, function($q) { $q->where('branch_name', $this->branch); })
            ->when($this->status, function($q) { $q->where('status', $this->status); })
            ->when($this->from_date, function ($q) { $q->whereDate('order_date', '>=', $this->from_date); })
            ->when($this->to_date, function ($q) { $q->whereDate('order_date', '<=', $this->to_date); })
            ->when($this->sale_person, function ($q) { $q->where('sale_person', $this->sale_person); });
    }

    public function headings(): array {
        return [
            "Customer Name / ឈ្មោះអតិថិជន",
            "Contract ID / លេខឯកសារ",
            "Phone / លេខទូរស័ព្ទ",
            "Order Date / កាលបរិច្ឆេទ",
            "Showroom / សាខា",
            "Sale Person / ផ្នែកលក់",
            "Model / ម៉ូឌែល",
            "Color / ពណ៌",
            "Sub Total",
            "Discount",
            "Deposit",
            "Balance Due",
            "Status"
        ];
    }

    public function map($row): array {
        return [
            $row->customer_name,
            $row->contract_number,
            $row->phone_number,
            $row->order_date,
            $row->branch_name,
            $row->sale_person,
            $row->model,
            $row->color,
            $row->sub_total,
            $row->discount,
            $row->deposit,
            $row->grand_total,
            $row->status,
        ];
    }

    /**
     * Professional Excel Styling
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (Header)
            1 => [
                'font' => [
                    'bold' => true, 
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'CE181E'] // TF Motors Brand Red
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Add borders to the entire data range
            'A1:M' . ($sheet->getHighestRow()) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Column Formats (Currency for financial columns)
     */
    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE, // Sub Total
            'J' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE, // Discount
            'K' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE, // Deposit
            'L' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE, // Grand Total
        ];
    }
}