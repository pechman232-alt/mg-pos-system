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

class LogisticController extends Controller
{
    public function logisticIndex(Request $request){

        $keyword = $request->input('keyword');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $isSale = $request->input('isSale');

        $logistics = LogisticsModel::query()->orderBy('sale_reports.invoice_date', 'desc')
        ->leftJoin('sale_reports', 'sale_reports.logistics_id', '=', 'logistics.id')
        ->select('logistics.*', 'sale_reports.invoice_date');

        if (!is_null($isSale) && $isSale !== '') {
            $logistics->where('logistics.is_sale', $isSale);
        }

        if (!empty($keyword)) {
            $logistics->where(function ($query) use ($keyword) {
                $query->where('logistics.model_name', 'like', '%' . $keyword . '%')
                ->orWhere('logistics.vin_number', 'like', '%' . $keyword . '%')
                ->orWhere('logistics.engine_number', 'like', '%' . $keyword . '%');
            });
        }

        if (!empty($dateFrom) && !empty($dateTo)) {
            $logistics->whereBetween('sale_reports.invoice_date', [$dateFrom, $dateTo]);
        } elseif (!empty($dateFrom)) {
            $logistics->whereDate('sale_reports.invoice_date', '>=', $dateFrom);
        } elseif (!empty($dateTo)) {
            $logistics->whereDate('sale_reports.invoice_date', '<=', $dateTo);
        }

        $logistics = $logistics->paginate(10);
        $countLogistic = $logistics->total();

        return view('Logistics.logistic_index',[
            'logistics'=> $logistics,
            'countLogistic'=> $countLogistic,
            'date_from'=> $dateFrom,
            'date_to'=> $dateTo,
            'isSale'=> $isSale
        ]);
    }

    public function createLogistics(){
        return view('Logistics.add_logistic');
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
            return view('Logistics.edit_logistic',['logistics'=>$logistics]);
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function updateLogistics(Request $request, $id){
        try {
            $ID = decrypt($id);
            $logistics = LogisticsModel::where('id' , $ID)->firstorfail();
            $logistics->update($request->all());
            return redirect('logistics')->with('info', 'The Logistic is Updated successfully.');
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function destroyLogistics($id){
        try {
            $ID = decrypt($id);
            $logistics = LogisticsModel::where('id' , $ID)->firstorfail()->delete();
            return redirect('logistics')->with('warning', 'The Logistic is Deleted successfully.');
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
            return redirect('logistics')->with('success', value: 'Logistics imported successfully.');
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
                $request->input('date_to'),
            ), 'logistic_report.xlsx');
        } catch (\Exception $th){
            Log::info($th);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
