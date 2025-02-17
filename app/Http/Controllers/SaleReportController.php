<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarImage;
use App\Models\CarModelModel;
use App\Models\BranchModel;
use App\Models\LogisticsModel;
use App\Models\WarrantyModel;
use App\Models\EmployeeModel;
use App\Models\ProvinceModel;
use App\Http\Requests\SaleReportRequest;

class SaleReportController extends Controller
{
    public function saleReportIndex(){
        return view('SaleReports.sale_report_index');
    }

    public function createSaleReport(Request $request){
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

        if ($request->ajax()) {
            $type = $request->input('type'); // Determine type of data (position or department)
            $search = $request->input('search');

            if ($type === 'logistic') {
                $logistics = LogisticsModel::where('status', '=', '1')
                    ->where(function ($query) use ($search) {
                    $query->where('model_name', 'LIKE', "%{$search}%")
                        ->orWhere('vin_number', 'LIKE', "%{$search}%")
                        ->orWhere('engine_number', 'LIKE', "%{$search}%");
                    })
                    ->select('id', 'model_name', 'vin_number', 'engine_number')
                    ->get();
                return response()->json($logistics);
            }

        }

        $branchs = BranchModel::all();
        $warrantys = WarrantyModel::all();
        $provinces = ProvinceModel::all();
        $employees = EmployeeModel::where('position_id','=', '2')->get();
        return view('SaleReports.add_sale_report',['carImages'=>$carImages, 'branchs'=>$branchs, 'warrantys'=>$warrantys,'employees'=>$employees, 'provinces'=>$provinces]);
    }

    public function storeSaleReport(SaleReportRequest $request){
        try {

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function editSaleReport(){

    }

    public function updateSaleReport(){

    }

    public function destroySaleReport(){

    }
}
