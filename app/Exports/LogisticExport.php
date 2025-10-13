<?php

namespace App\Exports;

use App\Models\LogisticsModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LogisticExport implements FromCollection, WithHeadings
{
    protected $isSale;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($isSale = null, $dateFrom = null, $dateTo = null) {
        $this->isSale  = $isSale;
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
    }

    public function collection() {
        $query = LogisticsModel::query()
        ->leftJoin('sale_reports', 'sale_reports.logistics_id', '=', 'logistics.id')
        ->select(
            'logistics.id', 
            'logistics.model_name', 
            'logistics.exterior_color', 
            'logistics.interior_color', 
            'logistics.model_year',
            'logistics.vin_number',
            'logistics.engine_number',
            'logistics.remark',
            'logistics.is_sale',
            'sale_reports.invoice_date'
        );

        if (!is_null($this->isSale) && $this->isSale !== '') {
            $query->where('logistics.is_sale', $this->isSale);
        }

        if (!empty($this->dateFrom) && !empty($this->dateTo)) {
            $query->whereBetween('sale_reports.invoice_date', [$this->dateFrom, $this->dateTo]);
        } elseif (!empty($this->dateFrom)) {
            $query->whereDate('sale_reports.invoice_date', '>=', $this->dateFrom);
        } elseif (!empty($this->dateTo)) {
            $query->whereDate('sale_reports.invoice_date', '<=', $this->dateTo);
        }

        return $query->get()->map(function ($item) {
            $item->is_sale = $item->is_sale == 0 ? 'sold' : 'not yet sold';
            return $item;
        });
    }

    public function headings(): array {
        return ["id", "model_name", "exterior_color", "interior_color", "model_year", "	vin_number", "engine_number", "remark", "is_sale", "invoice_date"];
    }
}
