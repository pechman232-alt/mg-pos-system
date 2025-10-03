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
use App\Models\MemberCard;
use App\Models\SaleReportModel;
use App\Http\Requests\SaleReportRequest;

class SaleReportController extends Controller
{
    public function saleReportIndex(){
        $saleReports = SaleReportModel::get();
        return view('SaleReports.sale_report_index',['saleReports'=> $saleReports]);
    }

    public function createSaleReport(Request $request){
        $carImages = CarImage::selectRaw(
            '
            car_images.id,
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

            if ($type === 'memberCard') {
                $memberCard = MemberCard::where(function ($query) use ($search) {
                    $query->where('member_card', 'LIKE', "%{$search}%");
                    })
                    ->select('id', 'member_card')
                    ->get();
                return response()->json($memberCard);
            }

        }

        $branchs = BranchModel::all();
        $warrantys = WarrantyModel::all();
        $provinces = ProvinceModel::all();
        $employees = EmployeeModel::where('department_id','=', '17')->get();
        return view('SaleReports.add_sale_report',['carImages'=>$carImages, 'branchs'=>$branchs, 'warrantys'=>$warrantys,'employees'=>$employees, 'provinces'=>$provinces]);
    }

    public function storeSaleReport(SaleReportRequest $request){
        try {
            $validated = $request->validated();
            $dob = $validated['dob'];
            $age = \Carbon\Carbon::parse($dob)->age;
            $validated['age'] = $age;
            $validated['remark_sa'] = $request->input('remark_sa');
            if ($request->hasFile('attach_file')) {
                $file = $request->file('attach_file');
                $path = $file->store('attachments', 'public');
                $validated['attach_file'] = $path;
            }
            $validated['status'] = 1;
            SaleReportModel::create($validated);
            return redirect('sale-reports/create')->with('success', 'The Sale Reports is created successfully.');
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
