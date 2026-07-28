<?php

namespace App\Exports;

use App\Models\LogisticsModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class LogisticExport implements FromCollection, WithHeadings
{
    protected $isSale, $dateFrom, $dateTo;

    public function __construct($isSale = null, $dateFrom = null, $dateTo = null) {
        $this->isSale   = $isSale;
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
    }

    public function collection() {
        $latestInvoices = DB::table('sale_reports')
            ->select('logistics_id', DB::raw('MAX(invoice_date) as invoice_date'))
            ->groupBy('logistics_id');

        $q = LogisticsModel::query()
            ->leftJoinSub($latestInvoices, 'sr', function ($join) {
                $join->on('sr.logistics_id', '=', 'logistics.id');
            })
            // យើង Join ដើម្បីយកឈ្មោះមកបង្ហាញក្នុង Excel ឱ្យស្រួលមើល
            ->leftJoin('car_models', 'car_models.id', '=', 'logistics.model_name_id')
            ->select(
                'car_models.model_name', // នឹងក្លាយជា model_name_id ក្នុង headings
                'logistics.exterior_color',
                'logistics.interior_color',
                'logistics.model_year',
                'logistics.vin_number',
                'logistics.engine_number',
                'logistics.remark',
                DB::raw("CASE WHEN logistics.is_sale = 0 THEN 'Sold' ELSE 'Not yet sold' END AS sale_status"),
                DB::raw("DATE_FORMAT(sr.invoice_date, '%Y-%m-%d') AS invoice_date")
            )
            ->orderByDesc('sr.invoice_date');

        if ($this->isSale !== null && $this->isSale !== '') {
            $q->where('logistics.is_sale', $this->isSale);
        }

        if (!empty($this->dateFrom) && !empty($this->dateTo)) {
            $q->whereBetween('sr.invoice_date', [$this->dateFrom, $this->dateTo]);
        } elseif (!empty($this->dateFrom)) {
            $q->whereDate('sr.invoice_date', '>=', $this->dateFrom);
        } elseif (!empty($this->dateTo)) {
            $q->whereDate('sr.invoice_date', '<=', $this->dateTo);
        }

        return $q->get();
    }

    public function headings(): array {
        return [
            "model_name_id",   // ត្រូវគ្នានឹង Import
            "exterior_color", 
            "interior_color", 
            "model_year", 
            "vin_number", 
            "engine_number", 
            "remark", 
            "sale_status", 
            "invoice_date"
        ];
    }
}