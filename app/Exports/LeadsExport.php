<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDefaultStyles; // 🌟 ថែម Import នេះ
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Style; // 🌟 ថែម Import នេះ
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

// 🌟 ថែម WithDefaultStyles ទៅក្នុង class implements
class LeadsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithDefaultStyles
{
    protected $leads;
    protected $filters;
    protected $maxFollowUps = 0;

    public function __construct($leads, $filters)
    {
        $this->leads = $leads;
        $this->filters = $filters;

        // គណនារក Follow-up ច្រើនបំផុត
        foreach ($this->leads as $lead) {
            $count = $lead->activities ? $lead->activities->count() : 0;
            if ($count > $this->maxFollowUps) {
                $this->maxFollowUps = $count;
            }
        }
        if ($this->maxFollowUps == 0) { $this->maxFollowUps = 1; }
    }

    public function collection()
    {
        return $this->leads;
    }

    // 🌟 កំណត់ Font គោល (Khmer OS Siemreap) សម្រាប់ Excel ទាំងមូល
    public function defaultStyles(Style $defaultStyle)
    {
        return [
            'font' => [
                'name' => 'Khmer OS Siemreap',
                'size' => 11,
            ],
        ];
    }

    // 🌟 កំណត់ចំណងជើងជាច្រើនជាន់ (Multi-row Headings សម្រាប់ Report Summary)
    public function headings(): array
    {
        // ១. រាប់ទិន្នន័យសរុប
        $totalLeads = $this->leads->count();

        // ២. រាប់ទិន្នន័យតាម Status (ឧ: Appointment: 5 | Prospecting: 10)
        $statusCounts = [];
        foreach ($this->leads->groupBy('status') as $status => $group) {
            $statusCounts[] = ($status ?: 'No Status') . ': ' . $group->count();
        }
        $statusSummary = implode('   |   ', $statusCounts);

        // ៣. រាប់ទិន្នន័យតាម Model ឡាន (សម្រាប់ Marketing)
        $modelCounts = [];
        foreach ($this->leads->groupBy('model') as $model => $group) {
            $modelCounts[] = ($model ?: 'No Model') . ': ' . $group->count();
        }
        $modelSummary = implode('   |   ', $modelCounts);

        // ៤. បង្កើតជួរ Header ពីលើ (Report Summary)
        $headings = [
            ['📊 របាយការណ៍សរុប (LEADS & MARKETING SUMMARY)'],
            ['ទាញយកដោយ / Exported By:', $this->filters['exported_by']],
            ['កាលបរិច្ឆេទ / Date Range:', $this->filters['date_range']],
            ['អតិថិជនសរុប / Total Leads:', $totalLeads . ' នាក់'],
            ['សរុបតាម Status / By Status:', $statusSummary],
            ['សរុបតាមម៉ូដែល / By Model:', $modelSummary],
            [''], // ជួរទី ៧ ទុកទទេដើម្បីដកឃ្លា
        ];

        // ៥. ក្បាលតារាងទិន្នន័យពិតប្រាកដ (នឹងស្ថិតនៅជួរទី ៨ ក្នុង Excel)
        $tableHeaders = [
            'Reg Date', 'Customer', 'Phone', 'Province', 'Model', 'Sales', 'Branch', 'Status'
        ];
        
        for ($i = 1; $i <= $this->maxFollowUps; $i++) {
            $tableHeaders[] = 'Follow-up ' . $i;
        }
        
        $headings[] = $tableHeaders; // បញ្ចូលក្បាលតារាងទៅក្នុងជួរទី ៨

        return $headings;
    }

    // 🌟 រៀបចំទិន្នន័យបញ្ចូលក្នុង Column
    public function map($lead): array
    {
        $row = [
            Carbon::parse($lead->created_at)->format('j/n/y'), 
            $lead->customer_name,
            $lead->contact_info,
            $lead->location ?? '', 
            $lead->model,
            $lead->sales_person,
            str_replace('MG ', '', $lead->branch_name), 
            $lead->status,
        ];

        // ទាញយក Activities ទាំងអស់
        $activities = $lead->activities ? $lead->activities->sortBy('created_at')->values() : collect();

        // វាយបញ្ចូល comment ទៅតាម Column Follow-up នីមួយៗ
        for ($i = 0; $i < $this->maxFollowUps; $i++) {
            if ($activities->has($i)) {
                $activity = $activities->get($i);
                $date = Carbon::parse($activity->created_at)->format('j/n/y');
                $row[] = "[$date] " . ($activity->comment ?? 'N/A');
            } else {
                $row[] = ''; 
            }
        }

        return $row;
    }

    // 🌟 Design ពណ៌ និង Auto Filter 🌟
    public function styles(Worksheet $sheet)
    {
        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();

        // ១. ចាក់ពណ៌ និងពង្រីកចំណងជើងធំ (Merge ទំព័រ A1 ដល់ចុង)
        $sheet->mergeCells('A1:' . $lastColumn . '1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF1E293B']], // ពណ៌ខ្មៅដិត
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // ២. ដាក់អក្សរដិត (Bold) ឱ្យ Label សង្ខេបៗពីជួរទី ២ ដល់ ៦
        $sheet->getStyle('A2:A6')->getFont()->setBold(true);

        // ៣. ចាក់ពណ៌ Header របស់ "តារាង" ដែលស្ថិតនៅជួរទី ៨
        $sheet->getStyle('A8:' . $lastColumn . '8')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], 
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF10B981'], // ពណ៌បៃតង
            ],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // ៤. គូសបន្ទាត់ជុំវិញតារាង (ចាប់ពីជួរទី ៨ ចុះក្រោម)
        $sheet->getStyle('A8:' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCBD5E1'], 
                ],
            ],
        ]);

        // ៥. បើកមុខងារ Auto Filter នៅលើក្បាលតារាង (ជួរទី ៨)
        $sheet->setAutoFilter('A8:' . $lastColumn . '8');

        return [];
    }
}