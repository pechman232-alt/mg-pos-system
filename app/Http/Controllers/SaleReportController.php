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
use App\Models\EngineOilModel;
use App\Models\SaleReportModel;
use App\Http\Requests\SaleReportRequest;
use App\Exports\SaleReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class SaleReportController extends Controller
{
    public function saleReportIndex(Request $request) {

        $keyword = $request->input('keyword');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $invoiceDate = $request->input('invoice_date');

        $saleReports = SaleReportModel::query()
        ->leftJoin('member_cards', 'member_cards.id', '=', 'sale_reports.member_card_id')
        ->leftJoin('logistics', 'logistics.id', '=', 'sale_reports.logistics_id')
        ->select('sale_reports.*' ,'member_cards.member_card')
        ->orderByDesc('sale_reports.invoice_date');

        if (!empty($keyword)) {
            $saleReports->where(function ($query) use ($keyword) {
                $query->orWhere('sale_reports.customer_name', 'like', '%' . $keyword . '%')
                ->orWhere('sale_reports.invoice_no', 'like', '%' . $keyword . '%')
                ->orWhere('sale_reports.phone_number', 'like', '%' . $keyword . '%')
                ->orWhere('member_cards.member_card', 'like', '%' . $keyword . '%')
                ->orWhere('logistics.vin_number','like', '%'. $keyword . '%')
                ->orWhere('logistics.engine_number','like', '%'. $keyword . '%');
            });
        }

        if (!empty($dateFrom) && !empty($dateTo)) {
            $saleReports->whereBetween('sale_reports.invoice_date', [$dateFrom, $dateTo]);
        } elseif (!empty($dateFrom)) {
            $saleReports->whereDate('sale_reports.invoice_date', '>=', $dateFrom);
        } elseif (!empty($dateTo)) {
            $saleReports->whereDate('sale_reports.invoice_date', '<=', $dateTo);
        }

        $saleReports = $saleReports->paginate(10);
        $countSaleReports = $saleReports->total();

        return view('SaleReports.sale_report_index',[
            'saleReports'=> $saleReports,
            'countSaleReports'=> $countSaleReports,
            'date_from'=> $dateFrom,
            'date_to'=> $dateTo,
            'invoice_date'=> $invoiceDate,
        ]);
    }

    public function viewSaleReport($id){
        try {
            $ID = decrypt($id);
            $saleReports = SaleReportModel::selectRaw(
                'sale_reports.id,
                sale_reports.customer_name,
                sale_reports.brand_name,
                sale_reports.phone_number,
                sale_reports.gender,
                sale_reports.dob,
                provinces.name as address,
                employees.full_name as sale_person,
                member_cards.member_card as memberCard,
                branches.branch_name as branchs,

                car_models.model_name as model_name,
                colors.color_name as color_name,
                logistics.model_year as modelYear,
                logistics.vin_number as vinNumber,
                logistics.engine_number as engineNumber,
                car_images.image_name,
                
                warranty_mg.name as warranty,
                sale_reports.vihicle_battery,
                sale_reports.vpc_vdr,
                warranty_leap_motor.name as warranty_leap_motor,

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
                sale_reports.feedback_command,
                sale_reports.attach_file,
                sale_reports.remark_sa
                '
            )
            ->leftJoin('employees', 'employees.id', '=', 'sale_reports.employee_id')
            ->leftJoin('logistics', 'logistics.id', '=', 'sale_reports.logistics_id')
            ->leftJoin('car_models', 'car_models.id', '=', 'logistics.model_name_id')
            ->leftJoin('car_images', 'car_images.id', '=', 'sale_reports.image')
            ->leftJoin('colors', 'colors.id', '=', 'car_images.color_id')
            ->leftJoin('member_cards', 'member_cards.id', '=', 'sale_reports.member_card_id')
            ->leftJoin('provinces', 'provinces.id', '=', 'sale_reports.province_id')
            ->leftJoin('branches', 'branches.id', '=', 'sale_reports.branch_id')
            ->leftJoin('warranty as warranty_mg', 'warranty_mg.id', '=', 'sale_reports.warranty_id')
            ->leftJoin('warranty as warranty_leap_motor', 'warranty_leap_motor.id', '=', 'sale_reports.warranty_leap_motor')
            ->find($ID);
            return view('SaleReports.view_sale_report', ['saleReports'=>$saleReports]);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function createSaleReport(Request $request){
        $carImages = CarImage::selectRaw(
            'car_images.id,
            car_images.image_name,
            car_images.created_at,
            car_models.model_name as modelName,
            colors.color_name as colorName'
        )
        ->join('car_models','car_models.id','=','car_images.model_id')
        ->join('colors','colors.id', '=', 'car_images.color_id')
        ->get();

        if ($request->ajax()) {
            $type = $request->input('type');
            $search = $request->input('search');

            if ($type === 'logistic') {
                $logistics = LogisticsModel::where('is_sale', '=', '1')
                    ->join('car_models', 'car_models.id', '=', 'logistics.model_name_id')
                    ->where(function ($query) use ($search) {
                    $query->where('car_models.model_name', 'LIKE', "%{$search}%")
                        ->orWhere('vin_number', 'LIKE', "%{$search}%")
                        ->orWhere('engine_number', 'LIKE', "%{$search}%");
                    })
                    // ->select('id', 'model_name', 'vin_number', 'engine_number')
                    ->select(
                        'logistics.id',
                        'car_models.model_name',
                        'logistics.vin_number',
                        'logistics.engine_number'
                    )
                    ->get();
                return response()->json($logistics);
            }

            if ($type === 'memberCard') {
                $memberCard = MemberCard::where('is_sale', '=', '1')
                    ->where(function ($query) use ($search) {
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
        $engineOil = EngineOilModel::all();

        $employees = EmployeeModel::where('department_id','=', '1')->get();

        return view('SaleReports.add_sale_report',[
            'carImages'=>$carImages, 
            'branchs'=>$branchs, 
            'warrantys'=>$warrantys,
            'employees'=>$employees, 
            'provinces'=>$provinces,
            'engineOil'=>$engineOil,
        ]);
    }

    public function storeSaleReport(SaleReportRequest $request){
        // try {
            
            $validated = $request->validated();
            $validated['remark_sa'] = $request->input('remark_sa');
            $validated['follow_up'] = $request->input('follow_up');
            $validated['feedback_command'] = $request->input('feedback_command');
            $validated['price'] = $request->input('price');
            $validated['contract_date'] = $request->input('contract_date');

            if ($request->hasFile('attach_file')) {
                $file = $request->file('attach_file');
                $path = $file->store('attachments', 'public');
                $validated['attach_file'] = $path;
            }

            $validated['created_by'] = auth(guard: 'users')->user()->full_name;
            // return $validated;

            SaleReportModel::create($validated);
            return redirect('sale-reports/create')->with('success', 'The Sale Reports is created successfully.');
        // } catch (\Throwable $th) {
        //     return redirect()->back()->with('error', $th->getMessage());
        // }
    }

    public function editSaleReport(Request $request, $id){
        try {
            $ID = decrypt($id);
            $saleReports = SaleReportModel::find($ID);

            // if ($request->ajax()) {
            //     $search = $request->input('search');
            //     $memberCard = MemberCard::where('is_sale', '=', '1')
            //         ->where('member_card', 'LIKE', "%{$search}%")
            //         ->select('id', 'member_card')
            //         ->get();
            //     if ($saleReports && $saleReports->member_card) {
            //         $selectedMemberCard = MemberCard::find($saleReports->member_card);
            //         if ($selectedMemberCard) {
            //             $memberCard->prepend($selectedMemberCard);
            //         }
            //     }
            //     return response()->json($memberCard);
            // }

            $employees = EmployeeModel::where('department_id','=', '1')->get();
            $provinces = ProvinceModel::all();
            $warrantys = WarrantyModel::all();
            $engineOil = EngineOilModel::all();
            $branchs = BranchModel::all();
            $getCarImages = CarImage::selectRaw(
                'car_images.id,
                car_images.image_name,
                car_images.created_at,
                car_models.model_name as modelName,
                colors.color_name as colorName'
            )->join('car_models','car_models.id','=','car_images.model_id')
            ->join('colors','colors.id', '=', 'car_images.color_id')
            ->where('car_images.id', $saleReports->image)
            ->first();

            $carImages = CarImage::selectRaw(
                'car_images.id,
                car_images.image_name,
                car_images.created_at,
                car_models.model_name as modelName,
                colors.color_name as colorName'
            )->join('car_models','car_models.id','=','car_images.model_id')
            ->join('colors','colors.id', '=', 'car_images.color_id')
            ->get();

            return view('SaleReports.edit_sale_report', [
                'saleReports'=>$saleReports, 
                'employees'=>$employees, 
                'provinces'=>$provinces, 
                'carImages'=>$carImages,
                'getCarImages'=>$getCarImages,
                'warrantys'=>$warrantys,
                'engineOil'=>$engineOil,
                'branchs'=>$branchs,
            ]);
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function updateSaleReport(Request $request, $id){
        $ID = decrypt($id);
        $saleReport = SaleReportModel::findOrFail($ID);

        if ($saleReport->member_card_id && $saleReport->member_card_id != $request->member_card_id) {
            MemberCard::where('id', $saleReport->member_card_id)->update(['is_sale' => 1]);
        }

        if ($saleReport->logistics_id && $saleReport->logistics_id != $request->logistics_id){
            LogisticsModel::where('id', $saleReport->logistics_id)->update(['is_sale' => 1]);
        }

        MemberCard::where('id', $request->member_card_id)->update(['is_sale' => 0]);
        LogisticsModel::where('id', $request->logistics_id)->update(['is_sale' => 0]);

        $saleReport->updated_by = auth('users')->user()->full_name;

        if ($request->filled('image')) { 
            $saleReport->image = $request->image;
        }

        $updateData = $request->except(['image']);

        $saleReport->update($updateData);
        return redirect('sale-reports')->with('info', 'The Sale Reports is Updated successfully.');
    }

    public function destroySaleReport($id){
        try {
            $ID = decrypt($id);

            $saleReport = SaleReportModel::findOrFail($ID);
            $saleReport->status = 0;
            $saleReport->save();

            LogisticsModel::where('id', $saleReport->logistics_id)->update(['is_sale' => 1]);
            MemberCard::where('id', $saleReport->member_card)->update(['is_sale' => 1]);

            return redirect('sale-reports')->with('error', 'The Sale Reports is Deleted successfully.');
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function exportSaleReport(Request $request){
        try {
            return Excel::download(new SaleReportExport(
                $request->input('date_from'),
                $request->input('date_to'),
            ), 'sale_report_export.xlsx');
        } catch (\Exception $th){
            Log::info($th);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}