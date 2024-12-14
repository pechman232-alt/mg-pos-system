<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LogisticRequest;
use App\Http\Requests\LogisticFileRequest;
use App\Models\LogisticsModel;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LogisticImport;

class LogisticController extends Controller
{
    public function logisticIndex(){
        $logistics = LogisticsModel::get();
        return view('Logistics.logistic_index',['logistics'=>$logistics]);
    }

    public function createLogistics(){
        return view('Logistics.add_logistic');
    }

    public function storeLogistics(LogisticRequest $request){
        try {
            $validated = $request->validated();

            // LogisticsModel::created($validated);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    // Import and Export Logistic
    public function importLogistic(LogisticFileRequest $logisticFileRequest){
        try {

            // if (!$logisticFileRequest->hasFile('file')) {
            //     return redirect()->back()->with('error', 'Please import a file.');
            // }
            
            Excel::import(new LogisticImport, $logisticFileRequest->file('file'));
            return redirect('logistics')->with('success', value: 'Logistics imported successfully.');
        } catch (\Exception $th){
            Log::info($th);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

}
