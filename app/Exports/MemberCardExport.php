<?php

namespace App\Exports;

use App\Models\MemberCard;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MemberCardExport implements FromCollection, WithHeadings
{
    protected $isSale;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($isSale = null, $dateFrom = null, $dateTo = null) {
        $this->isSale  = $isSale;
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
    }

    public function collection(){
        $query = MemberCard::query()
            ->leftJoin('sale_reports', 'sale_reports.member_card', '=', 'member_cards.id')
            ->select('member_cards.id', 'member_cards.member_card', 'member_cards.is_sale', 'sale_reports.invoice_date');

        if (!is_null($this->isSale) && $this->isSale !== '') {
            $query->where('member_cards.is_sale', $this->isSale);
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
        return ["ID", "Member Card", "Is Sale", "Invoice Date"];
    }
}
