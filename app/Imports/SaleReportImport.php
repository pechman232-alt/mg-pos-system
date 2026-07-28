<?php

namespace App\Imports;

use App\Models\SaleReportModel;
use App\Models\BranchModel;
use App\Models\EmployeeModel;
use App\Models\ProvinceModel;
use App\Models\LogisticsModel;
use App\Models\WarrantyModel;
use App\Models\MemberCard;
use Carbon\Carbon;
use Maatwebsite\Excel\Row; 
use Maatwebsite\Excel\Concerns\OnEachRow; 
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SaleReportImport implements OnEachRow, WithHeadingRow, WithChunkReading
{
    public function onRow(Row $excelRow)
    {
        $row = $excelRow->toArray();
        $customerName = $row['customer_s_name'] ?? $row['customers_name'] ?? $row['customer_name'] ?? null;

        if (empty($row['brand_name']) && empty($customerName)) {
            return; 
        }

        $logisticsId = $this->getLogisticsId($row);
        $memberCardId = $this->getMemberCardId($row['member_card'] ?? null);

        $branchId = $this->getBranchId($row['branch'] ?? null) ?? 1;
        $employeeId = $this->getEmployeeId($row['sales_person'] ?? $row['employee_id'] ?? null) ?? 1;
        $provinceId = $this->getProvinceId($row['address'] ?? $row['province'] ?? null) ?? 1;
        $warrantyId = $this->getWarrantyId($row['warranty'] ?? null);
        $warrantyLeapId = $this->getWarrantyId($row['battery_warranty_for_leapmotor'] ?? $row['warranty_leap_motor'] ?? null);

        SaleReportModel::updateOrCreate(
            ['invoice_no' => $this->safeStr($row['invoice_no'] ?? null, 15, 'N/A')], 
            [
                'image'                  => 'default.png',
                'branch_id'              => $branchId,
                'employee_id'            => $employeeId,
                'province_id'            => $provinceId,
                'logistics_id'           => $logisticsId, // 🌟 ឥឡូវនេះវាទទួលបានជាលេខ ID ត្រឹមត្រូវ
                'member_card_id'         => $memberCardId,
                'warranty_id'            => $warrantyId,
                'warranty_leap_motor'    => $warrantyLeapId,
                
                'brand_name'             => $this->safeStr($row['brand_name'] ?? null, 10, 'MG'),
                'customer_name'          => $this->safeStr($customerName, 100, 'Unknown'),
                'phone_number'           => $this->safeStr($row['phone_number'] ?? null, 45, 'N/A'),
                'gender'                 => $this->safeStr($row['gender'] ?? null, 7, 'N/A'),
                'source_id'              => $this->safeStr($row['source_customer'] ?? $row['source_id'] ?? null, 50, 'N/A'),
                'plate_number'           => $this->safeStr($row['plate_number'] ?? null, 30, 'N/A'),
                'follow_up'              => $this->safeStr($row['follow_up'] ?? null, 10, 'N/A'),
                'feedback_command'       => $this->safeStr($row['follow_up_commends'] ?? $row['feedback_command'] ?? null, 255, 'N/A'),
                'contract_no'            => $this->safeStr($row['contract_no'] ?? null, 20, 'N/A'),
                
                'vpc_vdr'                => $this->safeStr($row['vpc_vdr'] ?? $row['vpc_vdr_'] ?? null, 10, 'N/A'),
                'offer_license_plate'    => $this->safeStr($row['offer_license_plate'] ?? null, 30, 'No'),
                'offer_uv_film'          => $this->safeStr($row['offer_uv_film'] ?? null, 30, 'No'),
                'offer_floor_mat'        => $this->safeStr($row['offer_floor_mat'] ?? null, 30, 'No'),
                'side_step'              => $this->safeStr($row['side_step'] ?? null, 30, 'No'),
                'auto_tailgate'          => $this->safeStr($row['auto_tailgate'] ?? null, 30, 'No'),
                'rear_spoiler'           => $this->safeStr($row['rear_spoiler'] ?? null, 30, 'No'),
                'certificate_engine_oil' => $this->safeStr($row['certificate_engine_oil'] ?? null, 30, 'No'),
                'pro_offer_oil'          => $this->safeStr($row['pro_offer_oil'] ?? null, 255, 'N/A'),
                
                'dob'                    => $this->transformDate($row['date_of_birth'] ?? $row['dob'] ?? null),
                'invoice_date'           => $this->transformDate($row['invoice_date'] ?? null) ?? now()->format('Y-m-d'),
                'delivery_date'          => $this->transformDate($row['delivery_date'] ?? null) ?? now()->format('Y-m-d'),
                'contract_date'          => $this->transformDate($row['contract_date'] ?? null),
                'pro_offer_oil_date'     => $this->transformDate($row['prooffer_oil_date'] ?? $row['pro_offer_oil_date'] ?? null),
                'vihicle_battery'        => $this->transformDate($row['vehicle_battery_inspection_record'] ?? $row['vihicle_battery'] ?? null),
                
                'price'                  => $this->cleanPrice($row['price'] ?? null),
                'remark_sa'              => $this->safeStr($row['remark_sa'] ?? $row['remark_admin'] ?? null, 255, 'N/A'),
                
                'attach_file'            => null,
                'status'                 => 1,
                'created_by'             => auth('users')->user()->full_name ?? 'System Admin',
            ]
        );
    }

    public function chunkSize(): int
    {
        return 200; 
    }

    private function getMemberCardId($cardName) {
        if (!$cardName || strtolower(trim($cardName)) === 'n/a' || trim($cardName) === '-') return null;
        $cleanCard = trim((string)$cardName);
        $card = MemberCard::where('member_card', $cleanCard)->first();
        if ($card) { $card->update(['is_sale' => 0]); return $card->id; } 
        else { $newCard = MemberCard::create(['member_card' => $cleanCard, 'is_sale' => 0]); return $newCard->id; }
    }

    // ==============================================================
    // 🌟 ការ Update ថ្មី៖ បំប្លែងឈ្មោះឡាន ទៅជាលេខ ID ឱ្យបានត្រឹមត្រូវ
    // ==============================================================
    private function getLogisticsId($row) {
        $vin = $row['vin_number'] ?? $row['vin'] ?? null;
        if (!$vin || strtolower(trim($vin)) === 'n/a' || trim($vin) === '-') return null; 
        
        $cleanVin = trim($vin);
        $log = LogisticsModel::where('vin_number', $cleanVin)->first();
        
        if ($log) { 
            $log->update(['is_sale' => 0]); 
            return $log->id; 
        } 
        else {
            $excelModelName = $row['model_name'] ?? $row['brand_model'] ?? $row['model'] ?? null;
            $carModelId = null;

            // ស្វែងរកលេខ ID ក្នុងតារាង car_models ជាមុនសិន
            if ($excelModelName) {
                $carModel = \App\Models\CarModelModel::where('model_name', 'LIKE', '%' . trim($excelModelName) . '%')->first();
                
                if ($carModel) {
                    $carModelId = $carModel->id;
                } else {
                    $newCarModel = \App\Models\CarModelModel::create([
                        'model_name' => trim($excelModelName),
                        'color_id' => null
                    ]);
                    $carModelId = $newCarModel->id;
                }
            }

            // បង្កើត Logistics ថ្មី ដោយប្រើប្រាស់លេខ ID ដែលទើបនឹងរកឃើញ
            $newLogistics = LogisticsModel::create([
                'vin_number' => $cleanVin, 
                'engine_number' => $row['engine_number'] ?? $row['engine'] ?? 'N/A',
                'model_name_id' => $carModelId, // 🌟 Save ជាលេខ ID
                'exterior_color' => $row['color'] ?? 'N/A', 
                'model_year' => date('Y'), 
                'is_sale' => 0 
            ]);
            
            return $newLogistics->id;
        }
    }

    private function getEmployeeId($name) {
        if (!$name || strtolower(trim($name)) === 'n/a' || trim($name) === '?') return null;
        $cleanName = trim(str_ireplace(['Mr.', 'Ms.', 'Mss.', 'Mrs.', 'Mr', 'Ms', 'Mrs'], '', $name));
        $emp = EmployeeModel::where('full_name', 'LIKE', "%{$cleanName}%")->first();
        return $emp ? $emp->id : null;
    }

    private function getBranchId($name) {
        if (!$name || strtolower(trim($name)) === 'n/a' || trim($name) === '?') return null;
        $branch = BranchModel::where('branch_name', 'LIKE', "%{$name}%")->first();
        return $branch ? $branch->id : null;
    }

    private function getProvinceId($name) {
        if (!$name || strtolower(trim($name)) === 'n/a' || trim($name) === '?') return null;
        $prov = ProvinceModel::where('name', 'LIKE', "%{$name}%")->first();
        return $prov ? $prov->id : null;
    }

    private function getWarrantyId($name) {
        if (!$name || strtolower(trim($name)) === 'n/a' || trim($name) === '?') return null;
        $war = WarrantyModel::where('name', 'LIKE', "%{$name}%")->first();
        return $war ? $war->id : null;
    }

    private function safeStr($value, $maxLength, $default = null) {
        $val = trim((string) $value);
        if ($val === '' || strtolower($val) === 'null' || $val === ';' || $val === '-' || strtolower($val) === 'n/a' || $val === '["n/a"]') return $default;
        return mb_substr($val, 0, $maxLength);
    }

    private function cleanPrice($value) {
        if (!$value) return 0;
        $cleanVal = str_replace(['$', ',', ' '], '', (string) $value);
        return is_numeric($cleanVal) ? $cleanVal : 0;
    }
    

   // ==========================================
    // 🌟 THE ULTIMATE DATE SWAPPER (វះកាត់ថ្ងៃខែ Excel ត្រឡប់មកដើមវិញ) 🌟
    // ==========================================
    private function transformDate($value, $format = 'Y-m-d')
    {
        if (empty($value) || trim($value) === '' || trim($value) === '-' || strtolower(trim($value)) === 'n/a') {
            return null;
        }

        $val = trim((string) $value);

        try {
            $year = null; $month = null; $day = null;

            // 1. បើទិន្នន័យជាលេខកូដសម្ងាត់របស់ Excel (ឧ. 46114)
            if (is_numeric($val)) {
                $excelObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val);
                $year  = (int)$excelObj->format('Y');
                $month = (int)$excelObj->format('m'); // កន្លែងនេះ Excel គិតថាជា ខែ តែតាមពិតគឺ ថ្ងៃ ដែលបងវាយ
                $day   = (int)$excelObj->format('d'); // កន្លែងនេះ Excel គិតថាជា ថ្ងៃ តែតាមពិតគឺ ខែ
            } 
            else {
                // 2. បើទិន្នន័យជាអក្សរ
                $val = str_replace(['/', '.', '\\', ' '], '-', $val);
                
                // បើជាអក្សរអង់គ្លេស (ឧ. 12-Jan-2026) ឱ្យវាដើរតាមធម្មតា មិនបាច់វះកាត់ទេ
                if (preg_match('/[a-zA-Z]/', $val)) {
                    return \Carbon\Carbon::parse($val)->format($format);
                }

                // កាត់យកតួលេខមកវះកាត់
                if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $val, $m)) {
                    $year = (int)$m[1]; $month = (int)$m[2]; $day = (int)$m[3];
                } elseif (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $val, $m)) {
                    $year = (int)$m[3]; $month = (int)$m[2]; $day = (int)$m[1];
                } elseif (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{2})$/', $val, $m)) {
                    $year = 2000 + (int)$m[3]; $month = (int)$m[2]; $day = (int)$m[1];
                } else {
                    return \Carbon\Carbon::parse($val)->format($format);
                }
            }

            // 🌟 ធ្វើការបង្វិល (SWAP) ថ្ងៃ និង ខែ ជានិច្ចដើម្បីកែកំហុស Excel! 🌟
            try {
                // បង្ខំយក $day (ខែរបស់ Excel) មកធ្វើជា ខែពិតប្រាកដ (Month)
                // បង្ខំយក $month (ថ្ងៃរបស់ Excel) មកធ្វើជា ថ្ងៃពិតប្រាកដ (Day)
                return \Carbon\Carbon::createFromDate($year, $day, $month)->format($format);
            } catch (\Exception $e) {
                // បើបង្វិលមិនបាន (ឧ. ខែទី ២៥ ព្រោះវាត្រូវស្រាប់ហើយ) យើងឱ្យវាអានតាមដើមវិញ
                return \Carbon\Carbon::createFromDate($year, $month, $day)->format($format);
            }

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Date Error: ' . $val . ' => ' . $e->getMessage());
            return null; 
        }
    }
}