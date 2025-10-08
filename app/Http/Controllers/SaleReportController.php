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

    public function viewSaleReport($id){
        $ID = decrypt($id);
        $saleReports = SaleReportModel::selectRaw(
            'sale_reports.id,
            sale_reports.customer_name,
            sale_reports.brand_name,
            sale_reports.phone_number,
            sale_reports.gender,
            sale_reports.dob,
            sale_reports.age,
            sale_reports.vihicle_battery,
            sale_reports.vpc_vdr,
            sale_reports.plate_number,
            sale_reports.offer_uv_film,
            sale_reports.offer_floor_mat,
            sale_reports.side_step,
            sale_reports.auto_tailgate,
            sale_reports.rear_spoiler,
            sale_reports.certificate_engine_oil,
            sale_reports.pro_offer_oil,
            sale_reports.pro_offer_oil_date,
            sale_reports.source_id,
            sale_reports.contract_no,
            sale_reports.contract_date,
            sale_reports.invoice_no,
            sale_reports.invoice_date,
            sale_reports.delivery_date,
            sale_reports.price,
            sale_reports.follow_up,
            sale_reports.attach_file,

            provinces.name as address,
            employees.full_name as sale_person,
            member_cards.member_card as memberCard,
            car_images.image_name as car_image,
            car_models.model_name as model_name,
            colors.color_name as color_name,
            warranty.name as warranty,

            logistics.model_year as modelYear,
            logistics.vin_number as vinNumber,
            logistics.engine_number as engineNumber
            '
        )
        ->join('car_images', 'car_images.id', '=', 'sale_reports.image')
        ->join('car_models', 'car_models.id', '=', 'car_images.model_id')
        ->join('colors', 'colors.id', '=' ,'car_models.color_id')
        ->join('employees', 'employees.id', '=', 'sale_reports.employee_id')
        ->join('member_cards', 'member_cards.id', '=', 'sale_reports.member_card')
        ->join('provinces', 'provinces.id', '=', 'sale_reports.province_id')
        ->join('logistics', 'logistics.id', '=', 'sale_reports.logistics_id')
        ->join('warranty', 'warranty.id', '=', 'sale_reports.warranty_id')
        ->find($ID);
        return view('SaleReports.view_sale_report', ['saleReports'=>$saleReports]);
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
