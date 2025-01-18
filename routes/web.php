<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\LogisticController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SaleReportController;

// Route Login
Route::get('login', [LoginController::class , 'loginPage']);
Route::post('login', [LoginController::class, 'fnLogin']);

Route::middleware(['user_is_login'])->group(function () {
    // Route HomePage
    Route::get('',[HomeController::class, 'homePage']);
    // Route Logout
    Route::get('logout', [LoginController::class, 'fnLogout']);

    // Route Users
    Route::get('/users', [UsersController::class, 'usersIndex']);
    
    Route::get('/users/create', [UsersController::class, 'formUsers']);
    Route::post('/users/store', [UsersController::class, 'storeUsers']);

    // Route UserRoles
    Route::get('/userRoles',[UserRoleController::class, 'userRoleIndex']);
    Route::get('/userRoles/create',[UserRoleController::class, 'formUserRole']);
    Route::post('/userRoles/store',[UserRoleController::class,'storeUserRole']);
    Route::get('/userRoles/edit/{id}',[UserRoleController::class, 'fnEditUserRole']);
    Route::post('/userRoles/update/{id}',[UserRoleController::class, 'fnUpdateUserRole']);

    // Route Logistics
    Route::get('/logistics', [LogisticController::class, 'logisticIndex']);
    Route::get('/logistics/create',[LogisticController::class, 'createLogistics']);
    Route::post('/logistics/store',[LogisticController::class, 'storeLogistics']);
    Route::post('/logistics/import', [LogisticController::class, 'importLogistic']);
    Route::get('/logistics/edit/{id}', [LogisticController::class, 'editLogistics']);
    Route::post('/logistics/update/{id}', [LogisticController::class, 'updateLogistics']);
    Route::get('/logistics/destroy/{id}', [LogisticController::class, 'destroyLogistics']);
    Route::get('/download-template-logistic', function(){
        $filePath = public_path('templates/logistic_excel_template.xlsx');
        return Response::download($filePath, 'logistic_excel_template.xlsx');
    });

    // Route Employee
    Route::get('/employees',[EmployeeController::class, 'employeeIndex']);
    Route::get('/employees/create',[EmployeeController::class, 'createEmployee']);
    Route::post('/employees/store', [EmployeeController::class, 'storeEmployee']);

    // Route SaleReport
    Route::get('/sale-reports', [SaleReportController::class, 'saleReportIndex']);
    Route::get('/sale-reports/create', [SaleReportController::class, 'createSaleReport']);
    Route::get('/sale-reports/store', [SaleReportController::class, 'storeSaleReport']);

    // todo ----------------------- Route Setting -----------------------------------
    // Route Departments
    Route::get('/departments', [SettingController::class, 'departIndex']);
    Route::get('/departments/create', [SettingController::class, 'formDepart']);
    Route::post('/departments/store', [SettingController::class, 'createDepart']);

    // Route Positions
    Route::get('/positions', [SettingController::class, 'positionIndex']);
    Route::get('/positions/create',[SettingController::class, 'formPosition']);
    Route::post('/positions/store',[SettingController::class, 'createPosition']);

    // Route Branch
    Route::get('/branchs',[SettingController::class, 'branchIndex']);
    Route::get('/branch/create', [SettingController::class, 'formBranch']);
    Route::post('/branchs/store', [SettingController::class,'createBranch']);

    // Route Province
    Route::get('/provinces',[SettingController::class, 'provinceIndex']);
    Route::get('/provinces/create',[SettingController::class, 'createProvince']);
    Route::post('/provinces/store',[SettingController::class, 'storeProvince']);

    // Route Company
    Route::get('/companys',[SettingController::class, 'companyIndex']);
    Route::get('/companys/create',[SettingController::class, 'createCompany']);
    Route::post('/companys/store',[SettingController::class, 'storeCompany']);

    // Route Waranty
    Route::get('/warantys',[SettingController::class, 'warantyIndex']);
    Route::get('/warantys/create', [SettingController::class, 'createWaranty']);
    Route::post('/warantys/store', [SettingController::class , 'storeWaranty']);

    // Route Engine Oil
    Route::get('/engineOils',[SettingController::class, 'engineOilIndex']);
    Route::get('/engineOils/create', [SettingController::class, 'createEngineOil']);
    Route::post('/engineOils/store', [SettingController::class , 'storeEngineOil']);

    // todo ----------------------- End Route Setting -----------------------------------
});
