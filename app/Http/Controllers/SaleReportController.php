<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarImage;
use App\Models\CarModelModel;

class SaleReportController extends Controller
{
    public function saleReportIndex(){
        return view('SaleReports.sale_report_index');
    }

    public function createSaleReport(){
        $carImages = CarImage::selectRaw(
            '
            car_images.image_name,
            car_images.created_at,
            car_models.model_name as modelName,
            colors.color_name as colorName
            '    
        )
        ->join('car_models','car_models.id','=','car_images.model_id')
        ->join('colors','colors.id', '=', 'car_images.color_id')
        ->get();
        return view('SaleReports.add_sale_report',['carImages'=>$carImages]);
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
