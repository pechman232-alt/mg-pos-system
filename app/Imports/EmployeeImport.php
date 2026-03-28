<?php

namespace App\Imports;

use App\Models\EmployeeModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

class EmployeeImport implements ToModel, WithStartRow
{
    public function startRow(): int { return 2; }

    public function model(array $row)
    {
        if (empty($row[2])) return null; // បើអត់មាន First Name គឺ Skip

        return new EmployeeModel([
            'first_name_kh'   => $row[0],
            'last_name_kh'    => $row[1],
            'first_name'      => $row[2],
            'last_name'       => $row[3],
            'full_name'       => $row[2] . ' ' . $row[3],
            'gender'          => ($row[5] == 'M') ? 'Male' : 'Female',
            'dob'             => $this->transformDate($row[7]),
            'user_id'         => $row[8],
            
            // Logic បំប្លែងឈ្មោះក្រុមហ៊ុនទៅជា ID (បងអាចកែលេខ ID តាម Database បង)
            'company_id'      => $this->mapCompany($row[10]), 
            'branch_id'       => 1, // ដាក់ Default លេខ 1 សិន
            'department_id'   => 1, 
            'position_id'     => 1, 

            'contract'        => $this->transformDate($row[13]),
            'official_userid' => $row[14],
            'official_pass'   => $row[15],
            'phone_number'    => $row[35],
            'status'          => ($row[36] == 1) ? 1 : 0,
        ]);
    }

    private function mapCompany($name) {
        $name = strtolower($name);
        if (str_contains($name, 'tf')) return 1;
        if (str_contains($name, 'vive')) return 2;
        if (str_contains($name, 'taifu')) return 3;
        return 1;
    }

    private function transformDate($value) {
        if (empty($value) || $value == '-') return null;
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) { return null; }
    }
}