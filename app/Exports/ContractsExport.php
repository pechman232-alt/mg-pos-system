<?php

namespace App\Exports;

use App\Models\SalesContract;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ContractsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $currentUser = auth('users')->user();

        // 🌟 ការពារសុវត្ថិភាពជាន់ទី ២៖ បើជា Sale ធម្មតា (អត់មាន Role ក្នុងបញ្ជីនេះទេ) អត់ឱ្យទាញទិន្នន័យដាច់ខាត!
        if (!in_array((int) $currentUser->role_id, [1, 2, 3, 4, 5, 6, 7, 8])) {
            abort(403, 'លោកអ្នកមិនមានសិទ្ធិទាញយកទិន្នន័យនេះទេ!');
        }

        // 🌟 ១. កំណត់សិទ្ធិ Export by User: ប្រើប្រាស់ Scope byRole() ដូចក្នុង Controller
        $query = SalesContract::byRole();

        // 🌟 ២. ចាប់យកការ Filter ទាំងអស់ដែល User កំពុងរើស
        if (!empty($this->request['month'])) {
            $query->where('order_date', 'like', $this->request['month'] . '%');
        }

        if (!empty($this->request['branch'])) {
            $query->where('branch_name', $this->request['branch']);
        }

        if (!empty($this->request['status'])) {
            $query->where('status', $this->request['status']);
        }

        if (!empty($this->request['search'])) {
            $search = $this->request['search'];
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('contract_number', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('vin_number', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('id', 'desc')->get();
    }

    // 🌟 រៀបចំក្បាលតារាង Excel (English Only)
    public function headings(): array
    {
        return [
            'ID',
            'Contract Number',
            'Order Date',
            'Customer Name (EN)', // ដូរពី Customer Name ទៅបញ្ជាក់ថាអង់គ្លេស
            'Customer Name (KH)', // បើចង់ដក Field នេះចេញតែម្តង អាចលុបបន្ទាត់នេះបាន (តែកុំភ្លេចលុបក្នុង map() ដែរ)
            'Phone',
            'ID Card',
            'Branch',
            'Sale Person',
            'Vehicle Model',
            'VIN Number',
            'Color',
            'Qty',
            'Unit Price ($)',
            'Discount ($)',
            'Deposit ($)',
            'Sub Total ($)',
            'Grand Total ($)',
            'Payment Type',
            'Status',
            'Note'
        ];
    }

    // 🌟 រៀបចំជួរទិន្នន័យ (Mapping Data)
    public function map($contract): array
    {
        return [
            $contract->id,
            $contract->contract_number,
            // 🌟 Format ថ្ងៃខែឱ្យស្អាត (ឧ. 03-May-2026)
            \Carbon\Carbon::parse($contract->order_date)->format('d-M-Y'), 
            $contract->customer_name,
            $contract->customer_name_kh,
            $contract->phone_number,
            $contract->id_card_number,
            $contract->branch_name,
            $contract->sale_person,
            $contract->model,
            $contract->vin_number,
            $contract->color,
            $contract->qty,
            $contract->unit_price,
            $contract->discount,
            $contract->deposit,
            $contract->sub_total,
            $contract->grand_total,
            $contract->payment_type,
            strtoupper($contract->status),
            // សម្អាត Note កុំឱ្យជាប់ឈ្មោះ User និងម៉ោង បើចង់បានស្អាត
            preg_replace('/\[.*?\] 👤 .*?:/', '', $contract->note), 
        ];
    }

    // 🌟 មុខងារ Design, Auto Filter និងប្តូរ Font ទាំងមូល
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // ១. កំណត់ Font លំនាំដើម "Khmer OS Battambang" សម្រាប់ទាំងមូល
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Khmer OS Battambang');
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10); // ទំហំអក្សរ

                // ចាប់យកជួរ និង Column ចុងក្រោយគេបង្អស់ (ឧទាហរណ៍ A ដល់ U)
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $headerRange = 'A1:' . $highestColumn . '1';

                // ២. បន្ថែម Auto Filter នៅក្បាលតារាង (Row ទី 1)
                $sheet->setAutoFilter($headerRange);

                // ៣. Design ក្បាលតារាង (Row ទី 1) - ពណ៍ខៀវ (Royal Blue) និងអក្សរស
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => Color::COLOR_WHITE],
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF2563EB'], // ពណ៌ខៀវដូច Web
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // ៤. Design តួខ្លួនតារាង (ដាក់បន្ទាត់ (Borders) ឱ្យគ្រប់ក្រឡា)
                $dataRange = 'A1:' . $highestColumn . $highestRow;
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFCCCCCC'], // ពណ៌ប្រផេះស្រាល
                        ],
                    ],
                ]);
                
                // ៥. តម្រឹម (Center Align) សម្រាប់ Column ខ្លះៗ (ឧ. ID, Qty, Status)
                $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('M2:M' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Qty
            },
        ];
    }
}