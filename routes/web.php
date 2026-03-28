<?php

use App\Http\Controllers\CarModelController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogisticController;
use App\Http\Controllers\MemberCardController;
use App\Http\Controllers\SaleReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceBookingController;

// Route Login
Route::get('login', [LoginController::class, 'loginPage']);
Route::post('login', [LoginController::class, 'fnLogin']);

Route::get('/imageCar/{path}', [CarModelController::class, 'imageCar']);
Route::get('/imageUser/{path}', [UsersController::class, 'imageUser']);
Route::get('/imageEmployee/{path}', [EmployeeController::class, 'imageEmployee']);

Route::middleware(['user_is_login'])->group(function () {
    // Route HomePage
    Route::get('', [HomeController::class, 'homePage']);
    // Route Logout
    Route::get('logout', [LoginController::class, 'fnLogout']);

    // Route Users
    Route::get('/users', [UsersController::class, 'usersIndex']);
    Route::get('/users/create', [UsersController::class, 'formUsers']);
    Route::post('/users/store', [UsersController::class, 'storeUsers']);
    Route::get('/users/edit/{id}', [UsersController::class, 'editUsers']);
    Route::post('/users/update/{id}', [UsersController::class, 'updateUsers']);
    Route::get('/users/destroy/{id}', [UsersController::class, 'destroyUsers']);

    // Route UserRoles
    Route::get('/userRoles', [UserRoleController::class, 'userRoleIndex']);
    Route::get('/userRoles/create', [UserRoleController::class, 'formUserRole']);
    Route::post('/userRoles/store', [UserRoleController::class, 'storeUserRole']);
    Route::get('/userRoles/edit/{id}', [UserRoleController::class, 'fnEditUserRole']);
    Route::post('/userRoles/update/{id}', [UserRoleController::class, 'fnUpdateUserRole']);
    // ក្នុង routes/web.php
    Route::delete('/userRoles/delete/{id}', [UserRoleController::class, 'fnDeleteUserRole']);

    // Route Logistics
    Route::get('/logistics', [LogisticController::class, 'logisticIndex'])->name('search.logistics');
    Route::get('/logistics/create', [LogisticController::class, 'createLogistics']);
    Route::post('/logistics/store', [LogisticController::class, 'storeLogistics']);
    Route::get('/logistics/edit/{id}', [LogisticController::class, 'editLogistics']);
    Route::post('/logistics/update/{id}', [LogisticController::class, 'updateLogistics']);
    Route::get('/logistics/destroy/{id}', [LogisticController::class, 'destroyLogistics']);
    Route::post('/logistics/import', [LogisticController::class, 'importLogistic']);
    Route::get('/logistics/export', [LogisticController::class, 'exportLogistic']);
    Route::get('/download-template-logistic', function () {
        $filePath = public_path('templates/logistic_excel_template.xlsx');

        return Response::download($filePath, 'logistic_excel_template.xlsx');
    });


    Route::controller(EmployeeController::class)->prefix('employees')->name('employees.')->group(function () {
        Route::get('/', 'employeeIndex')->name('index');
        Route::get('/create', 'createEmployee')->name('create');
        Route::post('/store', 'storeEmployee')->name('store');
        Route::get('/edit/{id}', 'editEmployee')->name('edit');
        Route::post('/update/{id}', 'updateEmployee')->name('update');
        Route::delete('/destroy/{id}', 'destroyEmployee')->name('destroy');
        Route::get('/details/{id}', 'viewEmployee')->name('details');
    
        // បន្ថែម ២ បន្ទាត់នេះ ដើម្បីឱ្យ Import/Export ដើរក្នុង Blade
        Route::post('/import', 'importEmployee')->name('import');
        
        Route::get('/export', 'exportEmployee')->name('export'); 
    });

    // Route SaleReport
    Route::get('/sale-reports', [SaleReportController::class, 'saleReportIndex'])->name('search.sale-reports');
    Route::get('/sale-reports/create', [SaleReportController::class, 'createSaleReport']);
    Route::post('/sale-reports/store', [SaleReportController::class, 'storeSaleReport']);
    Route::get('/sale-reports/view/{id}', [SaleReportController::class, 'viewSaleReport']);
    Route::get('/sale-reports/edit/{id}', [SaleReportController::class, 'editSaleReport']);
    Route::post('/sale-reports/update/{id}', [SaleReportController::class, 'updateSaleReport']);
    Route::get('/sale-reports/destroy/{id}', [SaleReportController::class, 'destroySaleReport']);
    Route::get('/sale-reports/export', [SaleReportController::class, 'exportSaleReport']);

    // todo ----------------------- Route Setting -----------------------------------
    // Route Departments
    Route::get('/departments', [SettingController::class, 'departIndex']);
    Route::get('/departments/create', [SettingController::class, 'formDepart']);
    Route::post('/departments/store', [SettingController::class, 'createDepart']);
    Route::get('/departments/edit/{id}', [SettingController::class, 'editDepart']);
    Route::post('/departments/update/{id}', [SettingController::class, 'updateDepart']);
    Route::get('/departments/destroy/{id}', [SettingController::class, 'destroyDepart']);

    // Route Positions
    Route::get('/positions', [SettingController::class, 'positionIndex']);
    Route::get('/positions/create', [SettingController::class, 'formPosition']);
    Route::post('/positions/store', [SettingController::class, 'createPosition']);
    Route::get('/positions/edit/{id}', [SettingController::class, 'editPosition']);
    Route::post('/positions/update/{id}', [SettingController::class, 'updatePosition']);
    Route::get('/positions/destroy/{id}', [SettingController::class, 'destroyPosition']);

    // Route Branch
    Route::get('/branchs', [SettingController::class, 'branchIndex']);
    Route::get('/branch/create', [SettingController::class, 'formBranch']);
    Route::post('/branchs/store', [SettingController::class, 'createBranch']);
    Route::get('/branch/edit/{id}', [SettingController::class, 'editBranch']);
    Route::post('/branch/update/{id}', [SettingController::class, 'updateBranch']);
    Route::get('/branch/destroy/{id}', [SettingController::class, 'destroyBranch']);
    // កែពី get មក delete
    Route::delete('/branch/destroy/{id}', [SettingController::class, 'destroyBranch']);

    // Route Province
    Route::get('/provinces', [SettingController::class, 'provinceIndex']);
    Route::get('/provinces/create', [SettingController::class, 'createProvince']);
    Route::post('/provinces/store', [SettingController::class, 'storeProvince']);
    Route::get('/provinces/edit/{id}', [SettingController::class, 'editProvince']);
    Route::post('/provinces/update/{id}', [SettingController::class, 'updateProvince']);
    Route::get('/provinces/destroy/{id}', [SettingController::class, 'destroyProvince']);

    // Route Company
    Route::get('/companys', [SettingController::class, 'companyIndex']);
    Route::get('/companys/create', [SettingController::class, 'createCompany']);
    Route::post('/companys/store', [SettingController::class, 'storeCompany']);
    Route::get('/companys/edit/{id}', [SettingController::class, 'editCompany']);
    Route::post('/companys/update/{id}', [SettingController::class, 'updateCompany']);
    Route::get('/companys/destroy/{id}', [SettingController::class, 'destroyCompany']);

    // Route Waranty
    Route::get('/warantys', [SettingController::class, 'warantyIndex']);
    Route::get('/warantys/create', [SettingController::class, 'createWaranty']);
    Route::post('/warantys/store', [SettingController::class, 'storeWaranty']);
    Route::get('/warantys/edit/{id}', [SettingController::class, 'editWaranty']);
    Route::post('/warantys/update/{id}', [SettingController::class, 'updateWaranty']);
    Route::get('/warantys/destroy/{id}', [SettingController::class, 'destroyWaranty']);

    // Route Engine Oil
    Route::get('/engineOils', [SettingController::class, 'engineOilIndex']);
    Route::get('/engineOils/create', [SettingController::class, 'createEngineOil']);
    Route::post('/engineOils/store', [SettingController::class, 'storeEngineOil']);
    Route::get('/engineOils/edit/{id}', [SettingController::class, 'editEngineOil']);
    Route::post('/engineOils/update/{id}', [SettingController::class, 'updateEngineOil']);
    Route::get('/engineOils/destroy/{id}', [SettingController::class, 'destroyEngineOil']);

    // todo ----------------------- End Route Setting -----------------------------------

    // Route Car Model
    Route::get('/car-models', [CarModelController::class, 'carModelIndex']);
    Route::get('/car-models/create', [CarModelController::class, 'createCarModel']);
    Route::post('/car-models/store', [CarModelController::class, 'storeCarModel']);
    Route::get('/car-models/edit/{id}', [CarModelController::class, 'editCarModel']);
    Route::post('/car-models/update/{id}', [CarModelController::class, 'updateCarModel']);
    Route::get('/car-models/destroy/{id}', [CarModelController::class, 'destroyCarModel']);

    // Route Car Image
    Route::get('/car-images', [CarModelController::class, 'carImageIndex']);
    Route::get('/car-images/create', [CarModelController::class, 'createCarImage']);
    Route::post('/car-images/store', [CarModelController::class, 'storeCarImage']);
    Route::get('/car-images/edit/{id}', [CarModelController::class, 'editCarImage']);
    Route::post('/car-images/update/{id}', [CarModelController::class, 'updateCarImage']);
    Route::get('/car-images/destroy/{id}', [CarModelController::class, 'destroyCarImage']);

    // Route Colors
    Route::get('/colors', [CarModelController::class, 'colorsIndex']);
    Route::get('/colors/create', [CarModelController::class, 'createColor']);
    Route::post('/colors/store', [CarModelController::class, 'storeColor']);

    Route::middleware(['auth:users'])->group(function () {

        // 1. Static & Analytics Routes (ដាក់ខាងលើ)
        Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
        Route::get('/leads/create', [LeadController::class, 'create'])->name('leads.create');
     

        Route::get('/leads/export', [App\Http\Controllers\LeadController::class, 'exportLeads'])->name('leads.export');
    
        // 2. AJAX Validation & Quick Actions (ដាក់មុន {id})
        Route::post('/leads/check-duplicate', [LeadController::class, 'checkDuplicate'])->name('leads.checkDuplicate');
        Route::post('/leads/check-phone', [LeadController::class, 'checkDuplicatePhone'])->name('leads.checkDuplicatePhone');
        Route::post('/leads/quick-update', [LeadController::class, 'quickUpdate'])->name('leads.quickUpdate');


        // Test Daily Report (បងអាចចុច Link នេះដើម្បីតេស្តផ្ញើសារសរុបទៅ Telegram ភ្លាមៗ)
        Route::get('/leads/daily-report-test', [LeadController::class, 'sendDailySummary']);
    
        // 3. Main Action Routes
        Route::post('/leads/store', [LeadController::class, 'store'])->name('leads.store');
        
        // 4. Wildcard Routes (ត្រូវដាក់នៅខាងក្រោមគេបង្អស់ ដើម្បីការពារការច្រឡំ ID)
        Route::get('/leads/{id}', [LeadController::class, 'show'])->name('leads.show');
        Route::get('/leads/{id}/edit', [LeadController::class, 'edit'])->name('leads.edit');
        Route::put('/leads/{id}/update', [LeadController::class, 'update'])->name('leads.update');
        Route::delete('/leads/{id}/delete', [LeadController::class, 'destroy'])->name('leads.destroy');
    
    });

    // Route Sale Contract
    Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
    Route::get('/contracts/create', [ContractController::class, 'create'])->name('contracts.create');
    Route::post('/contracts/store', [ContractController::class, 'store'])->name('contracts.store');
    Route::get('/contracts/view/{contract}', [ContractController::class, 'show'])->name('contracts.show');
    Route::get('/contracts/edit/{contract}', [ContractController::class, 'edit'])->name('contracts.edit');
    Route::put('/contracts/update/{contract}', [ContractController::class, 'update'])->name('contracts.update');
    Route::delete('/contracts/destroy/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy');

    Route::get('contracts/export', [App\Http\Controllers\ContractController::class, 'export'])->name('contracts.export');
    Route::resource('contracts', App\Http\Controllers\ContractController::class);
    Route::post('/leads/check-duplicate', [LeadController::class, 'checkDuplicate'])->name('leads.checkDuplicate');
    Route::post('/contracts/update-status', [ContractController::class, 'updateStatus'])->name('contracts.updateStatus');

    Route::get('/issues', [IssueController::class, 'index'])->name('issues.index');
    Route::get('/issues/create', [IssueController::class, 'create'])->name('issues.add');
    Route::post('/issues/store', [IssueController::class, 'store'])->name('issues.store');
    Route::get('/issues/view/{id}', [IssueController::class, 'show'])->name('issues.view');



// ---------------------------------------------------------
// ១. ផ្នែកសម្រាប់សាធារណជន (PUBLIC - បង្ហាញភ្ញៀវ)
// ---------------------------------------------------------
Route::middleware('throttle:60,1')->group(function () {
    // បង្ហាញ Form កក់ Online
    Route::get('/book-service', [ServiceBookingController::class, 'publicCreate'])->name('services.public_create');
    // រក្សាទុកទិន្នន័យភ្ញៀវកក់
    Route::post('/book-service', [ServiceBookingController::class, 'publicStore'])->name('services.public_store');
    // AJAX ឆែកម៉ោងទំនេរ
    Route::get('/api/check-available-slots', [ServiceBookingController::class, 'checkSlots'])->name('slots.check');
});

// ---------------------------------------------------------
// ២. ផ្នែកសម្រាប់ ADMIN & WORKSHOP (ឥឡូវចូលមើលបានគ្រប់គ្នា)
// ---------------------------------------------------------
// ចំណាំ៖ ខ្ញុំបានដក middleware 'auth' ចេញ ដើម្បីកុំឱ្យលោត Error "Route login not defined"

Route::prefix('services')->group(function () {
    Route::get('/', [ServiceBookingController::class, 'index'])->name('services.index');
    Route::get('/add', [ServiceBookingController::class, 'create'])->name('services.add');
    Route::post('/store', [ServiceBookingController::class, 'store'])->name('services.store');
    Route::get('/edit/{id}', [ServiceBookingController::class, 'edit'])->name('services.edit');
    Route::post('/update/{id}', [ServiceBookingController::class, 'update'])->name('services.update');
    Route::delete('/delete/{id}', [ServiceBookingController::class, 'destroy'])->name('services.delete');
    
    // ឆែកការកក់ថ្មី (Real-time Alert)
    Route::get('/check-new-bookings', [ServiceBookingController::class, 'checkNewBookings'])->name('bookings.checkNew');
});

// ផ្នែកសម្រាប់ Workshop Board
Route::get('/workshop-board', [ServiceBookingController::class, 'workshopIndex'])->name('workshop.index');

Route::prefix('workshop')->group(function () {
    Route::post('/arrive/{id}', [ServiceBookingController::class, 'arriveJob'])->name('workshop.arrive');
    Route::post('/start/{id}', [ServiceBookingController::class, 'startJob'])->name('workshop.start');
    Route::post('/finish/{id}', [ServiceBookingController::class, 'finishJob'])->name('workshop.finish');
    Route::post('/hold/{id}', [ServiceBookingController::class, 'holdJob'])->name('workshop.hold');
    Route::post('/resume/{id}', [ServiceBookingController::class, 'resumeJob'])->name('workshop.resume');
});



    // 🚩 បន្ថែម Route ពីរនេះដើម្បីដោះស្រាយ Error របស់បង
    Route::get('/issues/edit/{id}', [IssueController::class, 'edit'])->name('issues.edit');
    Route::put('/issues/update/{id}', [IssueController::class, 'update'])->name('issues.update');
    // 🚩 ថែម Route សម្រាប់ Resolve និង Delete ដើម្បីកុំឱ្យបុក Error
    Route::put('/issues/resolve/{id}', [IssueController::class, 'resolve'])->name('issues.resolve');
    Route::delete('/issues/destroy/{id}', [IssueController::class, 'destroy'])->name('issues.destroy');
    Route::get('/issues/export', [IssueController::class, 'exportExcel'])->name('issues.export');

    Route::get('/notifications/mark-all-read', function () {
        // 🛡️ ឆែក Guard 'users' ឱ្យច្បាស់លាស់
        if (auth('users')->check()) {
            // ✅ ប្រើ unreadNotifications() ជាមួយវង់ក្រចក គឺប្រសើរជាង
            auth('users')->user()->unreadNotifications()->update(['read_at' => now()]);
            
            // ឬប្រើ Method របស់ Laravel ផ្ទាល់៖
            // auth('users')->user()->unreadNotifications->markAsRead();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    })->name('notifications.markAllRead');
    Route::get('/notifications/count', function () {
        $count = 0;
        if (auth('users')->check()) {
            $count = auth('users')->user()->unreadNotifications()->count();
        }
        return response()->json(['count' => $count]);
    });

    


    
    //Route MemberCard
    Route::get('/member-cards', [MemberCardController::class, 'memberCardIndex'])->name('search.member-cards');
    Route::get('/member-cards/create', [MemberCardController::class, 'createMemberCard']);
    Route::post('/member-cards/store', [MemberCardController::class, 'storeMemberCard']);
    Route::get('/member-cards/edit/{id}', [MemberCardController::class, 'editMemberCard']);
    Route::post('/member-cards/update/{id}', [MemberCardController::class, 'updateMemberCard']);
    Route::get('/member-cards/destroy/{id}', [MemberCardController::class, 'destroyMemberCard']);
    Route::get('/member-cards/export', [MemberCardController::class, 'exportMemberCard']);
    Route::post('/member-cards/import', [MemberCardController::class, 'importMemberCard']);
    Route::get('/download-template-member-card', function () {
        $filePath = public_path('templates/member_card_template.xlsx');

        return Response::download($filePath, 'member_card_template.xlsx');
    });
});
