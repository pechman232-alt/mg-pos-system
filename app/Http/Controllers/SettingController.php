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
            return $depart;
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
        }        
    }

    public function updateDepart($id){
        try {
            $ID = decrypt($id);
            $depart = DepartmentModel::where('id', $ID)->firstorfail();
            return $depart;
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

    public function destroyBranch($id){
        try {
            $ID = decrypt($id);
            $branch = BranchModel::where('id' , $ID)->firstorfail()->delete();
            return redirect('branchs')->with('warning', 'The Branch is Deleted Succesfully!');
        } catch (\Throwable $th){
            return redirect()->back()->with('error', $th->getMessage());
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

    public function editWaranty(){

    }

    public function updateWaranty(){

    }

    public function fnDeleteWaranty(){

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
}
