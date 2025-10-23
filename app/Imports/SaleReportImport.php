<?php

namespace App\Imports;

use App\Models\SaleReportModel;
use Maatwebsite\Excel\Concerns\ToModel;

class SaleReportImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new SaleReportModel([
            //
        ]);
    }
}
