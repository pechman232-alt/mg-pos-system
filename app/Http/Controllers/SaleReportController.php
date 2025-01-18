<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SaleReportController extends Controller
{
    public function saleReportIndex(){
        return view('SaleReports.sale_report_index');
    }

    public function createSaleReport(){
        return view('SaleReports.add_sale_report');
    }

    public function storeSaleReport(){

    }

    public function editSaleReport(){

    }

    public function updateSaleReport(){

    }

    public function destroySaleReport(){

    }
}
