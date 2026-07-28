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
/**
     * បញ្ជីបុគ្គលិក រួមមានមុខងារ Live Search, Filter & Pagination
     */
    public function employeeIndex(Request $request)
    {
        $query = EmployeeModel::query();
    
        // 🌟 1. Logic ថ្មីសម្រាប់ Filter Status និងថ្ងៃខែចូលធ្វើការ 🌟
        if ($request->has('status') && $request->status !== '') {
            $today = \Carbon\Carbon::today();
            
            if ($request->status === 'upcoming') {
                // បុគ្គលិកដែលមិនទាន់ដល់ថ្ងៃចូលធ្វើការ (ថ្ងៃកុងត្រាធំជាងថ្ងៃនេះ)
                $query->where('status', 1)->whereDate('contract', '>', $today);
            } elseif ($request->status === 'active') {
                // បុគ្គលិកដែលកំពុងធ្វើការ (ថ្ងៃកុងត្រាស្មើ ឬតូចជាងថ្ងៃនេះ ឬអត់មានថ្ងៃកុងត្រា)
                $query->where('status', 1)->where(function($q) use ($today) {
                    $q->whereDate('contract', '<=', $today)->orWhereNull('contract');
                });
            } elseif ($request->status === '0') {
                // បុគ្គលិកឈប់
                $query->where('status', 0);
            }
        }

        // 2. Live Search (ស្វែងរកឈ្មោះ, ID, លេខទូរស័ព្ទ)
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

        // 3. Filter តាម Company
        if ($request->filled('company')) {
            $query->where('company_id', $request->company);
        }

        // 4. Filter តាម Branch 
        if ($request->filled('branch')) {
            $query->where('branch_id', $request->branch);
        }

        // 5. Filter តាម Department
        if ($request->filled('dept')) {
            $query->where('department_id', $request->dept);
        }

        // បែងចែកទំព័រម្ដង ១៥ នាក់ និងរក្សាទុក Query String
        $employees = $query->latest()->paginate(15)->withQueryString();

        // ទាញយកទិន្នន័យសម្រាប់បង្ហាញក្នុង Select Box Filters
        $companies = CompanyModel::all();
        $departments = DepartmentModel::all();
        $branches = BranchModel::all(); 

        // បោះ Variable ទាំងអស់ទៅកាន់ View Index
        return view('Employees.employee_index', compact('employees', 'companies', 'departments', 'branches'));
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
     * ទម្រង់បង្កើតបុគ្គលិកថ្មី 
     */
    public function createEmployee(Request $request) {
        // ១. សម្រាប់សំណើ AJAX (ឧទាហរណ៍៖ Select2 លោតឈ្មោះដោយស្វ័យប្រវត្តិ)
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
                $model = ($type === 'position') ? new PositionModel : new DepartmentModel;
                $data = $model::where('name', 'LIKE', "%{$search}%")
                    ->limit(15)
                    ->get(['id', 'name as text']);
            }
            return response()->json(['results' => $data]);
        }

        // ២. ទាញទិន្នន័យសម្រាប់ទម្លាក់ចូល Select Box ក្នុង Form បង្កើតថ្មី
        $company     = CompanyModel::all();
        $branch      = BranchModel::all();
        $departments = DepartmentModel::all(); 
        $positions   = PositionModel::all();   

        return view('Employees.add_employee', compact('company', 'branch', 'departments', 'positions'));
    }

    /**
     * 🌟 មុខងារថ្មីសម្រាប់ Generate Employee ID តាម Contract Date 🌟
     */
  /**
     * 🌟 មុខងារ Generate Employee ID (បន្តលេខរៀងជានិច្ច) 🌟
     */
    public function generateEmployeeId(Request $request)
    {
        $contractDate = $request->input('date');

        if (!$contractDate) {
            return response()->json(['success' => false, 'message' => 'No date provided.']);
        }

        // 1. បង្កើតកូដថ្ងៃខែថ្មី (ឧទាហរណ៍ 04-03-2026 -> 040326)
        $dateCode = \Carbon\Carbon::parse($contractDate)->format('dmy');
        $prefix = 'TF-' . $dateCode . '-'; 

        // 2. 🌟 រកមើលបុគ្គលិកចុងក្រោយគេបង្អស់ ដោយមិនខ្វល់ពីថ្ងៃខែ (ស្វែងរកដោយ TF%-% ឬ TF-)
        // យើងទាញយក ID ដែលមានទម្រង់មានសញ្ញាដក '-'
        $lastEmployee = \App\Models\EmployeeModel::where('user_id', 'LIKE', 'TF%-%')
                                     ->orderBy('id', 'desc') // យកអ្នក Add ក្រោយគេបង្អស់
                                     ->first();

        if ($lastEmployee) {
            // ឧទាហរណ៍ lastId = "TF040121-063" ឬ "TF-150826-063"
            $lastId = $lastEmployee->user_id;
            
            // កាត់យកលេខចុងក្រោយ
            $parts = explode('-', $lastId);
            $lastNumber = (int) end($parts); // ទាញយកលេខ 063 មកបំប្លែងជា 63
            
            $newNumber = $lastNumber + 1; // 63 + 1 = 64
        } else {
            // បើកន្លែងធ្វើការថ្មីស្រឡាង អត់ទាន់មានបុគ្គលិកសោះ
            $newNumber = 1; 
        }

        // 3. តភ្ជាប់ Prefix ថ្ងៃខែថ្មី ជាមួយលេខរៀងថ្មី
        // លទ្ធផល: TF-040326-064
        $newId = $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'success' => true,
            'new_id' => $newId
        ]);
    }
    public function storeEmployee(EmployeeRequest $request) {
        try {
            $data = $request->validated();
            
            // ការពារ Error ប្រសិនបើ first_name ឬ last_name អត់មានទិន្នន័យ
            $data['full_name'] = trim(($request->first_name ?? '') . ' ' . ($request->last_name ?? ''));
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
            
            $company     = CompanyModel::all();
            $branch      = BranchModel::all();
            $departments = DepartmentModel::all(); 
            $positions   = PositionModel::all();   

            return view('Employees.edit_employee', compact('employees', 'company', 'branch', 'departments', 'positions'));
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
            $data['full_name'] = trim(($request->input('first_name') ?? '') . ' ' . ($request->input('last_name') ?? ''));

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
     * លុបបុគ្គលិក (បិទ Status ជាការលាក់ទិន្នន័យ)
     */
    public function destroyEmployee($id) {
        try {
            $decryptedId = decrypt($id);
            $employee = EmployeeModel::findOrFail($decryptedId);
            
            // គ្រាន់តែបិទ Status ទៅ 0 ដើម្បីលាក់ទិន្នន័យចេញពីបញ្ជី (ការពារដាច់ប្រវត្តិលក់)
            $employee->update(['status' => 0]); 
            
            return redirect()->route('employees.index')->with('success', 'Personnel node deactivated.');
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
     * 🌟 Download Excel Template (កូដថ្មី) 🌟
     */
   /**
     * Download Excel Template តាមទម្រង់ File Employees_Report_04-05-2026.xlsx
     */
    public function downloadTemplate() 
    {
        // កំណត់ Header ទាំង ១៤ តាម File របាយការណ៍របស់បង
        $headings = [
            [
                'គោលត្តនាម', 'នាម', 'First Name', 'Last Name', 'Full Name', 
                'Sex', 'Position', 'Date of Birth', 'User.ID', 'Company', 
                'Branch', 'Department', 'Phone Number', 'Status'
            ]
        ];

        return Excel::download(new class($headings) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithStyles {
            private $data;
            public function __construct($data) { $this->data = $data; }
            public function array(): array { return $this->data; }
            
            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet) {
                // កំណត់ Range ឱ្យចំពី A1 ដល់ N1 តែម្តង
                $sheet->getStyle('A1:N1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
                $sheet->getStyle('A1:N1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF4F46E5');
            
            }
        }, 'Employee_Import_Template.xlsx');
    }

    /**
     * Export បញ្ជីបុគ្គលិកជា Excel
     */
    public function exportEmployee() {
        return Excel::download(new EmployeeExport, 'Employees_Report_' . now()->format('d-m-Y') . '.xlsx');
    }

    public function resignEmployee($encryptedId)
    {
        try {
            // បំប្លែងលេខកូដសម្ងាត់មកជា ID វិញ
            $id = decrypt($encryptedId);
            
            // ស្វែងរកបុគ្គលិកនោះក្នុង Database
            $employee = \App\Models\EmployeeModel::findOrFail($id);
            
            // ប្តូរស្ថានភាពទៅជា 0 (Inactive)
            $employee->status = 0;
            $employee->save();

            // បញ្ជូនត្រឡប់ទៅវិញដោយភ្ជាប់ជាមួយសារជោគជ័យ
            // ប្រសិនបើបងចង់ប្រើ SweetAlert ក្នុង Blade បងអាចទាញយក Session 'success' នេះទៅបង្ហាញបាន
            return redirect()->back()->with('success', 'Personnel node has been marked as Inactive (Resigned).');
            
        } catch (\Exception $e) {
            // ក្នុងករណី Error លេខ ID ខុស
            return redirect()->back()->with('error', 'Failed to process the request. Invalid ID.');
        }
    }


    public function importEmployee(Request $request) {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);
        
        try {
            Excel::import(new EmployeeImport, $request->file('file'));
            return redirect()->route('employees.index')->with('success', 'Database synchronized successfully.');
            
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // ចាប់ Error បើសិនជា Admin វាយខុសទម្រង់ក្នុង Excel
            return redirect()->route('employees.index')->with('error', 'Validation Error in Excel file. Please check your data format.');
        } catch (Exception $e) {
            return redirect()->route('employees.index')->with('error', 'Sync Failed: ' . $e->getMessage());
        }
    }
}