<?php

namespace App\Exports;

use App\Models\SaleReportModel;
use Maatwebsite\Excel\Concerns\FromCollection;

class SaleReportExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return SaleReportModel::all();
    }
}
