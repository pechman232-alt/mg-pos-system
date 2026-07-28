<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LogisticRequest;
use App\Http\Requests\LogisticFileRequest;
use App\Models\LogisticsModel;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LogisticImport;
use App\Exports\LogisticExport;
use App\Models\CarModelModel;
use App\Models\ColorModel;
use DB;

class LogisticController extends Controller
{
    public function logisticIndex(Request $request){

        $keyword = $request->input('keyword');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $isSale = $request->input('isSale');

        // 🌟 1. ទាញយកចំនួនឡានសរុប សម្រាប់បង្ហាញលើ Dashboard ខាងលើ 🌟
        $countLogistic = LogisticsModel::count(); 
        $countSold = LogisticsModel::where('is_sale', '0')->count();
        $countInStock = LogisticsModel::where('is_sale', '1')->count();

        // 2. ទាញយកថ្ងៃ Invoice ចុងក្រោយគេពី sale_reports
        $latestInvoices = DB::table('sale_reports')
            ->select('logistics_id', DB::raw('MAX(invoice_date) as invoice_date'))
            ->groupBy('logistics_id');

        // 3. ទាញទិន្នន័យ Logistics
        $logistics = LogisticsModel::query()
            ->leftJoinSub($latestInvoices, 'sr', function ($join) {
                $join->on('sr.logistics_id', '=', 'logistics.id');
            })
            // ❌ លុបការ Join ជាមួយ car_models ចោល ព្រោះ model_name_id ជាអក្សរស្រាប់ហើយ
            ->select('logistics.*', 'sr.invoice_date')
            ->orderByDesc('logistics.id');

        // Filter តាម Status (Available / Sold)
        if ($isSale !== null && $isSale !== '') {
            $logistics->where('logistics.is_sale', $isSale);
        }
        
        // Filter តាមការ Search (VIN, Engine, Model)
        if (!empty($keyword)) {
            $logistics->where(function ($query) use ($keyword) {
                // 🌟 Search ចាប់យកឈ្មោះឡានពី model_name_id ផ្ទាល់តែម្តង
                $query->where('logistics.model_name_id', 'like', '%' . $keyword . '%')
                      ->orWhere('logistics.vin_number', 'like', '%' . $keyword . '%')
                      ->orWhere('logistics.engine_number', 'like', '%' . $keyword . '%');
            });
        }

        // Filter តាមថ្ងៃខែ Invoice
        if (!empty($dateFrom) && !empty($dateTo)) {
            $logistics->whereBetween('sr.invoice_date', [$dateFrom, $dateTo]);
        } elseif (!empty($dateFrom)) {
            $logistics->whereDate('sr.invoice_date', '>=', $dateFrom);
        } elseif (!empty($dateTo)) {
            $logistics->whereDate('sr.invoice_date', '<=', $dateTo);
        }

        // 4. ចែកទំព័រ Pagination
        $logistics = $logistics->paginate(50);
        
        return view('Logistics.logistic_index', compact(
            'logistics',
            'countLogistic',
            'countSold',
            'countInStock',
            'dateFrom',
            'dateTo',
            'isSale'
        ));
    }

    public function createLogistics(){
        $carModels = CarModelModel::get();
        $colors = ColorModel::get();
        return view('Logistics.add_logistic',['carModels'=>$carModels, 'colors'=>$colors]);
    }

    public function storeLogistics(LogisticRequest $request){
        try {
            $validated = $request->validated();
            $validated['remark'] = $request->input('remark');
            LogisticsModel::create($validated);
            return redirect('logistics/create')->with('success', 'The Logistic is created successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function editLogistics($id){
        try {
            $ID = decrypt($id);
            $logistics = LogisticsModel::where('id' , $ID)->first();
            $carModels = CarModelModel::get();
            $colors = ColorModel::get();
            return view('Logistics.edit_logistic',['logistics'=>$logistics, 'carModels'=>$carModels, 'colors'=>$colors]);
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function updateLogistics(Request $request, $id){
        try {
            $ID = decrypt($id);
            $logistics = LogisticsModel::where('id' , $ID)->firstorfail();
            $logistics->update($request->all());
            return redirect('logistics')->with('success', 'The Logistic is Updated successfully.');
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function destroyLogistics($id){
        try {
            $ID = decrypt($id);
            $logistics = LogisticsModel::where('id' , $ID)->firstorfail()->delete();
            return redirect('logistics')->with('success', 'The Logistic is Deleted successfully.');
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function importLogistic(Request $logisticFileRequest){
        try {
            if (!$logisticFileRequest->hasFile('file')) {
                return redirect()->back()->with('error', 'Please Import File Excel!');
            }
            Excel::import(new LogisticImport, $logisticFileRequest->file('file'));
            return redirect('logistics')->with('success', 'Logistics imported successfully.');
        } catch (\Exception $th){
            Log::info($th);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function exportLogistic(Request $request){
        try {
            return Excel::download(new LogisticExport(
                $request->input('isSale'),
                $request->input('date_from'),
                $request->input('date_to')
            ), 'logistic_report.xlsx');
        } catch (\Exception $th){
            Log::info($th);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}