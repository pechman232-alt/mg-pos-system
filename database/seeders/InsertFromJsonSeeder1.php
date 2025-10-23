<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use App\Models\SaleReportModel;
use App\Models\MemberCard;
use App\Models\BranchModel;
use App\Models\LogisticsModel;
use App\Models\WarrantyModel;
use App\Models\EmployeeModel;
use App\Models\ProvinceModel;
use App\Models\CarImage;
use App\Models\CarModelModel;
use App\Models\ColorModel;

class InsertFromJsonSeeder1 extends Seeder
{
    public function run()
    {
        $this->command->info("Start import from JSON...");

        $dataFromFile = database_path('seeders/json-data/sale_report.json');

        if (! File::exists($dataFromFile)) {
            $this->command->error("File not found: {$dataFromFile}");
            return;
        }

        $json = File::get($dataFromFile);
        $rows = json_decode($json, true);

        if (! is_array($rows)) {
            $this->command->error('Invalid JSON format. Expected an array.');
            return;
        }

        DB::connection()->disableQueryLog();
        $batchSize = 200;

        foreach (array_chunk($rows, $batchSize) as $chunkIndex => $chunk) {
            foreach ($chunk as $index => $item) {
                if (! is_array($item) || empty($item)) {
                    continue;
                }

                // Wrap per-row related inserts in a transaction
                DB::transaction(function () use ($item) {
                    $now = Carbon::now();

                    // --- Branch (dedupe by branch name)
                    $branchName = $this->value($item, 'branch_id');
                    $branch = null;
                    if ($branchName) {
                        $branch = BranchModel::firstOrCreate(
                            ['branch_name' => $branchName],
                            ['created_at' => $now, 'updated_at' => $now]
                        );
                    }

                    // --- Member card (normalize values)
                    $rawCard = $this->value($item, 'member_card');
                    $memberCardValue = $this->normalizeMemberCard($rawCard);
                    $memberCard = null;
                    if ($memberCardValue !== null) {
                        $memberCard = MemberCard::firstOrCreate(
                            [
                                'member_card' => $memberCardValue
                            ],
                            [
                                'is_sale' => 1, 
                                'created_at' => $now, 
                                'updated_at' => $now
                            ]
                        );
                    }

                    $carModel = CarModelModel::firstOrCreate(
                        [
                            'model_name' => $this->value($item, 'vehicle_Model')
                        ],
                        [
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    $color = ColorModel::firstOrCreate(
                        [
                            'color_name' => $this->value($item, 'color')
                        ],
                        [
                            'code' => null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]
                    );


                    $carImage = CarImage::firstOrCreate(
                        [
                            'model_id' => $carModel->id,
                            'color_id'  => $color->id,
                        ],
                        [
                            'image_name' => null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]
                    );

                    // --- Logistics (dedupe by vin_number if present)
                    $vin = $this->value($item, 'vin_number');
                    $logisticsData = [
                        'model_name_id'  => $carModel->id,
                        'exterior_color' => $color->color_name,
                        'interior_color' => null,
                        'model_year'     => $this->value($item, 'model_year') ?: null,
                        'vin_number'     => $vin ?: null,
                        'engine_number'  => $this->value($item, 'engine_number') ?: null,
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ];
                    if ($vin) {
                        $logistics = LogisticsModel::firstOrCreate(['vin_number' => $vin], $logisticsData);
                    } else {
                        // if there's no VIN, create a record (or skip—adjust to your needs)
                        $logistics = LogisticsModel::create($logisticsData);
                    }

                    // --- Warranty (if any)
                    $warrantyName = $this->value($item, 'warranty_id') ?: $this->value($item, 'warranty_letter');
                    $warranty = null;
                    if ($warrantyName) {
                        $warranty = WarrantyModel::firstOrCreate(
                            ['name' => $warrantyName],
                            ['created_at' => $now, 'updated_at' => $now]
                        );
                    }

                    // --- Employee (parse name and gender)
                    $employeeFull = $this->value($item, 'employee_id');
                    $empInfo = $this->parseUserName($employeeFull);
                    $employee = EmployeeModel::firstOrCreate(
                        [
                            'first_name' => $empInfo['first_name'] ?? null,
                            'last_name'  => $empInfo['last_name'] ?? null,
                        ],
                        [
                            'full_name' => $employeeFull,
                            'gender'    => $empInfo['gender'],
                            'user_id'   => 1,
                            'branch_id' => $branch->id ?? null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]
                    );

                    // --- Province (aprovince_id)
                    $provinceName = $this->value($item, 'aprovince_id') ?? $this->value($item, 'province') ?? null;
                    $province = null;
                    if ($provinceName) {
                        $province = ProvinceModel::firstOrCreate(
                            ['name' => $provinceName],
                            ['created_at' => $now, 'updated_at' => $now]
                        );
                    }

                    // --- Parse dates to Y-m-d (invoice_date, delivery_date, contract_date, pro_offer_oil_date)
                    $invoiceDate = $this->parseDate($this->value($item, 'invoice_date'));
                    $deliveryDate = $this->parseDate($this->value($item, 'delivery_date'));
                    $contractDate = $this->parseDate($this->value($item, 'contract_date'));
                    $oilDate = $this->parseDate($this->value($item, 'pro_offer_oil_date'));

                    // --- Build sale report payload
                    $saleReportPayload = [
                        'image' => $carImage->id,
                        'branch_id' => $branch->id ?? null,
                        'member_card_id' => $memberCard->id ?? null,
                        'brand_name' => $this->value($item, 'brand_name') ?: null,
                        'customer_name' => $this->value($item, 'customer_name') ?: null,
                        'phone_number' => $this->value($item, 'phone_number') ?: null,
                        'gender' => $this->value($item, 'gender') ?: null,
                        'dob' => $this->parseDate($this->value($item, 'dob')),
                        // 'age' => $this->value($item, 'age') ?: null,
                        'source_id' => $this->value($item, 'source_id') ?: null,
                        'logistics_id' => $logistics->id ?? null,
                        'plate_number' => $this->value($item, 'plate_number') ?: null,
                        'invoice_no' => $this->value($item, 'invoice_no') ?: null,
                        'invoice_date' => $invoiceDate,
                        'delivery_date' => $deliveryDate,
                        'follow_up' => $this->value($item, 'follow_up') ?: null,
                        'warranty_id' => $warranty->id ?? null,
                        'employee_id' => $employee->id ?? null,
                        'province_id' => $province->id ?? null,
                        'contract_no' => $this->value($item, 'contract_no') ?: null,
                        'contract_date' => $contractDate,
                        'vpc_vdr' => $this->value($item, 'vpc_vdr') ?: null,
                        'offer_license_plate' => $this->value($item, 'offer_license_plate') ?: null,
                        'offer_uv_film' => $this->value($item, 'offer_uv_film') ?: null,
                        'offer_floor_mat' => $this->value($item, 'offer_floor_mat') ?: null,
                        'side_step' => $this->value($item, 'side_step') ?: null,
                        'auto_tailgate' => $this->value($item, 'auto_tailgate') ?: null,
                        'rear_spoiler' => $this->value($item, 'rear_spoiler') ?: null,
                        'certificate_engine_oil' => $this->value($item, 'certificate_engine_oil') ?: null,
                        'pro_offer_oil' => $this->value($item, 'pro_offer_oil') ?: null,
                        'pro_offer_oil_date' => $oilDate,
                        'vihicle_battery' => $this->value($item, 'vihicle_battery') ?: null,
                        'remark_sa' => $this->value($item, 'remark_sa') ?: null,
                        'price' => $this->value($item, 'price') ?: null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    // create sale report
                    SaleReportModel::create($saleReportPayload);
                }, 5);
            }

            $this->command->info("Processed chunk #{$chunkIndex} (".count($chunk)." rows)");
        }

        $this->command->info('Import finished.');
    }

    /**
     * Safe access to array keys (case-insensitive)
     */
    protected function value(array $item, string $key)
    {
        if (array_key_exists($key, $item)) return $item[$key];

        $lower = strtolower($key);
        foreach ($item as $k => $v) {
            if (strtolower($k) === $lower) return $v;
        }

        return null;
    }

    protected function normalizeMemberCard($raw)
    {
        if ($raw === null) return null;

        $raw = trim((string)$raw);

        if ($raw === '' || $raw === '-' || strcasecmp($raw, 'N/A') === 0) {
            return null;
        }

        return $raw;
    }

    /**
     * Parse human-friendly dates from excel export ( dd/mm/YYYY, d/m/yy, dd/m/yy, etc )
     * Returns Y-m-d string or null.
     */
    protected function parseDate($value)
    {
        if (empty($value)) return null;

        $value = trim((string)$value);

        // if already ISO-ish
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
            return substr($value, 0, 10);
        }

        // common formats to try
        $formats = [
            'd/m/Y', // 26/01/2021
            'd/m/y', // 26/1/21
            'j/n/Y', // 26/1/2021
            'j/n/y', // 26/1/21
            'd-m-Y',
            'Y-m-d',
            'm/d/Y', // in case mm/dd/yyyy
            'm/d/y',
        ];

        foreach ($formats as $fmt) {
            try {
                $dt = Carbon::createFromFormat($fmt, $value);
                if ($dt !== false) {
                    return $dt->format('Y-m-d');
                }
            } catch (\Exception $e) {
                // continue trying
            }
        }

        // try generic parse (may fail for ambiguous strings)
        try {
            $dt = new Carbon($value);
            return $dt->format('Y-m-d');
        } catch (\Exception $e) {
            // last resort: return null
            return null;
        }
    }

    protected function parseUserName(?string $string)
    {
        $gender = 'Unknown';
        $title = '';

        if (!$string) {
            return ['gender' => $gender, 'first_name' => null, 'last_name' => null];
        }

        $trimmed = trim($string);

        // normalize case for prefixes
        $upper = strtoupper($trimmed);
        if (str_starts_with($upper, 'MR') || str_starts_with($upper, 'MR.')) {
            $gender = 'Male';
            // remove MR/ MR. prefix
            $namePart = preg_replace('/^MR\.?\s*/i', '', $trimmed);
        } elseif (str_starts_with($upper, 'MRS') || str_starts_with($upper, 'MRS.')) {
            $gender = 'Female';
            $namePart = preg_replace('/^MRS\.?\s*/i', '', $trimmed);
        } elseif (str_starts_with($upper, 'MS') || str_starts_with($upper, 'MS.')) {
            $gender = 'Female';
            $namePart = preg_replace('/^MS\.?\s*/i', '', $trimmed);
        } elseif (str_starts_with($upper, 'MISS') || str_starts_with($upper, 'MISS.')) {
            $gender = 'Female';
            $namePart = preg_replace('/^MISS\.?\s*/i', '', $trimmed);
        } else {
            $namePart = $trimmed;
        }

        $parts = preg_split('/\s+/', $namePart, -1, PREG_SPLIT_NO_EMPTY);

        if (empty($parts)) {
            return ['gender' => $gender, 'first_name' => null, 'last_name' => null];
        }

        // assume first token is given name, remaining as last name (adjust if your app uses different convention)
        $first = array_shift($parts);
        $last = count($parts) ? implode(' ', $parts) : null;

        return [
            'gender' => $gender,
            'first_name' => $first,
            'last_name' => $last,
        ];
    }
}


// insert age base on dob
