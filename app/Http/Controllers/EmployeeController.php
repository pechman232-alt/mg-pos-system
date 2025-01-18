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
    public function employeeIndex(){
        
        return view('Employees.employee_index');
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
            $validated['status'] = 1;
            EmployeeModel::create($validated);
            return redirect('employees/create')->with('success', 'The Employee is created successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function editEmployee(){
        
    }
}
