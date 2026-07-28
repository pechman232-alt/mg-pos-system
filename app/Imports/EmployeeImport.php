<?php

namespace App\Imports;

use App\Models\EmployeeModel;
use App\Models\CompanyModel;
use App\Models\BranchModel;
use App\Models\DepartmentModel;
use App\Models\PositionModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

class EmployeeImport implements ToModel, WithStartRow
{
    public function startRow(): int { return 2; }

    public function model(array $row)
    {
        if (empty($row[2])) return null; 

        return EmployeeModel::updateOrCreate(
            ['user_id' => $row[8]], 
            [
                'last_name_kh'   => $row[0],
                'first_name_kh'  => $row[1],
                'first_name'     => $row[2],
                'last_name'      => $row[3],
                'full_name'      => $row[4] ?? ($row[2] . ' ' . $row[3]),
                'gender'         => (strtoupper($row[5]) == 'M') ? 'Male' : 'Female',
                'dob'            => $this->transformDate($row[7]),
                
                // 🌟 មុខងារ Auto Create ប្រសិនបើរករកឈ្មោះមិនឃើញ
                'company_id'     => $this->getOrCreateCompany($row[9]), 
                'branch_id'      => $this->getOrCreateBranch($row[10]),
                'department_id'  => $this->getOrCreateDepartment($row[11]),
                'position_id'    => $this->getOrCreatePosition($row[6]),

                'phone_number'   => $row[12],
                'status'         => (str_contains(strtolower($row[13] ?? ''), 'active') || $row[13] == 1) ? 1 : 0,
            ]
        );
    }

    /**
     * ប្រសិនបើគ្មានឈ្មោះក្រុមហ៊ុននេះទេ វានឹងបង្កើតថ្មី
     */
    private function getOrCreateCompany($name) {
        if (empty($name)) return 3; // Default ទៅ TF Motors បើទទេ
        $company = CompanyModel::firstOrCreate(
            ['company_name' => trim($name)]
        );
        return $company->id;
    }

    /**
     * ប្រសិនបើគ្មានឈ្មោះសាខានេះទេ វានឹងបង្កើតថ្មី
     */
    private function getOrCreateBranch($name) {
        if (empty($name)) return 4; // Default ទៅ HQ បើទទេ
        $branch = BranchModel::firstOrCreate(
            ['branch_name' => trim($name)]
        );
        return $branch->id;
    }

    /**
     * ប្រសិនបើគ្មានឈ្មោះផ្នែកនេះទេ វានឹងបង្កើតថ្មី
     */
    private function getOrCreateDepartment($name) {
        if (empty($name)) return 1; 
        $dept = DepartmentModel::firstOrCreate(
            ['name' => trim($name)],
            ['status' => 1]
        );
        return $dept->id;
    }

    /**
     * ប្រសិនបើគ្មានឈ្មោះតួនាទីនេះទេ វានឹងបង្កើតថ្មី
     */
    private function getOrCreatePosition($name) {
        if (empty($name)) return 1;
        $pos = PositionModel::firstOrCreate(
            ['name' => trim($name)],
            ['status' => 1]
        );
        return $pos->id;
    }
    private function transformDate($value) {
        if (empty($value) || $value == '-') return null;
        try {
            // ប្រសិនបើ Excel បញ្ជូនមកជាលេខ (Serial Number)
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }
            // ប្រសិនបើជាអក្សរ ត្រូវ Parse ឱ្យប្រុងប្រយ័ត្ន
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) { 
            return null; 
        }
    }
}