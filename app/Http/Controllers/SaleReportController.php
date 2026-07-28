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
use App\Imports\SaleReportImport; 
use App\Exports\SaleReportTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SaleReportController extends Controller
{
    // ==========================================
    // 🌟 មុខងារ Dashboard (បង្ហាញ Top 3)
    // ==========================================
    public function dashboard(Request $request) {
        
        $baseQuery = SaleReportModel::where(function($q) { 
            $q->where('sale_reports.status', '!=', 0)->orWhereNull('sale_reports.status'); 
        });
    
        if ($request->filled('month')) {
            $baseQuery->where('sale_reports.invoice_date', 'like', $request->month . '%');
        }
    
        // ១. សាខាលក់ដាច់ Top 5 (ដកសាខា Service ចេញពី Database តែម្តង)
        $topBranches = (clone $baseQuery)->join('branches', 'branches.id', '=', 'sale_reports.branch_id')
            ->where('branches.branch_name', 'not like', '%Service%') // 🌟 លាក់ Service
            ->select('branches.branch_name', DB::raw('COUNT(sale_reports.id) as total_qty'), DB::raw('SUM(sale_reports.price) as total_revenue'))
            ->groupBy('branches.branch_name')
            ->orderByDesc('total_qty')
            ->take(5) // 🌟 យក ៥
            ->get();
    
        // ២. អ្នកលក់ដាច់ Top 5
        $topSalePersons = (clone $baseQuery)->join('employees', 'employees.id', '=', 'sale_reports.employee_id')
            ->select('employees.full_name as sale_person', DB::raw('COUNT(sale_reports.id) as total_qty'))
            ->groupBy('employees.full_name')
            ->orderByDesc('total_qty')
            ->take(5) // 🌟 យក ៥
            ->get();
    
        // ៣. ម៉ូដែលឡានលក់ដាច់ Top 5
        $topModels = (clone $baseQuery)->join('logistics', 'logistics.id', '=', 'sale_reports.logistics_id')
            ->select('logistics.model_name_id as model_name', DB::raw('COUNT(sale_reports.id) as total_qty'))
            ->groupBy('logistics.model_name_id')
            ->orderByDesc('total_qty')
            ->take(5) // 🌟 យក ៥
            ->get();
    
        // ៤. ខេត្តដែលទិញច្រើនជាងគេ Top 5 (🌟 ថ្មី)
        $topProvinces = (clone $baseQuery)->join('provinces', 'provinces.id', '=', 'sale_reports.province_id')
            ->select('provinces.name as province_name', DB::raw('COUNT(sale_reports.id) as total_qty'))
            ->groupBy('provinces.name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();
    
        // ៥. ប្រភពអតិថិជនមកពីណាខ្លះ Top 5 (🌟 ថ្មី)
        $topSources = (clone $baseQuery)
            ->select('sale_reports.source_id as source_name', DB::raw('COUNT(sale_reports.id) as total_qty'))
            ->whereNotNull('sale_reports.source_id')
            ->where('sale_reports.source_id', '!=', '')
            ->groupBy('sale_reports.source_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();
    
        // ៦. របាយការណ៍សរុបប្រចាំខែ
        $monthlySales = SaleReportModel::where(function($q) { 
                $q->where('sale_reports.status', '!=', 0)->orWhereNull('sale_reports.status'); 
            })
            ->whereNotNull('sale_reports.invoice_date')
            ->select(
                DB::raw("DATE_FORMAT(sale_reports.invoice_date, '%Y-%m') as month_year"),
                DB::raw('COUNT(sale_reports.id) as total_qty'),
                DB::raw('SUM(sale_reports.price) as total_revenue')
            )
            ->groupBy('month_year')
            ->orderByDesc('month_year')
            ->get();
    
        return view('SaleReports.sale_report_dashboard', compact(
            'topBranches', 
            'topSalePersons', 
            'topModels', 
            'monthlySales',
            'topProvinces', // 🌟 បោះទិន្នន័យខេត្តទៅកាន់ View
            'topSources'    // 🌟 បោះទិន្នន័យប្រភពទៅកាន់ View
        ));
    }

    public function saleReportIndex(Request $request) {

        $keyword = $request->input('keyword');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $invoiceDate = $request->input('invoice_date');

        $saleReports = SaleReportModel::query()
        ->with('logistics') 
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
                ->orWhere('logistics.engine_number','like', '%'. $keyword . '%')
                ->orWhere('logistics.model_name_id','like', '%'. $keyword . '%');
            });
        }

        if (!empty($dateFrom) && !empty($dateTo)) {
            $saleReports->whereBetween('sale_reports.invoice_date', [$dateFrom, $dateTo]);
        } elseif (!empty($dateFrom)) {
            $saleReports->whereDate('sale_reports.invoice_date', '>=', $dateFrom);
        } elseif (!empty($dateTo)) {
            $saleReports->whereDate('sale_reports.invoice_date', '<=', $dateTo);
        }

        $saleReports = $saleReports->paginate(50);
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
                sale_reports.logistics_id, /* 🌟 បន្ថែម ID នេះទើប Relation ក្នុង View ស្គាល់ 🌟 */
                sale_reports.customer_name,
                sale_reports.brand_name,
                sale_reports.phone_number,
                sale_reports.gender,
                sale_reports.dob,
                provinces.name as address,
                employees.full_name as sale_person,
                member_cards.member_card as memberCard,
                branches.branch_name as branchs,

                logistics.model_name_id as model_name,
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
            ->leftJoin('car_images', 'car_images.id', '=', 'sale_reports.image')
            ->leftJoin('member_cards', 'member_cards.id', '=', 'sale_reports.member_card_id')
            ->leftJoin('provinces', 'provinces.id', '=', 'sale_reports.province_id')
            ->leftJoin('branches', 'branches.id', '=', 'sale_reports.branch_id')
            ->leftJoin('warranty as warranty_mg', 'warranty_mg.id', '=', 'sale_reports.warranty_id')
            ->leftJoin('warranty as warranty_leap_motor', 'warranty_leap_motor.id', '=', 'sale_reports.warranty_leap_motor')
            ->findOrFail($ID);

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
                    ->where(function ($query) use ($search) {
                    $query->where('model_name_id', 'LIKE', "%{$search}%")
                        ->orWhere('vin_number', 'LIKE', "%{$search}%")
                        ->orWhere('engine_number', 'LIKE', "%{$search}%");
                    })
                    ->select(
                        'id',
                        'model_name_id as model_name', 
                        'vin_number',
                        'engine_number'
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

        // 🌟 កំណត់យកតែ ៤ សាខាប៉ុណ្ណោះ (Head Quarters, Siem Reap, Leap Motors, Battambong)
        $branchs = BranchModel::whereIn('branch_name', [
            'Head Quarters', 
            'Siem Reap', 
            'Leap Motors', 
            'Battambong'
        ])->get();

        $warrantys = WarrantyModel::all();
        $provinces = ProvinceModel::all();
        $engineOils = EngineOilModel::all(); 
        
        // 🌟 នេះគឺជាកន្លែងទាញយកទិន្នន័យ Sale Person (Department 17)
        $employees = EmployeeModel::where('department_id','=', '17')->get();

        return view('SaleReports.add_sale_report',[
            'carImages'=>$carImages, 
            'branchs'=>$branchs, 
            'warrantys'=>$warrantys,
            'employees'=>$employees, // បោះទៅ View 
            'provinces'=>$provinces,
            'engineOils'=>$engineOils, 
        ]);
    }

    public function storeSaleReport(SaleReportRequest $request){
        // 🌟 កែតម្រូវទី១៖ ប្រើ only() ដើម្បីទាញតែ Field ដែលមានក្នុង $fillable របស់ Model
        $data = $request->only([
            'image', 'branch_id', 'member_card_id', 'brand_name', 'customer_name', 
            'phone_number', 'gender', 'dob', 'source_id', 'logistics_id', 
            'plate_number', 'invoice_no', 'invoice_date', 'delivery_date', 
            'follow_up', 'feedback_command', 'warranty_id', 'warranty_leap_motor', 
            'employee_id', 'province_id', 'contract_no', 'contract_date', 
            'vpc_vdr', 'offer_license_plate', 'offer_uv_film', 'offer_floor_mat', 
            'side_step', 'auto_tailgate', 'rear_spoiler', 'certificate_engine_oil', 
            'pro_offer_oil', 'pro_offer_oil_date', 'vihicle_battery', 'price', 
            'remark_sa', 'status', 'created_by', 'model_name'
        ]);
    
        // 🌟 កែតម្រូវទី២៖ បន្ថែមការ Debug ត្រង់នេះដើម្បីប្រាកដថា logistics_id មកដល់មែនទេ?
        if (!$request->filled('logistics_id')) {
            Log::warning('SaleReport: logistics_id is missing from request!', ['request' => $request->all()]);
        }
    
        // ផ្នែក Date Fields
        $dateFields = ['dob', 'contract_date', 'delivery_date', 'pro_offer_oil_date', 'vihicle_battery'];
        foreach ($dateFields as $field) {
            $data[$field] = $request->filled($field) ? $request->$field : null;
        }
    
        // Checkboxes
        $checkboxes = ['offer_floor_mat', 'offer_uv_film', 'offer_license_plate', 'side_step', 'auto_tailgate', 'rear_spoiler'];
        foreach ($checkboxes as $box) {
            $data[$box] = $request->has($box) ? 'Yes' : 'No';
        }
    
        $data['vpc_vdr'] = $request->input('vpc_vdr', 'N/A');
        $data['certificate_engine_oil'] = $request->input('certificate_engine_oil', '0');
        $data['created_by'] = auth('users')->check() ? auth('users')->user()->full_name : 'System Admin';
    
        // File
        if ($request->hasFile('attach_file')) {
            $data['attach_file'] = $request->file('attach_file')->store('attachments', 'public');
        }
    
        try {
            DB::beginTransaction();
            
            $report = SaleReportModel::create($data);
    
            // Update Stock
            if ($request->filled('member_card_id')) {
                MemberCard::where('id', $request->member_card_id)->update(['is_sale' => 0]);
            }
            if ($request->filled('logistics_id')) {
                LogisticsModel::where('id', $request->logistics_id)->update(['is_sale' => 0]);
            }
    
            DB::commit();
            return redirect('sale-reports/create')->with('success', 'Report created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SaleReport Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function editSaleReport(Request $request, $id){
        try {
            $ID = decrypt($id);
            $saleReports = SaleReportModel::find($ID);

            $employees = EmployeeModel::where('department_id', '=', '17')->get();
            $provinces = ProvinceModel::all();
            $warrantys = WarrantyModel::all();
            $engineOils = EngineOilModel::all(); 
            
            // 🌟 កាត់យកតែ ៤ សាខា ដូចនៅខាង Create ដែរ
            $branchs = BranchModel::whereIn('branch_name', [
                'Head Quarters', 
                'Siem Reap', 
                'Leap Motors', 
                'Battambong'
            ])->get();
            
            // ការពារ Error ពេល Sale Report គ្មានរូបភាព
            $getCarImages = null;
            if($saleReports->image && $saleReports->image !== 'default.png') {
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
            }

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
                'engineOils'=>$engineOils, 
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

        $saleReport->updated_by = auth('users')->check() ? auth('users')->user()->full_name : 'System Admin';

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

    public function downloadTemplate()
    {
        try {
            return Excel::download(new SaleReportTemplateExport, 'Sale_Report_Import_Template.xlsx');
        } catch (\Exception $e) {
            Log::error('Template Download Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'មានបញ្ហាក្នុងការទាញយក Template៖ ' . $e->getMessage());
        }
    }

    public function quickUpdateRemark(Request $request) {
        try {
            $decryptedId = decrypt($request->report_id);
            $report = \App\Models\SaleReportModel::findOrFail($decryptedId);
            
            $newRemark = trim($request->remark_sa);
            
            if (!empty($newRemark)) {
                $existingRemarks = $report->remark_sa;
                
                if (empty($existingRemarks) || $existingRemarks === 'N/A') {
                    $report->remark_sa = "1. " . $newRemark;
                } else {
                    $lines = explode("\n", $existingRemarks);
                    $nextNumber = count($lines) + 1;
                    $report->remark_sa = $existingRemarks . "\n" . $nextNumber . ". " . $newRemark;
                }
                
                $report->created_by = auth('users')->check() ? auth('users')->user()->full_name : 'System Admin'; 
                $report->save();
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function quickUpdateFeedback(Request $request) {
        try {
            $decryptedId = decrypt($request->report_id);
            $report = \App\Models\SaleReportModel::findOrFail($decryptedId);
            
            $newFeedback = trim($request->feedback_command);
            
            if (!empty($newFeedback)) {
                $existingFeedback = $report->feedback_command;
                
                if (empty($existingFeedback) || $existingFeedback === 'N/A') {
                    $report->feedback_command = "1. " . $newFeedback;
                } else {
                    $lines = explode("\n", $existingFeedback);
                    $nextNumber = count($lines) + 1;
                    $report->feedback_command = $existingFeedback . "\n" . $nextNumber . ". " . $newFeedback;
                }
                
                $report->created_by = auth('users')->check() ? auth('users')->user()->full_name : 'System Admin'; 
                $report->save();
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function importSaleReport(Request $request) 
    {
        ini_set('max_execution_time', 600); 
        $request->validate([
            'import_file' => 'required|mimes:xlsx,xls,csv|max:20480'
        ]);
        
        try {
            Excel::import(new SaleReportImport, $request->file('import_file'));
            
            return response()->json([
                'success' => true, 
                'message' => 'ទិន្នន័យត្រូវបានបញ្ចូលជោគជ័យ!'
            ]);
            
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "ជួរទី " . $failure->row() . ": " . implode(', ', $failure->errors());
            }
            return response()->json(['errors' => ['file' => $errors]], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'កំហុសប្រព័ន្ធ: ' . $e->getMessage()
            ], 422);
        }

        
    }
    public function apiDashboard(Request $request) {
        // 🌟 ប្រើ Base Query មួយដើម្បីសន្សំសំចៃកូដ
        $baseQuery = \App\Models\SaleReportModel::where(function($q) { 
            $q->where('sale_reports.status', '!=', 0)->orWhereNull('sale_reports.status'); 
        });
    
        if ($request->filled('month')) {
            $baseQuery->whereRaw("DATE_FORMAT(sale_reports.invoice_date, '%Y-%m') = ?", [$request->month]);
        }
    
        // ១. Top Branches
        $topBranches = (clone $baseQuery)->join('branches', 'branches.id', '=', 'sale_reports.branch_id')
            ->where('branches.branch_name', 'not like', '%Service%')
            ->select('branches.id as branch_id', 'branches.branch_name', 
                     \Illuminate\Support\Facades\DB::raw('COUNT(sale_reports.id) as total_qty'))
            ->groupBy('branches.id', 'branches.branch_name')
            ->orderByDesc('total_qty')->take(5)->get();
    
        // ២. Top Sale Persons
        $topSalePersons = (clone $baseQuery)->join('employees', 'employees.id', '=', 'sale_reports.employee_id')
            ->select('employees.full_name as sale_person', \Illuminate\Support\Facades\DB::raw('COUNT(sale_reports.id) as total_qty'))
            ->groupBy('employees.full_name')->orderByDesc('total_qty')->take(5)->get();
    
        // ៣. Top Models
        $topModels = (clone $baseQuery)->join('logistics', 'logistics.id', '=', 'sale_reports.logistics_id')
            ->select('logistics.model_name_id as model_name', \Illuminate\Support\Facades\DB::raw('COUNT(sale_reports.id) as total_qty'))
            ->groupBy('logistics.model_name_id')->orderByDesc('total_qty')->take(5)->get();
    
        // ៤. 🌟 Top Provinces (នេះគឺជាអ្វីដែលបាត់បណ្តាលឱ្យ Error 500)
        $topProvinces = (clone $baseQuery)->join('provinces', 'provinces.id', '=', 'sale_reports.province_id')
            ->select('provinces.name as province_name', \Illuminate\Support\Facades\DB::raw('COUNT(sale_reports.id) as total_qty'))
            ->groupBy('provinces.name')->orderByDesc('total_qty')->take(5)->get();
    
        // ៥. Monthly Total
        $monthlyData = (clone $baseQuery)->select(
            \Illuminate\Support\Facades\DB::raw('COUNT(sale_reports.id) as total_qty'),
            \Illuminate\Support\Facades\DB::raw('SUM(sale_reports.price) as total_revenue')
        )->first();
    
        return response()->json([
            'success' => true,
            'monthlyTotal' => $monthlyData,
            'topBranches' => $topBranches,
            'topSalePersons' => $topSalePersons,
            'topModels' => $topModels,
            'topProvinces' => $topProvinces, // ឥឡូវនេះវាស្គាល់អថេរនេះហើយ!
        ]);
    }

    public function getBranchDetails(Request $request) {
        $branchId = $request->input('branch_id');
        $month = $request->input('month');

        $reports = \App\Models\SaleReportModel::with(['logistics'])
            ->where('branch_id', $branchId)
            ->where(function($q) { 
                $q->where('sale_reports.status', '!=', 0)->orWhereNull('sale_reports.status'); 
            });

        if ($request->filled('month')) {
            $reports->where('invoice_date', 'like', $month . '%');
        }

        return response()->json($reports->get());
    }


public function verifyVehicleForRegistration(Request $request) 
    {
        $request->validate([
            'vin_last_6' => 'required|string|min:6|max:6',
        ]);

        $vinLast6 = $request->input('vin_last_6');

        try {
            // ស្វែងរកទិន្នន័យឡានដែលបានលក់រួច (Close Deal) ដោយភ្ជាប់ជាមួយព័ត៌មានអតិថិជន
            $vehicle = SaleReportModel::join('logistics', 'logistics.id', '=', 'sale_reports.logistics_id')
                ->where(function($q) {
                    $q->where('sale_reports.status', '!=', 0)->orWhereNull('sale_reports.status');
                })
                ->where('logistics.vin_number', 'LIKE', '%' . $vinLast6)
                ->select(
                    'sale_reports.customer_name',
                    'sale_reports.phone_number',
                    'logistics.model_name_id as car_model',
                    'logistics.model_year',
                    'logistics.vin_number',
                    'logistics.engine_number'
                )
                ->first();

            if ($vehicle) {
                return response()->json([
                    'success' => true,
                    'found' => true,
                    'customer_name' => $vehicle->customer_name,
                    'phone_number' => $vehicle->phone_number,
                    'car_model' => $vehicle->car_model,
                    'model_year' => $vehicle->model_year,
                    'vin_number' => $vehicle->vin_number,
                    'engine_number' => $vehicle->engine_number,
                    'message' => 'ទិន្នន័យរថយន្តត្រូវបានផ្ទៀងផ្ទាត់ជោគជ័យ!'
                ], 200);
            }

            return response()->json([
                'success' => true,
                'found' => false,
                'message' => 'រកមិនឃើញលេខតួឡាន ៦ខ្ទង់នេះ នៅក្នុងប្រព័ន្ធលក់ឡានឡើយ។'
            ], 200);

        } catch (\Exception $e) {
            Log::error('API Verify VIN Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'កំហុសប្រព័ន្ធ៖ ' . $e->getMessage()
            ], 500);
        }
    }

}
