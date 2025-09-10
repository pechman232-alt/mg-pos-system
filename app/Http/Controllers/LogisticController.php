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

    // Import and Export Logistic
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

}
