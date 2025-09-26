<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PositionModel;
use App\Models\DepartmentModel;
use App\Http\Requests\EmployeeRequest;
use App\Models\CompanyModel;
use App\Models\BranchModel;
use App\Models\EmployeeModel;

class EmployeeController extends Controller
{
    public function imageEmployee($path){
        $storagePath = storage_path('app/public/imageEmployee/'.$path);
        if (!file_exists($storagePath)) {
            abort(404, 'File ImageEmployee not found');
        }
        return response()->file($storagePath);
    }
    public function employeeIndex(){
        $employees = EmployeeModel::selectRaw(
            'employees.id,
            employees.full_name,
            employees.gender,
            positions.name as postionName,
            employees.user_id,
            companies.company_name as companyName,
            departments.name as departmentName,
            employees.activate,
            employees.image_employee
            '
        )
        ->join('companies', 'companies.id', '=', 'employees.company_id')
        ->join('departments', 'departments.id','=','employees.department_id')
        ->join('positions', 'positions.id','=', 'employees.position_id')
        ->join('branches', 'branches.id','=', 'employees.branch_id')
        ->where('employees.status','=', '1')
        ->get();
        return view('Employees.employee_index',['employees'=>$employees]);
    }

    public function createEmployee(Request $request){

        if ($request->ajax()) {
            $type = $request->input('type'); // Determine type of data (position or department)
            $search = $request->input('search');

            if ($type === 'position') {
                $positions = PositionModel::where('status', '=', '1')
                    ->where('name', 'LIKE', "%{$search}%")
                    ->select('id', 'name')
                    ->get();
                return response()->json($positions);
            }

            if ($type === 'department') {
                $departments = DepartmentModel::where('status', '=', '1')
                    ->where('name', 'LIKE', "%{$search}%")
                    ->select('id', 'name')
                    ->get();
                return response()->json($departments);
            }
        }
        
        $company = CompanyModel::get();
        $branch = BranchModel::get();

        return view('Employees.add_employee',['company'=>$company, 'branch'=>$branch]);
    }

    public function storeEmployee(EmployeeRequest $employeeRequest){
        try {
            $validated = $employeeRequest->validated();
            $dob = $validated['dob'];
            $age = \Carbon\Carbon::parse($dob)->age;
            $validated['age'] = $age;
            $validated['note'] = $employeeRequest->input('note');
            $validated['full_name'] = $employeeRequest->input('first_name') . ' ' . $employeeRequest->input('last_name');

            if ($employeeRequest->hasFile('image_employee')) {
                $file = $employeeRequest->file('image_employee');
                $name = md5($file->getFilename() . time()) . '.' . $file->getClientOriginalExtension();
                $file_dir = 'imageEmployee';
                $file->move(storage_path("app/public/$file_dir"), $name);
                $validated['image_employee'] = $name;
            }
            // $validated['status'] = 1;
            EmployeeModel::create($validated);
            return redirect('employees/create')->with('success', 'The Employee is created successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function editEmployee($id){
        
    }

    public function updateEmployee(Request $request, $id){

    }

    public function destroyEmployee(){

    }

    public function viewEmployee($id){
        try {
            $ID = decrypt($id);
            $employees = EmployeeModel::selectRaw(
                'employees.full_name,
                employees.first_name,
                employees.last_name,
                employees.first_name_kh,
                employees.last_name_kh,
                employees.image_employee,
                employees.user_id,
                employees.gender,
                employees.age,
                employees.dob,
                employees.contract,
                employees.group_domain,
                employees.phone_number,
                employees.official_userid,
                employees.official_pass,
                employees.s_user_id,
                employees.s_pass,
                employees.m_user_id,
                employees.m_pass,
                employees.hr_user_id,
                employees.hr_pass,
                employees.rapp_user_id,
                employees.rapp_pass,
                employees.user_id_data_sheet,
                employees.data_sheet_pass,
                employees.extension_id,
                employees.register_pass,
                employees.access_door,
                employees.activate,
                positions.name as postionName,
                companies.company_name as companyName,
                departments.name as departmentName,
                branches.branch_name as branchName
                '
            )
            ->join('companies', 'companies.id', '=', 'employees.company_id')
            ->join('departments', 'departments.id','=','employees.department_id')
            ->join('positions', 'positions.id','=', 'employees.position_id')
            ->join('branches', 'branches.id','=', 'employees.branch_id')
            ->find($ID);
            return view('Employees.view_employee',['employees'=>$employees]);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
