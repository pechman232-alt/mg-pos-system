<?php

namespace Database\Seeders\MasterData;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmployeeModel;
class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $this->command->info(" /************** Creating Employee **************/ ");

            $employeeDataFilePath = database_path('seeders/json-data/employee.json');


            if (File::exists($employeeDataFilePath)) {
                $employeeData = json_decode(File::get($employeeDataFilePath), true) ?: [];
                for($i =0; $i < count($employeeData); $i++){

                    $data = [
                        'code' => $code,
                        'name_en' => $employeeData[$i]['name_en'],
                        'name_kh' => $employeeData[$i]['name_kh'],
                        'service_type_id' => $serviceTypeId,
                        'has_trainer' => '0',
                    ];
                    EmployeeModel::create($employeeData);
                }

            }


            $this->command->info(" /************** Employee Success Created **************/ ");
        } catch (\Exception $e) {
            $this->command->error("Error: " . $e->getMessage());
        }
    }
}
