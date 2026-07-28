<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DailySalesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $fromDate, $toDate, $branchId;
    protected $index = 0; // សម្រាប់រាប់លេខរៀង (No)

    public function __construct($fromDate, $toDate, $branchId = null)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->branchId = $branchId;
    }

    /**
     * ទាញយកទិន្នន័យពី Database
     */
    public function collection()
    {
        $query = DB::table('sales_headers as sh')
            ->leftJoin('branches as b', 'sh.location_id', '=', 'b.id')
            ->leftJoin('users as u', 'sh.user_id', '=', 'u.id')
            ->leftJoin('sales_details as sd', 'sh.id', '=', 'sd.sales_header_id')
            ->select(
                'sh.invoice_no',
                'sh.sale_date',
                'b.branch_name',
                'sh.customer_name',
                'sh.car_buyer_name',
                'sh.car_invoice_no',
                'sd.item_code',
                'sd.item_name',
                'sd.purchase_invoice_no as pi_no',
                'sd.qty',
                'sd.unit_price',
                'sd.total_price',
                'u.username',
                'sh.reason'
            )
            ->whereBetween('sh.sale_date', [$this->fromDate . ' 00:00:00', $this->toDate . ' 23:59:59']);

        if (!empty($this->branchId)) {
            $query->where('sh.location_id', $this->branchId);
        }

        return $query->orderBy('sh.id', 'desc')->get();
    }

    /**
     * ✅ បង្កើតចំណងជើងក្រុមហ៊ុន និង ក្បាលតារាង (ដូចក្នុងរូបភាព)
     */
    public function headings(): array
    {
        $monthYear = date('F Y', strtotime($this->fromDate));
        $dateRange = date('d.m.Y', strtotime($this->fromDate)) . ' - ' . date('d.m.Y', strtotime($this->toDate));

        return [
            ['TF MOTORS (CAMBODIA) CO.,LTD'],            // ជួរទី១
            ['Stock Out List for ' . $monthYear],        // ជួរទី២
            ['Date: ' . $dateRange],                     // ជួរទី៣
            [''],                                        // ជួរទី៤ (ទុកចន្លោះទំនេរ)
            [                                            // ជួរទី៥ (ក្បាលតារាង)
                'No',
                'Invoice No',
                'Date',
                'Branch',
                'Sale Person',
                'Car Buyer',
                'Car Invoice',
                'Item Code',
                'Description',
                'Purchase Invoice (PI)',
                'Qty',
                'Unit Price ($)',
                'Total ($)',
                'Created By',
                'Remark'
            ]
        ];
    }

    /**
     * រៀបចំទិន្នន័យដាក់តាម Column នីមួយៗ
     */
    public function map($row): array
    {
        $this->index++; // បូកលេខរៀងម្តង ១ ស្វ័យប្រវត្តិ

        return [
            $this->index,
            $row->invoice_no,
            date('d-M-Y H:i', strtotime($row->sale_date)),
            $row->branch_name,
            $row->customer_name,
            $row->car_buyer_name ?? '-',
            $row->car_invoice_no ?? '-',
            $row->item_code,
            $row->item_name,
            $row->pi_no ?? '-',
            $row->qty,
            $row->unit_price, 
            $row->total_price,
            $row->username,
            $row->reason ?? '-'
        ];
    }

    /**
     * ✅ Design ពណ៌អក្សរខៀវ និង បន្ទាត់តារាងឱ្យដូចគំរូ
     */
    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn(); 

        // ១. ស្ទីលសម្រាប់ចំណងជើងខាងលើ (Rows 1-3)
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FF0000FF'], 'size' => 12] // អក្សរពណ៌ខៀវ
        ]);
        $sheet->getStyle('A2:A3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FF000000']] // អក្សរពណ៌ខ្មៅ
        ]);

        // ២. គូសបន្ទាត់តារាង ចាប់ពីជួរទី៥ ចុះក្រោម (A5 រហូតដល់ចុងក្រោយ)
        $tableRange = 'A5:' . $highestColumn . $highestRow;
        $sheet->getStyle($tableRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'], // បន្ទាត់ពណ៌ខ្មៅ
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // ៣. ស្ទីលសម្រាប់ក្បាលតារាង ជួរទី៥ (អក្សរខៀវ ដិត ចំកណ្តាល)
        $sheet->getStyle('A5:' . $highestColumn . '5')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FF0000FF'], // ពណ៌ខៀវដូចក្នុងរូបបេះបិទ
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // ៤. តម្រឹមទិន្នន័យកណ្តាលសម្រាប់ No, Invoice, Date, Branch
        $sheet->getStyle('A6:D' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K6:K' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Qty
        
        // ៥. តម្រឹមស្តាំសម្រាប់តម្លៃលុយ និង Format ជាលុយដុល្លារ
        $sheet->getStyle('L6:M' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('L6:M' . $highestRow)->getNumberFormat()->setFormatCode('"$"#,##0.00');

        return [];
    }
}