<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DepartmentModel;
use App\Models\PositionModel;
use App\Models\BranchModel;
use App\Models\ProvinceModel;
use App\Models\CompanyModel;
use App\Models\WarrantyModel;
use App\Models\EngineOilModel;

class SettingController extends Controller
{
    // ------------- Department Controller -------------------
    public function departIndex(){
        $depart_lists = DepartmentModel::where('status', '=', '1')->get();
        return view('Settings.Departments.depart_index',['depart_lists'=>$depart_lists]);
    }

    public function formDepart(){
        return view('Settings.Departments.add_depart');
    }

    public function createDepart(Request $request){
        $request->validate(
            ['name' => 'required'],
            ['name' => 'Please Enter Departments Name!']
        );
        $depart = new DepartmentModel;
        $depart->name = $request->name;
        $depart->save();
        return redirect('departments/create')->with('success', 'The Department is Created Succesfully!');
    }

    public function editDepart($id){
        try {
            $ID = decrypt($id);
            $depart = DepartmentModel::where('id', $ID)->first();
            return view('Settings.Departments.edit_depart',['depart'=>$depart]);
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        }        
    }

    public function updateDepart(Request $request, $id){
        try {
            $ID = decrypt($id);
            $depart = DepartmentModel::where('id', $ID)->firstorfail();
            $depart->update(attributes: $request->all());
            return redirect('departments')->with('info', 'The Department is Updated successfully.');
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        } 
    }

    public function destroyDepart($id){
        try {
            $ID = decrypt($id);
            $depart = DepartmentModel::where('id' , $ID)->firstorfail()->delete();
            return redirect('departments')->with('warning', 'The Department is Deleted Succesfully!');
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        } 
    }

    // ------------- Position Controller -------------------
    public function positionIndex(){
        $positions = PositionModel::where('status', '=', '1')->get();
        return view('Settings.Positions.position_index',['positions'=>$positions]);
    }

    public function formPosition(){
        return view('Settings.Positions.add_position');
    }

    public function createPosition(Request $request){
        $request->validate(
            ['name' => 'required'],
            ['name' => 'Please Enter Position Name!']
        );
        $position = new PositionModel();
        $position->name = $request->name;
        $position->save();
        return redirect('positions/create')->with('success', 'The Position is Created Succesfully!');
    }

    public function editPosition($id){
        try {
            $ID = decrypt($id);
            $position = PositionModel::where('id', $ID)->first();
            return view('Settings.Positions.edit_position',['position'=>$position]);
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        }        
    }

    public function updatePosition(Request $request, $id){
        try {
            $ID = decrypt($id);
            $position = PositionModel::where('id', $ID)->firstorfail();
            $position->update($request->all());
            return redirect('positions')->with('info', 'The Position is Updated successfully.');
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        } 
    }

    public function destroyPosition($id){
        try {
            $ID = decrypt($id);
            $position = PositionModel::where('id' , $ID)->firstorfail()->delete();
            return redirect('positions')->with('warning', 'The Position is Deleted Succesfully!');
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        } 
    }

    // ------------- Branch Controller -------------------
    public function branchIndex(){
        $branchs = BranchModel::get();
        return view('Settings.Branchs.branch_index', ['branchs'=>$branchs]);
    }

    public function formBranch(){
        return view('Settings.Branchs.add_branch');
    }

    public function createBranch(Request $request){
        $request->validate(
            ['branch_name' => 'required'],
            ['branch_name' => 'Please Enter Branch Name!']
        );

        $branchs = new BranchModel();
        $branchs->branch_name = $request->branch_name;
        $branchs->save();
        return redirect('branch/create')->with('success', 'The Branch is Created Succesfully!');
    }

    public function editBranch($id){
        try {
            $ID = decrypt($id);
            $branch = BranchModel::where('id', $ID)->first();
            return view('Settings.Branchs.edit_branch',['branch'=>$branch]);
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        } 
    }

    public function updateBranch(Request $request, $id){
        try {
            $ID = decrypt($id);
            $branch = BranchModel::where('id', $ID)->firstorfail();
            $branch->update($request->all());
            return redirect('branchs')->with('info', 'The Branch is Updated successfully.');
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        } 
    }

    public function destroyBranch($id)
    {
        try {
            $branch = \App\Models\BranchModel::findOrFail($id);
            $branch->delete();
    
            return redirect('/branchs')->with('success', 'លុបសាខាបានជោគជ័យ!');
        } catch (\Exception $e) {
            return redirect('/branchs')->with('error', 'មិនអាចលុបបានទេ! សាខានេះប្រហែលជាមានទិន្នន័យជាប់ពាក់ព័ន្ធ។');
        }
    }
    
    // ------------- Province Controller -------------------
    public function provinceIndex(){
        $provinces = ProvinceModel::get();
        return view('Settings.Provinces.province_index', ['provinces'=>$provinces]);
    }

    public function createProvince(){
        return view('Settings.Provinces.add_province');
    }

    public function storeProvince(Request $request){
        $request->validate(
            ['name' => 'required'],
            ['name' => 'Please Enter Province Name!']
        );
        $province = new ProvinceModel();
        $province->name = $request->name;
        $province->save();
        return redirect('provinces/create')->with('success', 'The Province is Created Succesfully!');
    }

    public function editProvince($id){
        try {
            $ID = decrypt($id);
            $province = ProvinceModel::where('id', $ID)->first();
            return view('Settings.Provinces.edit_province',['province'=>$province]);
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        } 
    }

    public function updateProvince(Request $request, $id){
        try {
            $ID = decrypt($id);
            $province = ProvinceModel::where('id', $ID)->firstorfail();
            $province->update($request->all());
            return redirect('provinces')->with('info', 'The Province is Updated successfully.');
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        } 
    }

    public function destroyProvince($id){
        try {
            $ID = decrypt($id);
            $province = ProvinceModel::where('id' , $ID)->firstorfail()->delete();
            return redirect('provinces')->with('warning', 'The Province is Deleted Succesfully!');
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        } 
    }

    // ------------- Company Controller -------------------
    public function companyIndex(){
        $companys = CompanyModel::get();
        return view('Settings.Companys.company_index',['companys'=>$companys]);
    }

    public function createCompany(){
        return view('Settings.Companys.add_company');
    }

    public function storeCompany(Request $request){
        $request->validate(
            ['company_name' => 'required'],
            ['company_name' => 'Please Enter Company Name!']
        );
        $company = new CompanyModel();
        $company->company_name = $request->company_name;
        $company->save();
        return redirect('companys/create')->with('success','The Company is Created Succesfully!');
    }

    public function editCompany($id){
        try {
            $ID = decrypt($id);
            $company = CompanyModel::where('id', $ID)->first();
            return view('Settings.Companys.edit_company',['company'=>$company]);
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        } 
    }

    public function updateCompany(Request $request, $id){
        try {
            $ID = decrypt($id);
            $company = CompanyModel::where('id', $ID)->firstorfail();
            $company->update($request->all());
            return redirect('companys')->with('info', 'The Company is Updated successfully.');
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        } 
    }

    public function destroyCompany($id){
        try {
            $ID = decrypt($id);
            $company = CompanyModel::where('id' , $ID)->firstorfail()->delete();
            return redirect('companys')->with('warning', 'The Company is Deleted Succesfully!');
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        } 
    }

    // ------------- Waranty Controller -------------------
    public function warantyIndex(){
        $warantys = WarrantyModel::get();
        return view('Settings.Warantys.waranty_index',['warantys'=>$warantys]);
    }

    public function createWaranty(){
        return view('Settings.Warantys.add_waranty');
    }
    
    public function storeWaranty(Request $request){
        $request->validate(
            ['name' => 'required'],
            ['name' => 'Please Enter Waranty.']
        );
        $waranty = new WarrantyModel();
        $waranty->name = $request->name;
        $waranty->save();
        return redirect('warantys/create')->with('success','The Waranty is Created Succesfully!');
    }

    public function editWaranty($id){
        try {
            $ID = decrypt($id);
            $waranty = WarrantyModel::where('id', $ID)->first();
            return view('Settings.Warantys.edit_waranty',['waranty'=>$waranty]);
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        } 
    }

    public function updateWaranty(Request $request, $id){
        try {
            $ID = decrypt($id);
            $waranty = WarrantyModel::where('id', $ID)->firstorfail();
            $waranty->update($request->all());
            return redirect('warantys')->with('info', 'The Waranty is Updated successfully.');
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        } 
    }

    public function destroyWaranty($id){
        try {
            $ID = decrypt($id);
            $waranty = WarrantyModel::where('id' , $ID)->firstorfail()->delete();
            return redirect('warantys')->with('warning', 'The Waranty is Deleted Succesfully!');
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        } 
    }

    // ------------- Engine Oil Controller -------------------
    public function engineOilIndex(){
        $engineOils = EngineOilModel::get();
        return view('Settings.EngineOils.engine_oil_index', ['engineOils'=>$engineOils]);

    }

    public function createEngineOil(){
        return view('Settings.EngineOils.add_engine_oil');
    }

    public function storeEngineOil(Request $request){
        $request->validate(
            ['name' => 'required'],
            ['name' => 'Please Enter Engine Oil.']
        );
        $engine_oil = new EngineOilModel();
        $engine_oil->name = $request->name;
        $engine_oil->save();
        return redirect('engineOils/create')->with('success','The Engine Oil is Created Succesfully!');
    }

    public function editEngineOil($id){
        try {
            $ID = decrypt($id);
            $engine_oil = EngineOilModel::where('id', $ID)->first();
            return view('Settings.EngineOils.edit_engine_oil',['engine_oil'=>$engine_oil]);
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        } 
    }

    public function updateEngineOil(Request $request, $id){
        try {
            $ID = decrypt($id);
            $engine_oil = EngineOilModel::where('id', $ID)->firstorfail();
            $engine_oil->update($request->all());
            return redirect('engineOils')->with('info', 'The Engine Oil is Updated successfully.');
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        } 
    }

    public function destroyEngineOil($id){
        try {
            $ID = decrypt($id);
            $engine_oil = EngineOilModel::where('id' , $ID)->firstorfail()->delete();
            return redirect('engineOils')->with('warning', 'The Engine Oil is Deleted Succesfully!');
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        } 
    }
}