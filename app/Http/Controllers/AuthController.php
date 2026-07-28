<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http; // 🌟 ថែម Http សម្រាប់បាញ់ API ទៅកាន់ SMS Gateway
use App\Models\User;
use App\Models\SaleReportModel; 
use Carbon\Carbon;

class AuthController extends Controller
{
    // =========================================================================
    // មុខងារទី ១៖ ចូលប្រព័ន្ធតាមរយៈលេខ VIN (គាំទ្រទាំង ៦ខ្ទង់ដៃ និងស្កេន QR ១៧ខ្ទង់)
    // =========================================================================
    public function loginByQr(Request $request)
    {
        $request->validate([
            'vin_number' => 'required|string'
        ]);

        $vinNumber = trim($request->vin_number);

        // ការពារការដកឃ្លា (Space) មុខក្រោយនៅក្នុង Database
        $sale = SaleReportModel::whereHas('logistic', function ($q) use ($vinNumber) {
            $q->where('vin_number', 'LIKE', '%' . $vinNumber . '%');
        })->first();

        if (!$sale) {
            return response()->json([
                'success' => false,
                'message' => 'រកមិនឃើញទិន្នន័យយានយន្តក្នុងប្រព័ន្ធទេ។ សូមពិនិត្យលេខតួឡាន ឬ QR Code ម្តងទៀត!'
            ], 200); 
        }

        // ការពារ Error ពេល phone_number ជា null
        $phone = trim($sale->phone_number ?? '');
        $cleanPhone = str_replace(' ', '', $phone); 

        if (empty($cleanPhone)) {
            return response()->json([
                'success' => false,
                'message' => 'រកឃើញឡានរបស់អ្នក ប៉ុន្តែមិនមានលេខទូរស័ព្ទក្នុងប្រព័ន្ធ។ សូមទាក់ទងមកយានដ្ឋាន!'
            ], 200);
        }

        // ស្វែងរកគណនីអតិថិជន (User Model) ដោយប្រើ 'username'
        $userModel = User::where('username', $cleanPhone)->first();

        // បើមិនទាន់មានគណនី យើងបង្កើតជូនគាត់ភ្លាម (Auto-Register)
        if (!$userModel) {
            $timestamp = time(); 
            
            $userModel = new User();
            $userModel->user_id = 'APP-' . $timestamp;
            $userModel->username = $cleanPhone;
            $userModel->phone = $cleanPhone; 
            $userModel->full_name = $sale->customer_name ?? 'អតិថិជន MG'; 
            $userModel->email = $cleanPhone . '_' . $timestamp . '@mgcare.com';
            $userModel->gender = $sale->gender ?? 'N/A';
            
            $userModel->branch_id = $sale->branch_id ?? 13; 
            $userModel->department_id = 1; 
            $userModel->position_id = 1;   
            $userModel->role_id = 19;      // Customer Role
            
            $userModel->password = Hash::make('mg' . $cleanPhone);
            $userModel->save();
        }

        // 🔑 បង្កើតលេខកូដសម្គាល់សិទ្ធិ Sanctum Token
        $token = $userModel->createToken('customer_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'ចូលប្រព័ន្ធបានជោគជ័យ!',
            'token' => $token,
            'data' => [
                'user_name' => $userModel->full_name,
                'phone' => $userModel->username,
                'vin_number' => $vinNumber,
                'role_id' => 19,
            ]
        ]);
    }

    // =========================================================
    // មុខងារទី ២៖ ទទួលលេខទូរស័ព្ទ លេខតួ រួចពិនិត្យទិន្នន័យ និងបង្កើត OTP
    // =========================================================
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric',
            'full_name' => 'required|string',
            'branch_id' => 'required|numeric',
            'vin_last_6' => 'required|string|min:6|max:6'
        ]);

        $phone = $request->phone;
        $cleanPhone = ltrim($phone, '0'); 
        $vinLast6 = trim($request->vin_last_6);
        $fullName = $request->full_name;
        $branchId = $request->branch_id;

        $sale = SaleReportModel::where(function($q) use ($phone, $cleanPhone) {
                $q->whereRaw("REPLACE(phone_number, ' ', '') LIKE ?", ['%' . $phone . '%'])
                  ->orWhereRaw("REPLACE(phone_number, ' ', '') LIKE ?", ['%' . $cleanPhone . '%']);
            })
            ->whereHas('logistic', function ($query) use ($vinLast6) {
                $query->where('vin_number', 'LIKE', '%' . $vinLast6 . '%');
            })
            ->first();

        if (!$sale) {
            return response()->json([
                'success' => false,
                'message' => 'រកមិនឃើញទិន្នន័យរថយន្តនេះទេ។ សូមពិនិត្យលេខទូរស័ព្ទ និងលេខតួ ៦ ខ្ទង់ម្ដងទៀត!'
            ], 200);
        }

        $otpCode = rand(1000, 9999);
        $expiresAt = Carbon::now()->addMinutes(5);

        $user = User::where('phone', $phone)->first();

        if ($user) {
            $user->full_name = $fullName;
            $user->save();
        } else {
            $user = User::create([
                'phone' => $phone,
                'full_name' => $fullName,
                'email' => $phone . '_' . time() . '@mgcare.com',
                'password' => Hash::make(uniqid()),
                'user_id' => 'APP-' . time(),
                'username' => $phone,
                'gender' => 'N/A',
                'branch_id' => $branchId,
                'department_id' => 1,
                'position_id' => 1,
                'role_id' => 19 
            ]);
        }

        $user->otp_code = $otpCode;
        $user->otp_expires_at = $expiresAt;
        $user->save();

        // ========================================================
        // 🚀 ផ្នែកបាញ់សារ SMS (SMS Gateway Integration) 
        // ឧទាហរណ៍ខាងក្រោមនេះប្រើសម្រាប់ Plasgate ដែលពេញនិយម
        // ========================================================
        /*
        try {
            $smsResponse = Http::post('https://cloudapi.plasgate.com/rest/send', [
                'private_key' => 'YOUR_PLASGATE_PRIVATE_KEY',
                'sender'      => 'MG Care', // ឈ្មោះដែលលោតលើទូរស័ព្ទ
                'phone'       => $phone,
                'message'     => "Your MG Care verification code is {$otpCode} (OTP)"
            ]);
            
            // អាចឆែកមើល $smsResponse->successful() បើចាំបាច់
        } catch (\Exception $e) {
            // ទោះបី SMS បាញ់អត់ចេញ ក៏កុំឱ្យគាំង App 
            \Log::error('SMS Gateway Error: ' . $e->getMessage());
        }
        */

        return response()->json([
            'success' => true,
            'message' => 'កូដ OTP ត្រូវបានផ្ញើ!',
            'test_otp' => $otpCode // បើកដំណើរការ SMS Gateway ហើយ អាចលុបបន្ទាត់នេះចោលបាន
        ]);
    }

    // =========================================================
    // មុខងារទី ៣៖ ផ្ទៀងផ្ទាត់កូដ OTP របស់អតិថិជន
    // =========================================================
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric',
            'otp'   => 'required|numeric',
            'vin_last_6' => 'required|string|min:6|max:6'
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user || $user->otp_code != $request->otp) {
            return response()->json(['success' => false, 'message' => 'កូដ OTP មិនត្រឹមត្រូវទេ!'], 200);
        }

        if (Carbon::now()->isAfter($user->otp_expires_at)) {
            return response()->json(['success' => false, 'message' => 'កូដ OTP ផុតកំណត់ហើយ!'], 200);
        }

        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        $user->tokens()->delete();
        $token = $user->createToken('mg-care-app')->plainTextToken;

        $vPhone = $request->phone;
        $vCleanPhone = ltrim($vPhone, '0');
        $vVinLast6 = trim($request->vin_last_6);

        $sale = SaleReportModel::with(['logistic'])
            ->where(function($q) use ($vPhone, $vCleanPhone) {
                $q->whereRaw("REPLACE(phone_number, ' ', '') LIKE ?", ['%' . $vPhone . '%'])
                  ->orWhereRaw("REPLACE(phone_number, ' ', '') LIKE ?", ['%' . $vCleanPhone . '%']);
            })
            ->whereHas('logistic', function ($query) use ($vVinLast6) {
                $query->where('vin_number', 'LIKE', '%' . $vVinLast6 . '%');
            })
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'ចូលគណនីជោគជ័យ!',
            'token'   => $token,
            'user'    => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'phone' => $user->phone,
                'branch_id' => $user->branch_id,
                'role_id' => $user->role_id
            ],
            'dashboard_data' => [
                'purchase_history' => [
                    'brand_name'    => $sale->brand_name ?? 'MG Vehicle',
                    'model'         => ($sale && $sale->logistic && $sale->logistic->model_year) ? 'MG Vehicle (' . $sale->logistic->model_year . ')' : 'MG Vehicle',
                    'model_year'    => $sale->logistic->model_year ?? 'N/A', 
                    'vin'           => $sale->logistic->vin_number ?? 'N/A', 
                    'engine'        => $sale->logistic->engine_number ?? 'N/A', 
                    'plate_number'  => $sale->plate_number ?? 'មិនទាន់មាន',
                    'invoice_no'    => $sale->invoice_no ?? 'N/A',
                    'delivery_date' => ($sale && $sale->delivery_date) ? Carbon::parse($sale->delivery_date)->format('d-M-Y') : 'N/A', 
                    'offers' => [
                        'uv_film'   => ($sale && (int)$sale->offer_uv_film === 1) ? 'ទទួលបាន' : 'មិនមាន',
                        'floor_mat' => ($sale && (int)$sale->offer_floor_mat === 1) ? 'ទទួលបាន' : 'មិនមាន',
                        'side_step' => ($sale && (int)$sale->side_step === 1) ? 'ទទួលបាន' : 'មិនមាន',
                        'free_oil'  => ($sale && (int)$sale->pro_offer_oil === 1) ? 'ទទួលបាន' : 'មិនមាន',
                    ]
                ],
                'service_history' => [] 
            ]
        ]);
    }

    // =========================================================
    // មុខងារទី ៤៖ ទាញយកព័ត៌មានលម្អិតរបស់ឡាន ទៅបង្ហាញលើ Mobile App
    // =========================================================
    public function getVehicleDetails(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }

            $phone = $user->phone ?? $user->phone_number ?? '';
            $cleanPhone = $phone ? ltrim($phone, '0') : ''; 

            $vinNumber = $request->input('vin_number');
            $query = SaleReportModel::with(['logistic', 'branch', 'employee', 'province']);

            if (!empty($vinNumber)) {
                $query->whereHas('logistic', function ($q) use ($vinNumber) {
                    $q->where('vin_number', 'LIKE', '%' . $vinNumber . '%');
                });
            } else {
                $query->where(function($q) use ($phone, $cleanPhone) {
                    $q->whereRaw("REPLACE(phone_number, ' ', '') LIKE ?", ['%' . $phone . '%'])
                      ->orWhereRaw("REPLACE(phone_number, ' ', '') LIKE ?", ['%' . $cleanPhone . '%']);
                });
            }

            $sale = $query->orderBy('id', 'desc')->first(); 

            if (!$sale) {
                return response()->json([
                    'success' => false,
                    'message' => 'រកមិនឃើញទិន្នន័យរថយន្តនៅក្នុងប្រព័ន្ធរបាយការណ៍លក់ទេ។'
                ], 200);
            }

            $logistic = $sale->logistic;

            $realModelName = 'N/A';
            if ($logistic && !empty($logistic->model_name_id)) {
                if (is_numeric($logistic->model_name_id)) {
                    $carModelInfo = \Illuminate\Support\Facades\DB::table('car_models')
                        ->where('id', $logistic->model_name_id)
                        ->first();
                    $realModelName = $carModelInfo ? $carModelInfo->model_name : 'MG Vehicle';
                } else {
                    $realModelName = $logistic->model_name_id;
                }
            }

            $warrantyMapping = [
                3 => '2 Years/30,000 KM', 4 => '3 Years/100,000 KM',
                5 => '3 Years/50,000 KM', 6 => '5 Years/100,000 KM',
                7 => '5 Years/150,000 KM', 8 => 'N/A',
            ];
            $warrantyText = (isset($sale->warranty_id) && isset($warrantyMapping[$sale->warranty_id]))
                ? $warrantyMapping[$sale->warranty_id]
                : ($sale->warranty_id ?? 'N/A');

            $checkOffer = function($val) {
                if (is_null($val)) return 'មិនមាន';
                $v = trim(strtolower((string)$val));
                return in_array($v, ['1', 'on', 'yes', 'true']) ? 'ទទួលបាន' : 'មិនមាន';
            };

            return response()->json([
                'success' => true,
                'data' => [
                    'customer_name'  => $sale->customer_name ?? $user->full_name,
                    'phone_number'   => $sale->phone_number ?? $phone,
                    'gender'         => $sale->gender ?? 'N/A',
                    'dob'            => $sale->dob ? Carbon::parse($sale->dob)->format('d-M-Y') : 'N/A',
                    
                    'branch_id'      => $sale->branch->branch_name ?? $sale->branch->name ?? $sale->branch_id ?? 'N/A',
                    'employee_id'    => optional($sale->employee)->full_name ?: optional($sale->employee)->name ?: 'N/A',
                    'province_id'    => $sale->province->name ?? $sale->province_id ?? 'N/A',
                    
                    'brand_name'     => $sale->brand_name ?? 'MG',
                    'model_name'     => $realModelName,
                    'model_year'     => $logistic ? $logistic->model_year : 'N/A',
                    'vin_number'     => $logistic ? $logistic->vin_number : 'N/A',
                    'engine_number'  => $logistic ? $logistic->engine_number : 'N/A',
                    'plate_number'   => $sale->plate_number ?? 'មិនទាន់មានផ្លាកលេខ',
                    
                    'invoice_no'     => $sale->invoice_no ?? 'N/A',
                    'invoice_date'   => $sale->invoice_date ? Carbon::parse($sale->invoice_date)->format('d-M-Y') : 'N/A',
                    'contract_no'    => $sale->contract_no ?? 'N/A',
                    'contract_date'  => $sale->contract_date ? Carbon::parse($sale->contract_date)->format('d-M-Y') : 'N/A',
                    'delivery_date'  => $sale->delivery_date ? Carbon::parse($sale->delivery_date)->format('d-M-Y') : 'N/A',
                    'price'          => $sale->price ?? 'N/A',
                    
                    'warranty_id'     => $warrantyText,
                    'vihicle_battery' => $sale->vihicle_battery ? Carbon::parse($sale->vihicle_battery)->format('d-M-Y') : 'N/A',
                    
                    'offers' => [
                        'license_plate' => $checkOffer($sale->offer_license_plate),
                        'uv_film'       => $checkOffer($sale->offer_uv_film),
                        'floor_mat'     => $checkOffer($sale->offer_floor_mat),
                        'side_step'     => $checkOffer($sale->side_step),
                        'auto_tailgate' => $checkOffer($sale->auto_tailgate),
                        'rear_spoiler'  => $checkOffer($sale->rear_spoiler),
                        'cert_engine_oil'=> $checkOffer($sale->certificate_engine_oil),
                        'free_oil'      => $checkOffer($sale->pro_offer_oil),
                    ],
                    'status'           => $sale->status ?? 'N/A',
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Backend Error: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================
    // មុខងារទី ៥៖ លុកអ៊ីនសម្រាប់បុគ្គលិកក្រុមហ៊ុន (Staff Login)
    // =========================================================
    public function staffLogin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
    
        $user = User::where('username', $request->username)->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'ឈ្មោះ ឬលេខសម្ងាត់មិនត្រឹមត្រូវ'], 401);
        }
    
        $branchName = 'Head Office';
        if ($user->branch_id != null) {
            $branch = DB::table('branches')->where('id', $user->branch_id)->first();
            if ($branch) {
                $branchName = $branch->branch_name ?? $branch->name ?? 'Head Office';
            }
        }
    
        $token = $user->createToken('mg-staff-token')->plainTextToken;
    
        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'role_id' => $user->role_id,
                'branch_id' => $user->branch_id, 
                'branch_name' => $branchName,    
            ]
        ]);
    }

    // =========================================================
    // មុខងារទី ៦៖ រក្សាទុក FCM Token សម្រាប់បញ្ជូន Notification
    // =========================================================
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);
    
        $user = $request->user(); 
    
        if ($user) {
            DB::table('users')->where('id', $user->id)->update([
                'fcm_token' => $request->fcm_token,
                'updated_at' => now(),
            ]);
    
            return response()->json([
                'success' => true, 
                'message' => 'FCM Token ត្រូវបានរក្សាទុកក្នុង Database ជោគជ័យ! 🎉'
            ]);
        }
    
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }
}