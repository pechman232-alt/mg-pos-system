<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PositionModel;
use App\Models\DepartmentModel;
use App\Http\Requests\EmployeeRequest;
use App\Models\CompanyModel;
use App\Models\BranchModel;
use App\Models\EmployeeModel;
use App\Imports\EmployeeImport;
use App\Exports\EmployeeExport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class EmployeeController extends Controller
{
    /**
     * បញ្ជីបុគ្គលិក រួមមានមុខងារ Live Search, Filter & Pagination
     */
    public function employeeIndex(Request $request) {
        $query = EmployeeModel::with(['company', 'department', 'position', 'branch'])
            ->where('status', '1');

        // 1. Logic សម្រាប់ Live Search (ស្វែងរកឈ្មោះ, ID, លេខទូរស័ព្ទ)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('first_name_kh', 'LIKE', "%{$search}%")
                  ->orWhere('last_name_kh', 'LIKE', "%{$search}%")
                  ->orWhere('user_id', 'LIKE', "%{$search}%")
                  ->orWhere('phone_number', 'LIKE', "%{$search}%");
            });
        }

        // 2. Logic សម្រាប់ Filter តាម Department ID
        if ($request->filled('dept')) {
            $query->where('department_id', $request->dept);
        }

        // 3. Logic សម្រាប់ Filter តាម Company ID
        if ($request->filled('company')) {
            $query->where('company_id', $request->company);
        }

        // បែងចែកទំព័រម្ដង ១៥ នាក់ និងរក្សាទុក Query String (សម្រាប់ប្តូរទំព័រតាម AJAX ឬ Link)
        $employees = $query->latest()->paginate(15)->withQueryString();

        // ទាញយកបញ្ជី Company និង Dept សម្រាប់បង្ហាញក្នុង Select Box Filter (លុប Status ចេញការពារ Error)
        $companies = CompanyModel::all();
        $departments = DepartmentModel::all();

        // ប្រសិនបើជាសំណើ AJAX ឱ្យវា Render តែផ្នែកតារាង (Partial Table)
        if ($request->ajax()) {
            return view('Employees.employee_table_partial', compact('employees'))->render();
        }

        // បោះ Variable ទាំងអស់ទៅកាន់ View Index
        return view('Employees.employee_index', compact('employees', 'companies', 'departments'));
    }

    /**
     * បង្ហាញរូបភាពបុគ្គលិកពី Storage
     */
    public function imageEmployee($path) {
        $storagePath = storage_path('app/public/imageEmployee/' . $path);
        if (!file_exists($storagePath)) {
            return response()->file(public_path('backend/assets/img/avatars/1.png'));
        }
        return response()->file($storagePath);
    }

    /**
     * ទម្រង់បង្កើតបុគ្គលិកថ្មី (Support Select2 Ajax)
     */
   /**
     * ទម្រង់បង្កើតបុគ្គលិកថ្មី (Support ទាំង View ធម្មតា និង Select2 AJAX)
     */
    public function createEmployee(Request $request) {
        // ១. ប្រសិនបើជាសំណើ AJAX (សម្រាប់ Select2 Search)
        if ($request->ajax()) {
            $type = $request->input('type');
            $search = $request->input('search');

            if ($type === 'employee') {
                $data = EmployeeModel::where('status', '1')
                    ->where(function($q) use ($search) {
                        $q->where('full_name', 'LIKE', "%{$search}%")
                          ->orWhere('user_id', 'LIKE', "%{$search}%");
                    })
                    ->limit(15)
                    ->get(['id', \DB::raw("CONCAT(full_name, ' (', user_id, ')') as text")]);
            } else {
                // សម្រាប់ Position និង Department
                $model = ($type === 'position') ? new PositionModel : new DepartmentModel;
                $data = $model::where('name', 'LIKE', "%{$search}%")
                    ->limit(15)
                    ->get(['id', 'name as text']);
            }
            return response()->json(['results' => $data]);
        }

        // ២. ប្រសិនបើជាការចុចបើកទំព័រធម្មតា (Normal GET Request)
        // យើងត្រូវទាញទិន្នន័យចាំបាច់សម្រាប់បង្ហាញក្នុង Dropdown
        $company = CompanyModel::all();
        $branch = BranchModel::all();

        // ប្រាកដថាបងមាន File view នៅទីតាំង: resources/views/Employees/add_employee.blade.php
        return view('Employees.add_employee', compact('company', 'branch'));
    }
    /**
     * រក្សាទុកបុគ្គលិកថ្មី
     */
    public function storeEmployee(EmployeeRequest $request) {
        try {
            $data = $request->validated();
            $data['full_name'] = trim($request->first_name . ' ' . $request->last_name);
            $data['status'] = '1';

            if ($request->hasFile('image_employee')) {
                $file = $request->file('image_employee');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(storage_path("app/public/imageEmployee"), $filename);
                $data['image_employee'] = $filename;
            }

            EmployeeModel::create($data);
            return redirect()->route('employees.index')->with('success', 'Personnel onboarded successfully.');

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Critical Error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * ទម្រង់កែសម្រួលព័ត៌មានបុគ្គលិក
     */
    public function editEmployee($id) {
        try {
            $decryptedId = decrypt($id);
            $employees = EmployeeModel::findOrFail($decryptedId);
            $company = CompanyModel::all();
            $branch = BranchModel::all();

            return view('Employees.edit_employee', compact('employees', 'company', 'branch'));
        } catch (Exception $e) {
            return redirect()->route('employees.index')->with('error', 'Invalid access node.');
        }
    }

    /**
     * Update ទិន្នន័យបុគ្គលិក
     */
    public function updateEmployee(Request $request, $id) {
        try {
            $decryptedId = decrypt($id);
            $employee = EmployeeModel::findOrFail($decryptedId);

            $data = $request->all();
            $data['full_name'] = trim($request->input('first_name') . ' ' . $request->input('last_name'));

            if ($request->hasFile('image_employee')) {
                // លុបរូបចាស់បើមាន
                if ($employee->image_employee && file_exists(storage_path("app/public/imageEmployee/" . $employee->image_employee))) {
                    @unlink(storage_path("app/public/imageEmployee/" . $employee->image_employee));
                }
                $file = $request->file('image_employee');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(storage_path("app/public/imageEmployee"), $filename);
                $data['image_employee'] = $filename;
            }

            $employee->update($data);
            return redirect()->route('employees.index')->with('success', 'Information synchronized.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Update Failed: ' . $e->getMessage());
        }
    }

    /**
     * លុបបុគ្គលិក (Soft Delete/Deactivate)
     */
    public function destroyEmployee($id) {
        try {
            $decryptedId = decrypt($id);
            EmployeeModel::where('id', $decryptedId)->update(['status' => 0]); 
            return redirect()->route('employees.index')->with('success', 'Personnel node archived.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Operation failed.');
        }
    }

    /**
     * មើលព័ត៌មានលម្អិត Dossier
     */
    public function viewEmployee($id) {
        try {
            $decryptedId = decrypt($id);
            $employees = EmployeeModel::with(['company', 'department', 'position', 'branch'])
                ->findOrFail($decryptedId);
            return view('Employees.view_employee', compact('employees'));
        } catch (Exception $e) {
            return redirect()->route('employees.index')->with('error', 'Dossier not found.');
        }
    }

    /**
     * Export បញ្ជីបុគ្គលិកជា Excel
     */
    public function exportEmployee() {
        return Excel::download(new EmployeeExport, 'Employees_Report_' . now()->format('d-m-Y') . '.xlsx');
    }

    /**
     * Import បុគ្គលិកពី Excel
     */
    public function importEmployee(Request $request) {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:10240']);
        try {
            Excel::import(new EmployeeImport, $request->file('file'));
            return redirect()->route('employees.index')->with('success', 'Database synchronized successfully.');
        } catch (Exception $e) {
            return redirect()->route('employees.index')->with('error', 'Sync Failed: ' . $e->getMessage());
        }
    }
}