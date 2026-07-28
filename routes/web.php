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
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\POS\CheckoutController;
use App\Http\Controllers\POS\VendorController;
use App\Http\Controllers\POS\ExpenseAnalyticsController;
use App\Models\User;
use Carbon\Carbon;


use App\Http\Controllers\POS\ReceiveHeaderController;

// 1. Check Auth Route
Route::get('/check-auth-session', function () {
    return response()->json(['active' => Auth::guard('users')->check()]);
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => redirect('/bWctYm9va2luZy1zZXJ2aWNl'));
Route::get('login', fn() => abort(404)); // បិទ login ចាស់
Route::get('login', [LoginController::class, 'loginPage'])->name('login');
Route::post('login', [LoginController::class, 'fnLogin']);

Route::prefix('bWctYm9va2luZy1zZXJ2aWNl')->group(function () {
    Route::get('/', [ServiceBookingController::class, 'publicCreate'])->name('services.public_create');
    Route::post('/store', [ServiceBookingController::class, 'publicStore'])->name('services.public_store');
    Route::get('/success/{id}', [ServiceBookingController::class, 'showTicket'])->name('services.show_ticket');
    Route::get('/ticket/{id}', [ServiceBookingController::class, 'showPublicTicket'])->name('services.public_ticket');
});

Route::get('/api/check-available-slots', [ServiceBookingController::class, 'checkSlots'])->name('slots.check');
Route::get('/vip/{card_number}', [MemberCardController::class, 'scanDigitalCard'])->name('vip.scan')->where('card_number', '.*');

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

    Route::get('', [HomeController::class, 'homePage'])->name('home');
    Route::get('logout', [LoginController::class, 'fnLogout'])->name('logout');

    Route::get('/dashboard', [LeadController::class, 'dashboard'])->name('dashboard');
    Route::get('/daily-tasks', [LeadController::class, 'dailyTasks'])->name('daily-tasks');

    // --- Services ---
    Route::prefix('services')->controller(ServiceBookingController::class)->group(function () {
        Route::get('/', 'index')->name('services.index');
        Route::get('/create', 'create')->name('services.create');
        Route::post('/store', 'store')->name('services.store');
        Route::get('/edit/{id}', 'edit')->name('services.edit'); // កុំប្រើ Route::resource បើមាន Route ដៃ
        Route::post('/update/{id}', 'update')->name('services.update');
        Route::delete('/delete/{id}', 'delete')->name('services.delete');
        Route::post('/note/{id}', 'updateNote');
        Route::post('/cancel/{id}', 'cancelBooking')->name('services.cancel');
        Route::post('/archive/{id}', 'archive')->name('services.archive');
        Route::get('/notifications', 'getNotifications')->name('services.notifications');
    });

    // --- Workshop ---
    Route::get('/workshop-board', [ServiceBookingController::class, 'workshopIndex'])->name('workshop.index');
    Route::prefix('workshop')->name('workshop.')->controller(ServiceBookingController::class)->group(function () {
        Route::post('/arrive/{id}', 'arriveJob')->name('arrive');
        Route::post('/start/{id}', 'startJob')->name('start');
        Route::post('/finish/{id}', 'finishJob')->name('finish');
        Route::post('/hold/{id}', 'holdJob')->name('hold');
        Route::post('/resume/{id}', 'resumeJob')->name('resume');
    });

   // --- Leads ---
Route::controller(LeadController::class)->prefix('leads')->name('leads.')->group(function () {
    // Basic CRUD
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/view/{id}', 'show')->name('show');
    Route::get('/{id}/edit', 'edit')->name('edit');
    Route::put('/{id}/update', 'update')->name('update');
    
    // Management
    Route::get('/archive/{id}', 'archive')->name('archive');
    Route::get('/restore/{id}', 'restore')->name('restore');
    Route::get('/export', 'exportLeads')->name('export');
    Route::post('/check-duplicate', 'checkDuplicate')->name('checkDuplicate');
    
    // AJAX & Utilities
    Route::post('/quick-update', 'quickUpdate')->name('quickUpdate');
    Route::post('/transfer', 'transfer')->name('transfer');
    Route::post('/reschedule', 'reschedule')->name('reschedule');
    Route::post('/update-manager-comment', 'updateManagerComment')->name('updateManagerComment');
    
    // Payoff Alert
    Route::post('/send-payoff-alert', 'sendPayoffAlert')->name('payoff_alert');
});

// Route សម្រាប់ Notifications (អោយត្រូវតាមដែលបងហៅ)
Route::get('/leads/notifications', [HomeController::class, 'getLeadNotifications'])->name('leads.notifications');

    // --- Users & Roles ---
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

    // --- Logistics ---
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

    // --- Employees ---
    Route::controller(EmployeeController::class)->prefix('employees')->name('employees.')->group(function () {
        Route::get('/', 'employeeIndex')->name('index');
        Route::get('/create', 'createEmployee')->name('create');
        Route::post('/store', 'storeEmployee')->name('store');
        Route::get('/generate-id', 'generateEmployeeId')->name('generate_id');

        // 🌟 នេះគឺជា Route ថ្មីសម្រាប់ទាញយក File Excel Template 🌟
        Route::get('/download-template', 'downloadTemplate')->name('download_template');

        Route::get('/edit/{id}', 'editEmployee')->name('edit');
        Route::post('/update/{id}', 'updateEmployee')->name('update');
        Route::delete('/destroy/{id}', 'destroyEmployee')->name('destroy');

        // (ខ្ញុំឃើញបងប្រើឈ្មោះ details ដូច្នេះក្នុង index.blade.php ប៊ូតុង View ត្រូវប្រើ route('employees.details', $encryptedId) ណាទើបត្រូវ)
        Route::get('/details/{id}', 'viewEmployee')->name('details');

        // 🌟 Route ថ្មីសម្រាប់មុខងារ Resign (ឈប់ពីការងារ) 🌟
        Route::patch('/resign/{id}', 'resignEmployee')->name('resign');

        Route::post('/import', 'importEmployee')->name('import');
        Route::get('/export', 'exportEmployee')->name('export');
    });

   // --- Sale Reports ---
Route::controller(SaleReportController::class)->prefix('sale-reports')->group(function () {

    // 🌟 មុខងារ Dashboard (ថ្មី) 🌟
    Route::get('/dashboard', 'dashboard')->name('sale-reports.dashboard');

    Route::get('/', 'saleReportIndex')->name('search.sale-reports');
    Route::get('/create', 'createSaleReport');
    Route::post('/store', 'storeSaleReport');
    Route::get('/view/{id}', 'viewSaleReport');
    Route::get('/edit/{id}', 'editSaleReport');
    Route::post('/update/{id}', 'updateSaleReport');
    Route::get('/destroy/{id}', 'destroySaleReport');
    Route::get('/export', 'exportSaleReport');

    // 🌟 មុខងារ Quick Feedback & Remark (AJAX) 🌟
    Route::post('/quick-feedback', 'quickUpdateFeedback')->name('sale-reports.quick-feedback');
    Route::post('/quick-remark', 'quickUpdateRemark')->name('sale-reports.quick-remark');

    // 🌟 Route សម្រាប់មុខងារ Import និង Template 🌟
    Route::get('/download-template', 'downloadTemplate');
    Route::post('/import', 'importSaleReport');

}); // បិទ Group Sale Reports ត្រឹមនេះ


    // --- Sale Contracts ---
    Route::controller(ContractController::class)->prefix('contracts')->name('contracts.')->group(function () {

        // 🌟 ទំព័រដើម និង ការបង្កើតកិច្ចសន្យា
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');

        // 🌟 ទាញយកទិន្នន័យ Excel (ត្រូវដាក់នៅខាងលើ Route ដែលមាន {id})
        Route::get('/export', 'export')->name('export');

        // 🌟 API & Ajax សម្រាប់ JavaScript
        Route::get('/search-member-cards', 'searchMemberCards')->name('searchMemberCards');
        Route::post('/scan-id', 'scanIdCard')->name('scanId'); // កែពាក្យ contracts.scanId ចេញ
        Route::post('/check-duplicate', 'checkDuplicate')->name('checkDuplicate');
        Route::post('/update-status', 'updateStatus')->name('updateStatus');

        // 🌟 ការមើល កែប្រែ លុប និងទាញយក PDF
        Route::get('/view/{contract}', 'show')->name('show');
        Route::get('/edit/{id}', 'edit')->name('edit'); // ប្តូរទៅ {id} ឱ្យត្រូវជាមួយ Controller
        Route::put('/update/{id}', 'update')->name('update'); // ប្តូរទៅ {id} ឱ្យត្រូវជាមួយ Controller
        Route::delete('/destroy/{contract}', 'destroy')->name('destroy');
        Route::get('/{id}/download-pdf', 'downloadPdf')->name('download_pdf');

    });
    // --- Issue Tracking ---
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

    // --- Workshop & Service Management (Admin & Staff) ---
    Route::get('/performance-overview', [ServiceBookingController::class, 'performanceOverview'])->name('services.performance');
    Route::post('/services/note/{id}', [ServiceBookingController::class, 'updateNote']);

    // បន្ថែមនៅក្រោម Route របស់ Services ផ្សេងទៀត
    Route::post('/services/cancel/{id}', [App\Http\Controllers\ServiceBookingController::class, 'cancelBooking'])->name('services.cancel');
    Route::post('/services/archive/{id}', [App\Http\Controllers\ServiceBookingController::class, 'archive'])->name('services.archive');
    // កែពី LeadController មកជា HomeController (ឬកែទៅតាមទីតាំងជាក់ស្តែងដែល Function នោះស្ថិតនៅ)
Route::get('/notifications', [App\Http\Controllers\HomeController::class, 'getLeadNotifications'])->name('leads.notifications');

    Route::prefix('services')->controller(ServiceBookingController::class)->group(function () {
        Route::get('/', 'index')->name('services.index');
        Route::get('/add', 'create')->name('services.add');
        Route::post('/store', 'store')->name('services.store');
        Route::get('/edit/{id}', 'edit')->name('services.edit');
        Route::post('/update/{id}', 'update')->name('services.update');

        // ✅ កែពី 'destroy' មក 'delete' ព្រោះក្នុង Controller មុខងាររបស់បងឈ្មោះ delete()
        Route::delete('/delete/{id}', 'delete')->name('services.delete');

    });

    // Route សម្រាប់ Check ទិន្នន័យកក់ថ្មី (ទុកដាច់ដោយឡែកដើម្បីកុំឱ្យច្រឡំ name prefix)
    Route::get('/check-new-bookings', [ServiceBookingController::class, 'checkNewBookings'])->name('bookings.checkNew');

    Route::get('/workshop-board', [ServiceBookingController::class, 'workshopIndex'])->name('workshop.index');
    Route::controller(ServiceBookingController::class)->prefix('workshop')->name('workshop.')->group(function () {
        Route::post('/arrive/{id}', 'arriveJob')->name('arrive');
        Route::post('/start/{id}', 'startJob')->name('start');
        Route::post('/finish/{id}', 'finishJob')->name('finish');
        Route::post('/hold/{id}', 'holdJob')->name('hold');
        Route::post('/resume/{id}', 'resumeJob')->name('resume');
    });

    // --- Settings (Departments, Positions, Branches, etc.) ---
    Route::controller(SettingController::class)->group(function () {
        // Departments
        Route::get('/departments', 'departIndex');
        Route::get('/departments/create', 'formDepart');
        Route::post('/departments/store', 'createDepart');
        Route::get('/departments/edit/{id}', 'editDepart');
        Route::post('/departments/update/{id}', 'updateDepart');
        Route::get('/departments/destroy/{id}', 'destroyDepart');

        // Positions
        Route::get('/positions', 'positionIndex');
        Route::get('/positions/create', 'formPosition');
        Route::post('/positions/store', 'createPosition');
        Route::get('/positions/edit/{id}', 'editPosition');
        Route::post('/positions/update/{id}', 'updatePosition');
        Route::get('/positions/destroy/{id}', 'destroyPosition');

        // Branches
        Route::get('/branchs', 'branchIndex')->name('branches.index');
        Route::get('/branch/create', 'formBranch');
        Route::post('/branchs/store', 'createBranch');
        Route::get('/branch/edit/{id}', 'editBranch');
        Route::post('/branch/update/{id}', 'updateBranch');
        Route::delete('/branch/destroy/{id}', 'destroyBranch')->name('branches.destroy');

        // Provinces
        Route::get('/provinces', 'provinceIndex');
        Route::get('/provinces/create', 'createProvince');
        Route::post('/provinces/store', 'storeProvince');
        Route::get('/provinces/edit/{id}', 'editProvince');
        Route::post('/provinces/update/{id}', 'updateProvince');
        Route::get('/provinces/destroy/{id}', 'destroyProvince');

        // Other Settings...
        Route::get('/companys', 'companyIndex');
        Route::post('/companys/store', 'storeCompany');
        Route::get('/warantys', 'warantyIndex');
        Route::get('/engineOils', 'engineOilIndex');
    });

    // --- Car Masters (Models, Images, Colors) ---
    Route::controller(CarModelController::class)->group(function () {

        // 1. Car Models
        Route::get('/car-models', 'carModelIndex');
        Route::get('/car-models/create', 'createCarModel'); // ទំព័រ Form បង្កើតម៉ូដែល
        Route::post('/car-models/store', 'storeCarModel'); // កន្លែងទទួលទិន្នន័យ Model

        // 2. Car Images (🌟 បងត្រូវថែម ២ ជួរខាងក្រោមនេះ)
        Route::get('/car-images', 'carImageIndex');
        Route::get('/car-images/create', 'createCarImage'); // 👈 ជួរនេះសម្រាប់បង្ហាញ Form Upload រូបភាព
        Route::post('/car-images/store', 'storeCarImage');   // 👈 ជួរនេះសម្រាប់ទទួលរូបភាពទៅ Save

        // 3. Colors
        Route::get('/colors', 'colorsIndex');
        Route::get('/colors/create', 'createColor');
        Route::post('/colors/store', 'storeColor');
    });

    // --- Member Cards ---
    Route::controller(MemberCardController::class)->prefix('member-cards')->group(function () {
        Route::get('/', 'memberCardIndex')->name('search.member-cards');
        Route::get('/create', 'createMemberCard');
        Route::post('/store', 'storeMemberCard');
        Route::get('/edit/{id}', 'editMemberCard');
        Route::post('/update/{id}', 'updateMemberCard');
        Route::get('/destroy/{id}', 'destroyMemberCard');
        Route::get('/export', 'exportMemberCard');
        Route::post('/import', 'importMemberCard');
        // Route សម្រាប់អតិថិជន Scan (អត់ដាក់ Middleware Auth ទេ)


        // 🌟 បន្ថែម Route ថ្មីសម្រាប់ Download Excel Template 🌟
        Route::get('/download-template', 'downloadTemplate');
    });

    // --- Notifications Helper ---
    Route::get('/notifications/mark-all-read', function () {
        if (auth('users')->check()) {
            auth('users')->user()->unreadNotifications()->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    })->name('notifications.markAllRead');

    Route::get('/notifications/count', function () {
        return response()->json(['count' => auth('users')->check() ? auth('users')->user()->unreadNotifications()->count() : 0]);
    });

    // --- Templates Download ---
    Route::get('/download-template-logistic', fn () => Response::download(public_path('templates/logistic_excel_template.xlsx')));
    Route::get('/download-template-member-card', fn () => Response::download(public_path('templates/member_card_template.xlsx')));
    Route::prefix('pos')->group(function () {

        // ==========================================
        // 1. Checkout (Sale Invoice)
        // ==========================================
        Route::get('/checkout', [App\Http\Controllers\POS\CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout/process', [App\Http\Controllers\POS\CheckoutController::class, 'process'])->name('checkout.process');
        Route::post('/checkout/check-car-invoice', [App\Http\Controllers\POS\CheckoutController::class, 'checkCarInvoice'])->name('checkout.check-car-invoice');

        // ✅ Route សម្រាប់ទាញយកលេខ PI អូតូ (កែតម្រូវលែងឱ្យជាន់ពាក្យ /pos)
        Route::get('/checkout/get-latest-pi/{product_id}', [App\Http\Controllers\POS\CheckoutController::class, 'getLatestPI']); 

        // ✅ ប្រវត្តិលក់ និង បោះពុម្ព (Dynamic Print)
        Route::get('/sales/history', [App\Http\Controllers\POS\CheckoutController::class, 'history'])->name('sales.history');
        Route::get('/sales/print/{encryptedId}', [App\Http\Controllers\POS\CheckoutController::class, 'printDocument'])->name('print.sales');


        // ==========================================
        // 2. Receive Headers & Items (ទិញចូល)
        // ==========================================
        Route::resource('receive-headers', App\Http\Controllers\POS\ReceiveHeaderController::class);
        Route::get('/receive-headers/{id}/print', [App\Http\Controllers\POS\ReceiveHeaderController::class, 'print'])->name('receive-headers.print');

        Route::get('/receive-items', [App\Http\Controllers\POS\ReceiveItemController::class, 'index'])->name('receive-items.index');
        Route::post('/receive-items/store', [App\Http\Controllers\POS\ReceiveItemController::class, 'store'])->name('receive-items.store');
        Route::delete('/receive-items/{id}', [App\Http\Controllers\POS\ReceiveItemController::class, 'destroy'])->name('receive-items.destroy');
       
        // downloadTemplate
        Route::get('/vendors/template', [VendorController::class, 'downloadTemplate'])->name('vendors.template');
        Route::post('/vendors/import', [VendorController::class, 'importExcel'])->name('vendors.import');


        // ==========================================
        // 3. Reports (របាយការណ៍)
        // ==========================================
        Route::get('/reports', [App\Http\Controllers\POS\ReportController::class, 'dailySalesReport'])->name('reports.daily-sales');
        Route::get('/reports/export', [App\Http\Controllers\POS\ReportController::class, 'exportExcel'])->name('reports.daily-sales-export');

        Route::get('/reports/inventory', [App\Http\Controllers\POS\ReportController::class, 'inventoryBalance'])->name('reports.inventory');
        Route::get('/reports/inventory/export', [App\Http\Controllers\POS\ReportController::class, 'exportInventory'])->name('reports.inventory-export');

        Route::get('/reports/stock-in', [App\Http\Controllers\POS\ReportController::class, 'stockInReport'])->name('reports.stock-in');

     
        // ==========================================
        // 4. Master Data (ទិន្នន័យគោល)
        // ==========================================
        Route::resource('vendors', App\Http\Controllers\POS\VendorController::class);
        Route::resource('products', App\Http\Controllers\POS\ProductController::class);

    });
    Route::get('/pos/reports/expense/dashboard', [ExpenseAnalyticsController::class, 'index'])->name('expense.dashboard');
// Route សម្រាប់ Clear Cache លើ Live Server
Route::get('/clear-cache', function() {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    return "Cache is cleared successfully! កូដថ្មីត្រូវបាន Update ១០០% 🎉";
});
    
});
