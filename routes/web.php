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
use App\Http\Controllers\ServiceBookingController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CapacityManagerController; 
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// POS Controllers
use App\Http\Controllers\POS\CheckoutController;
use App\Http\Controllers\POS\VendorController;
use App\Http\Controllers\POS\ExpenseAnalyticsController;
use App\Http\Controllers\POS\ReceiveHeaderController;
use App\Http\Controllers\POS\ReceiveItemController;
use App\Http\Controllers\POS\ReportController;
use App\Http\Controllers\POS\ProductController;
use App\Http\Controllers\AuditLogController;

use App\Models\User;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Public Routes & Check Auth
|--------------------------------------------------------------------------
*/
Route::get('/check-auth-session', function () {
    return response()->json(['active' => Auth::guard('users')->check()]);
});

// 🌟 ទំព័រដើម (Under Maintenance) 🌟
Route::get('/', function () {
    return '
    <div style="display:flex; flex-direction:column; justify-content:center; align-items:center; height:100vh; font-family:\'Plus Jakarta Sans\', sans-serif; background-color:#04060f; color:#ffffff; text-align:center;">
        <h1 style="font-size: 3rem; font-weight: bold; margin-bottom: 10px;">🛠️ Under Maintenance</h1>
        <p style="font-size: 1.2rem; color: #a5b4fc;">We are currently upgrading our system. Please check back later.</p>
    </div>
    ';
});

// 🌟 Route សម្រាប់អ្នកគ្រប់គ្រង Login (Admin Portal) 🌟
Route::get('admin-portal', [LoginController::class, 'loginPage'])->name('login');
Route::post('admin-portal', [LoginController::class, 'fnLogin'])
    ->middleware('throttle:5,1');
// 🌟 Route សម្រាប់អតិថិជនកក់អនឡាញ (Public Booking) 🌟
Route::prefix('mg-service')->group(function () {
    Route::get('/', [ServiceBookingController::class, 'publicCreate'])->name('services.public_create');
    Route::post('/store', [ServiceBookingController::class, 'publicStore'])->middleware('throttle:30,1')->name('services.public_store');
    Route::get('/success/{id}', [ServiceBookingController::class, 'showTicket'])->name('services.show_ticket');
    Route::get('/ticket/{id}', [ServiceBookingController::class, 'showPublicTicket'])->name('services.public_ticket');
});

Route::get('/api/check-available-slots', [ServiceBookingController::class, 'checkSlots'])->middleware('throttle:30,1')->name('slots.check');
Route::get('/vip/{card_number}', [MemberCardController::class, 'scanDigitalCard'])->middleware('throttle:20,1')->name('vip.scan')->where('card_number', '[A-Za-z0-9\-]+');

// Image Routes
Route::get('/imageCar/{path}', [CarModelController::class, 'imageCar']);
Route::get('/imageUser/{path}', [UsersController::class, 'imageUser']);
Route::get('/imageEmployee/{path}', [EmployeeController::class, 'imageEmployee']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['user_is_login', 'auth:users'])->group(function () {

    Route::get('/home', [HomeController::class, 'homePage'])->name('home');
    Route::get('logout', [LoginController::class, 'fnLogout'])->name('logout');

    Route::get('/dashboard', [LeadController::class, 'dashboard'])->name('dashboard');
    Route::get('/daily-tasks', [LeadController::class, 'dailyTasks'])->name('daily-tasks');

    // ==========================================
    // 1. Services Management
    // ==========================================
    // រក្សា Route នេះនៅខាងក្រៅ Prefix 'services' ដើម្បីកុំឱ្យខូច URL ចាស់
    Route::get('/performance-overview', [ServiceBookingController::class, 'performanceOverview'])->name('services.performance');
    Route::get('/check-new-bookings', [ServiceBookingController::class, 'checkNewBookings'])->name('bookings.checkNew');

    // 🌟 ការប្រើប្រាស់ ->name('services.') នៅទីនេះ គឺត្រូវតាមស្តង់ដារ Laravel
    Route::prefix('services')->name('services.')->controller(ServiceBookingController::class)->group(function () {
        
        // 🔹 Standard CRUD (បង្កើត កែប្រែ លុប)
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('/add', 'create')->name('add'); // ទុកសម្រាប់ទ្រនាប់ URL ចាស់
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'delete')->name('delete');
        
        // 🔹 Specific Actions (ប្រតិបត្តិការផ្សេងៗ)
        Route::post('/note/{id}', 'updateNote')->name('updateNote');
        Route::post('/cancel/{id}', 'cancelBooking')->name('cancel');
        Route::post('/archive/{id}', 'archive')->name('archive');
        Route::post('/estimation/{id}', 'updateEstimation')->name('updateEstimation');
        Route::post('/update-vin', 'updateVin')->name('updateVin');
        
        // 🔹 API & Data Fetching (ទាញយកទិន្នន័យ)
        Route::get('/notifications', 'getNotifications')->name('notifications');
        Route::get('/check-walkin-slots', 'checkWalkinSlots')->name('checkWalkinSlots');
        Route::get('/daily-report', 'dailyReport')->name('dailyReport');
    });

    // ==========================================
    // 2. Capacity Manager
    // ==========================================
    Route::get('/capacity-manager', [CapacityManagerController::class, 'index'])->name('capacity.daily_tracking');

    // ==========================================
    // 3. Workshop Board
    // ==========================================
    // រក្សា Route នេះនៅខាងក្រៅ Prefix ដើម្បីកុំឱ្យ URL ក្លាយជា /workshop/workshop-board
    Route::get('/workshop-board', [ServiceBookingController::class, 'workshopIndex'])->name('workshop.index');
    
    // 🌟 Group ដាច់ដោយឡែកសម្រាប់សកម្មភាពរបស់ជាង (Mechanic Actions)
    Route::prefix('workshop')->name('workshop.')->controller(ServiceBookingController::class)->group(function () {
        Route::post('/arrive/{id}', 'arriveJob')->name('arrive');
        Route::post('/start/{id}', 'startJob')->name('start');
        Route::post('/finish/{id}', 'finishJob')->name('finish');
        Route::post('/hold/{id}', 'holdJob')->name('hold');
        Route::post('/resume/{id}', 'resumeJob')->name('resume');
    });
    // ==========================================
    // 4. Leads Management
    // ==========================================
    Route::controller(LeadController::class)->prefix('leads')->name('leads.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/view/{id}', 'show')->name('show');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}/update', 'update')->name('update');
        Route::get('/archive/{id}', 'archive')->name('archive');
        Route::get('/restore/{id}', 'restore')->name('restore');
        Route::get('/export', 'exportLeads')->name('export');
        
        // AJAX
        Route::post('/check-duplicate', 'checkDuplicate')->name('checkDuplicate');
        Route::post('/quick-update', 'quickUpdate')->name('quickUpdate');
        Route::post('/transfer', 'transfer')->name('transfer');
        Route::post('/reschedule', 'reschedule')->name('reschedule');
        Route::post('/update-manager-comment', 'updateManagerComment')->name('updateManagerComment');
        Route::post('/send-payoff-alert', 'sendPayoffAlert')->name('payoff_alert');
    });
    Route::get('/leads/notifications', [HomeController::class, 'getLeadNotifications'])->name('leads.notifications');

    // ==========================================
    // 5. Users & Roles
    // ==========================================
    Route::controller(UsersController::class)->prefix('users')->group(function () {
        Route::get('/', 'usersIndex')->name('users.index');
        Route::get('/create', 'formUsers');
        Route::post('/store', 'storeUsers');
        Route::get('/edit/{id}', 'editUsers');
        Route::post('/update/{id}', 'updateUsers');
        Route::get('/destroy/{id}', 'destroyUsers');
    });

    Route::controller(UserRoleController::class)->prefix('userRoles')->group(function () {
        Route::get('/', 'userRoleIndex')->name('userRoles.index');
        Route::get('/create', 'formUserRole');
        Route::post('/store', 'storeUserRole');
        Route::get('/edit/{id}', 'fnEditUserRole');
        Route::post('/update/{id}', 'fnUpdateUserRole');
        Route::delete('/delete/{id}', 'fnDeleteUserRole');
    });

    // ==========================================
    // 6. Logistics
    // ==========================================
    Route::controller(LogisticController::class)->prefix('logistics')->group(function () {
        Route::get('/', 'logisticIndex')->name('search.logistics');
        Route::get('/create', 'createLogistics');
        Route::post('/store', 'storeLogistics');
        Route::get('/edit/{id}', 'editLogistics');
        Route::post('/update/{id}', 'updateLogistics');
        Route::get('/destroy/{id}', 'destroyLogistics');
        Route::post('/import', 'importLogistic');
        Route::get('/export', 'exportLogistic');
    });

    // ==========================================
    // 7. Employees
    // ==========================================
    Route::controller(EmployeeController::class)->prefix('employees')->name('employees.')->group(function () {
        Route::get('/', 'employeeIndex')->name('index');
        Route::get('/create', 'createEmployee')->name('create');
        Route::post('/store', 'storeEmployee')->name('store');
        Route::get('/generate-id', 'generateEmployeeId')->name('generate_id');
        Route::get('/download-template', 'downloadTemplate')->name('download_template');
        Route::get('/edit/{id}', 'editEmployee')->name('edit');
        Route::post('/update/{id}', 'updateEmployee')->name('update');
        Route::delete('/destroy/{id}', 'destroyEmployee')->name('destroy');
        Route::get('/details/{id}', 'viewEmployee')->name('details');
        Route::patch('/resign/{id}', 'resignEmployee')->name('resign');
        Route::post('/import', 'importEmployee')->name('import');
        Route::get('/export', 'exportEmployee')->name('export');
    });

    // ==========================================
    // 8. Sale Reports
    // ==========================================
    Route::controller(SaleReportController::class)->prefix('sale-reports')->group(function () {
        Route::get('/dashboard', 'dashboard')->name('sale-reports.dashboard');
        Route::get('/', 'saleReportIndex')->name('search.sale-reports');
        Route::get('/create', 'createSaleReport');
        Route::post('/store', 'storeSaleReport');
        Route::get('/view/{id}', 'viewSaleReport');
        Route::get('/edit/{id}', 'editSaleReport');
        Route::post('/update/{id}', 'updateSaleReport');
        Route::get('/destroy/{id}', 'destroySaleReport');
        Route::get('/export', 'exportSaleReport');
        Route::post('/quick-feedback', 'quickUpdateFeedback')->name('sale-reports.quick-feedback');
        Route::post('/quick-remark', 'quickUpdateRemark')->name('sale-reports.quick-remark');
        Route::get('/download-template', 'downloadTemplate');
        Route::post('/import', 'importSaleReport');


        Route::get('/follow-up-tracking', 'followUpTracking')->name('sale-reports.follow-up-tracking');
    });

    // ==========================================
    // 9. Sale Contracts
    // ==========================================
    Route::controller(ContractController::class)->prefix('contracts')->name('contracts.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/export', 'export')->name('export');
        
        // API & Ajax
        Route::get('/search-member-cards', 'searchMemberCards')->name('searchMemberCards');
        Route::post('/scan-id', 'scanIdCard')->name('scanId');
        Route::post('/check-duplicate', 'checkDuplicate')->name('checkDuplicate');
        Route::post('/update-status', 'updateStatus')->name('updateStatus');
        
        // Actions
        Route::get('/view/{contract}', 'show')->name('show');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::put('/update/{id}', 'update')->name('update');
        Route::delete('/destroy/{contract}', 'destroy')->name('destroy');
        Route::get('/{id}/download-pdf', 'downloadPdf')->name('download_pdf');
    });

    // ==========================================
    // 10. Issue Tracking
    // ==========================================
    Route::controller(IssueController::class)->prefix('issues')->name('issues.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('add');
        Route::post('/store', 'store')->name('store');
        Route::get('/view/{id}', 'show')->name('view');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::put('/update/{id}', 'update')->name('update');
        Route::put('/resolve/{id}', 'resolve')->name('resolve');
        Route::delete('/destroy/{id}', 'destroy')->name('destroy');
        Route::get('/export', 'exportExcel')->name('export');
    });

    // ==========================================
    // 11. Settings (Departments, Branches, etc.)
    // ==========================================
    Route::controller(SettingController::class)->group(function () {
        Route::get('/departments', 'departIndex');
        Route::get('/departments/create', 'formDepart');
        Route::post('/departments/store', 'createDepart');
        Route::get('/departments/edit/{id}', 'editDepart');
        Route::post('/departments/update/{id}', 'updateDepart');
        Route::get('/departments/destroy/{id}', 'destroyDepart');

        Route::get('/positions', 'positionIndex');
        Route::get('/positions/create', 'formPosition');
        Route::post('/positions/store', 'createPosition');
        Route::get('/positions/edit/{id}', 'editPosition');
        Route::post('/positions/update/{id}', 'updatePosition');
        Route::get('/positions/destroy/{id}', 'destroyPosition');

        Route::get('/branchs', 'branchIndex')->name('branches.index');
        Route::get('/branch/create', 'formBranch');
        Route::post('/branchs/store', 'createBranch');
        Route::get('/branch/edit/{id}', 'editBranch');
        Route::post('/branch/update/{id}', 'updateBranch');
        Route::delete('/branch/destroy/{id}', 'destroyBranch')->name('branches.destroy');

        Route::get('/provinces', 'provinceIndex');
        Route::get('/provinces/create', 'createProvince');
        Route::post('/provinces/store', 'storeProvince');
        Route::get('/provinces/edit/{id}', 'editProvince');
        Route::post('/provinces/update/{id}', 'updateProvince');
        Route::get('/provinces/destroy/{id}', 'destroyProvince');

        Route::get('/companys', 'companyIndex');
        Route::post('/companys/store', 'storeCompany');
        Route::get('/warantys', 'warantyIndex');
        Route::get('/engineOils', 'engineOilIndex');
    });

    // ==========================================
    // 12. Car Masters
    // ==========================================
    Route::controller(CarModelController::class)->group(function () {
        Route::get('/car-models', 'carModelIndex');
        Route::get('/car-models/create', 'createCarModel');
        Route::post('/car-models/store', 'storeCarModel');

        Route::get('/car-images', 'carImageIndex');
        Route::get('/car-images/create', 'createCarImage');
        Route::post('/car-images/store', 'storeCarImage');

        Route::get('/colors', 'colorsIndex');
        Route::get('/colors/create', 'createColor');
        Route::post('/colors/store', 'storeColor');
    });

    // ==========================================
    // 13. Member Cards
    // ==========================================
    Route::controller(MemberCardController::class)->prefix('member-cards')->group(function () {
        Route::get('/', 'memberCardIndex')->name('search.member-cards');
        Route::get('/create', 'createMemberCard');
        Route::post('/store', 'storeMemberCard');
        Route::get('/edit/{id}', 'editMemberCard');
        Route::post('/update/{id}', 'updateMemberCard');
        Route::get('/destroy/{id}', 'destroyMemberCard');
        Route::get('/export', 'exportMemberCard');
        Route::post('/import', 'importMemberCard');
        Route::get('/download-template', 'downloadTemplate');
    });

    // ==========================================
    // 14. Notifications & Template Downloads
    // ==========================================
    Route::get('/notifications/mark-all-read', function () {
        if (auth('users')->check()) {
            auth('users')->user()->unreadNotifications()->update(['read_at' => now()]);
        }
        return response()->json(['success' => true]);
    })->name('notifications.markAllRead');

    Route::get('/notifications/count', function () {
        return response()->json(['count' => auth('users')->check() ? auth('users')->user()->unreadNotifications()->count() : 0]);
    });

    Route::get('/download-template-logistic', fn () => Response::download(public_path('templates/logistic_excel_template.xlsx')));
    Route::get('/download-template-member-card', fn () => Response::download(public_path('templates/member_card_template.xlsx')));

    // ==========================================
    // 15. POS System
    // ==========================================
    Route::prefix('pos')->group(function () {
        // Checkout
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
        Route::post('/checkout/check-car-invoice', [CheckoutController::class, 'checkCarInvoice'])->name('checkout.check-car-invoice');
        Route::get('/checkout/get-latest-pi/{product_id}', [CheckoutController::class, 'getLatestPI']);
        Route::get('/checkout/get-items-by-branch/{branch_id}', [CheckoutController::class, 'getItemsByBranch'])->name('checkout.get-items-by-branch');
        Route::get('/sales/history', [CheckoutController::class, 'history'])->name('sales.history');
        Route::get('/sales/print/{encryptedId}', [CheckoutController::class, 'printDocument'])->name('print.sales');

        Route::get('/products/get-last-code', function() {
            $lastProduct = \App\Models\Product::latest('id')->first();
            $nextId = $lastProduct ? $lastProduct->id + 1 : 1;
            return response()->json(['code' => 'PRD-' . str_pad($nextId, 3, '0', STR_PAD_LEFT)]);
        });

        // Receive Headers & Items
        Route::resource('receive-headers', ReceiveHeaderController::class);
        Route::get('/receive-headers/{id}/print', [ReceiveHeaderController::class, 'print'])->name('receive-headers.print');
        Route::get('/receive-items', [ReceiveItemController::class, 'index'])->name('receive-items.index');
        Route::post('/receive-items/store', [ReceiveItemController::class, 'store'])->name('receive-items.store');
        Route::delete('/receive-items/{id}', [ReceiveItemController::class, 'destroy'])->name('receive-items.destroy');
        
        // Vendors
        Route::get('/vendors/template', [VendorController::class, 'downloadTemplate'])->name('vendors.template');
        Route::post('/vendors/import', [VendorController::class, 'importExcel'])->name('vendors.import');

        // Reports
        Route::get('/reports', [ReportController::class, 'dailySalesReport'])->name('reports.daily-sales');
        Route::get('/reports/export', [ReportController::class, 'exportExcel'])->name('reports.daily-sales-export');
        Route::get('/reports/inventory', [ReportController::class, 'inventoryBalance'])->name('reports.inventory');
        Route::get('/reports/inventory/export', [ReportController::class, 'exportInventory'])->name('reports.inventory-export');
        Route::get('/reports/stock-in', [ReportController::class, 'stockInReport'])->name('reports.stock-in');
// ==========================================
// System Audit Logs
// ==========================================
Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
Route::post('/audit-logs/clear', [AuditLogController::class, 'clear'])->name('audit-logs.clear');
        // Master Data
        Route::resource('vendors', VendorController::class);
        Route::resource('products', ProductController::class);
    });
    
    Route::get('/pos/reports/expense/dashboard', [ExpenseAnalyticsController::class, 'index'])->name('expense.dashboard');
});

Route::get('/404-error', function () {

    return view('errors.404');
})->name('error.page');


Route::fallback(function () {
    return redirect('/404-error');
});