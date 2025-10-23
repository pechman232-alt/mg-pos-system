<?php

namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\SaleReportModel;
use App\Models\MemberCard;
use App\Models\BranchModel;
use App\Models\LogisticsModel;
use App\Models\WarrantyModel;
use App\Models\EmployeeModel;
use App\Models\ProvinceModel;

class InsertFromJsonSeeder extends Seeder
{
    public function run()
    {
        try{
            $this->command->info("********************** creating data sale report*****************");
        $dataFromFile = database_path('json-data/sale_report.json');

            if(File::exits($dataFromFile)){
                $saleReportData = json_decode(File::get($dataFromFile), true) ? :[];
                $branchData = [
                    "branch_name" => $saleReportData[$i]->branch_name,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $createdBranch = BranchModel::create($branchData);

                if($saleReportData[$i]['member_card'].length < 3 && not_exsit($saleReportData[$i]['member_card'], "-")){
                    $memberCardData = [
                        "member_card" => $saleReportData[$i]->card,
                        "is_sale" => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                $createdMemberCard = MemberCard::create($memberCardData);
                }
               
                $logisticsData = [
                    'model_name' => $saleReportData[$i]['vechicle'],
                    'exterior_color' => null,
                    'interior_color' => null,
                    'model_year' => $saleReportData[$i]['model_year'],
                    'vin_number' => $saleReportData[$i]['vin_number'],
                    'engine_number' => $saleReportData[$i]['engine_number'],
                    'is_sale' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $createdlogistics = LogisticsModel::create($logisticsData);


                $warrantyData = [
                    "name" => $saleReportData[$i]->warranty,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $createdwarranty = WarrantyModel::create($warrantyData);
                $empInfo = parseUserName($saleReportData[$i]['employee_id']);
                $createdEmployee = EmployeeModel::firstOrCreate(
                    [
                        'first_name' => $empInfo['first_name'],
                        'last_name'  => $empInfo['last_name'],
                    ],
                    [
                        'full_name' => $saleReportData[$i]['employee_id'],
                        'gender' => $empInfo['gender'],
                        'user_id' => 1,
                        'branch_id' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );

                

                $provinceData = [
                    "name" => $saleReportData[$i]['aprovince_id'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $createdProvince = ProvinceModel::create($provinceData);

                $saleReportData = [
                    'image' => null,
                    'branch_id' => $createdBranch->id,
                    'member_card' => $createdMemberCard->member_card,
                    'brand_name' => $saleReportData[$i]->branch_name,
                    'customer_name' => $saleReportData[$i]->customer_name,
                    'phone_number' => $saleReportData[$i]['phone_number'],
                    'gender' => $saleReportData[$i]['gender'],
                    'dob' => $saleReportData[$i]['dob'],
                    'age' => $saleReportData[$i]['age'],
                    'source_id' => $saleReportData[$i]['source_id'],
                    'logistics_id' => $createdlogistics->id,
                    'plate_number' => $saleReportData[$i]['plate_number'],
                    'invoice_no' => $saleReportData[$i]['invoice_no'],
                    'invoice_date' => $saleReportData[$i]['invoice_date'],
                    'delivery_date' => $saleReportData[$i]['delivery_date'],
                    'follow_up' => $saleReportData[$i]['follow_up'],
                    'warranty_id' => $createdwarranty->id,
                    'employee_id' => $createdEmployee->id,
                    'province_id' => $createdProvince->id,
                    'contract_no' => $saleReportData[$i]['contract_no'],
                    'contract_date' => $saleReportData[$i]['contract_date'],
                    'vpc_vdr' => $saleReportData[$i]['vpc_vdr'],
                    'offer_license_plate' => $saleReportData[$i]['offer_license_plate'],
                    'offer_uv_film' => $saleReportData[$i]['offer_uv_film'],
                    'offer_floor_mat' => $saleReportData[$i]['offer_floor_mat'],
                    'side_step' => $saleReportData[$i]['side_step'],
                    'auto_tailgate' => $saleReportData[$i]['auto_tailgate'],
                    'rear_spoiler' => $saleReportData[$i]['rear_spoiler'],
                    'certificate_engine_oil' => $saleReportData[$i]['certificate_engine_oil'],
                    'pro_offer_oil' => $saleReportData[$i]['pro_offer_oil'],
                    'pro_offer_oil_date' => $saleReportData[$i]['pro_offer_oil_date'],
                    'vihicle_battery' => $saleReportData[$i]['vihicle_battery'],
                    'price' => $saleReportData[$i]['price']
                ];
                SaleReportModel::create();
            }
        }catch (\Exception $e){
            this->command->error("Error: ". $e);
        }
        
    }

    function parseUserName($string)
    {
        $gender = 'Unknown';
        $title = '';

        if (str_starts_with($string, 'Mr.')) {
            $gender = 'Male';
            $title = 'Mr.';
        } elseif (str_starts_with($string, 'Ms.') || str_starts_with($string, 'Mrs.')) {
            $gender = 'Female';
            $title = str_starts_with($string, 'Mrs.') ? 'Mrs.' : 'Ms.';
        }

        $namePart = trim(str_replace($title, '', $string));
        $parts = explode(' ', $namePart);

        $first_name = array_pop($parts);
        $last_name = implode(' ', $parts);

        return [
            'gender' => $gender,
            'first_name' => $first_name,
            'last_name' => $last_name,
        ];
    }

}
