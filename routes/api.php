<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceBookingController;
use App\Http\Controllers\SaleReportController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\BranchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Services\FirebaseService;


Route::get('/locations', [BranchController::class, 'index']);
Route::get('/slots/check', [ServiceBookingController::class, 'checkSlots'])->middleware('throttle:60,1');

Route::post('/services/public-store', [ServiceBookingController::class, 'publicStore'])->middleware('throttle:10,1');






Route::post('/auth/login-by-qr', [AuthController::class, 'loginByQr']);


Route::post('/auth/send-otp', [AuthController::class, 'sendOtp'])->middleware('throttle:3,1');
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);


Route::post('/services/history-by-vin', [ServiceBookingController::class, 'getHistoryByVin']);


Route::post('/verify-vehicle', [SaleReportController::class, 'verifyVehicleForRegistration']);



// ==========================================
// 🔒 Protected Routes (ត្រូវការ Login)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {

    // 🌟 API សម្រាប់ទទួលការវាយតម្លៃអតិថិជន (ត្រូវដាក់ក្នុងនេះព្រោះត្រូវការ Token)
    Route::post('/customer/booking/update', [ServiceBookingController::class, 'updateRemark']);

    // 🌟 Group សេវាកម្ម (Service Routes)
    Route::prefix('services')->group(function () {
        Route::get('/history', [ServiceBookingController::class, 'getHistory']);
        
        // 🌟 កែត្រង់នេះ៖ ដក /services ចេញ
        Route::post('/update-status', [ServiceBookingController::class, 'updateStatus']);
    });

    // ១. Update FCM Token
    Route::post('/update-fcm-token', function (Request $request) {
        $user = $request->user();
        if ($user) {
            $user->update(['fcm_token' => $request->token]);
            return response()->json(['success' => true, 'message' => 'Token Updated']);
        }
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    });

    // ២. ពត៌មានឡាន
    Route::get('/customer/vehicle-info', [AuthController::class, 'getVehicleDetails']);
    
    // ៣. ផ្នែករបាយការណ៍លក់ (Sale Report)
    Route::middleware('role:1,2,3,6,10,11,12,14,15,16')->group(function () {
        Route::get('/sale-reports/dashboard', [SaleReportController::class, 'apiDashboard']);
        Route::get('/sale-reports/branch-details', [SaleReportController::class, 'getBranchDetails']);
    });

    // ៤. ផ្នែក Lead Management 
    Route::prefix('leads')->middleware('role:1,2,3,4,5,6,7,8,9,10,11,12,14,15,16')->group(function () {
        Route::get('/dashboard', [LeadController::class, 'dashboardStats']);
        Route::get('/mobile-list', [LeadController::class, 'apiMobileIndex']); 
        Route::post('/mobile-store', [LeadController::class, 'apiMobileStore']);
        Route::get('/mobile-show/{id}', [LeadController::class, 'apiMobileShow']);
        Route::post('/update-status/{id}', [LeadController::class, 'apiMobileUpdateStatus']);
        Route::post('/mobile-update/{id}', [LeadController::class, 'apiMobileUpdate']);
        Route::get('/notifications', [LeadController::class, 'apiMobileNotifications']);
        Route::get('/check-phone/{phone}', [LeadController::class, 'apiCheckDuplicatePhone']);
        Route::get('/branch-summary', [LeadController::class, 'apiDirectorBranchSummary']);
        Route::post('/transfer', [LeadController::class, 'transfer']);
        Route::post('/reschedule', [LeadController::class, 'reschedule']);
        Route::post('/manager-comment', [LeadController::class, 'updateManagerComment']);
        Route::get('/transfer-options', [LeadController::class, 'getTransferOptions']);
        Route::get('/staff-by-branch', [LeadController::class, 'getStaffByBranch']);
        
        Route::post('/{id}/call-activity', [LeadController::class, 'storeCallActivity']);
        Route::get('/{id}/call-history', [LeadController::class, 'getCallHistory']);
    });

});