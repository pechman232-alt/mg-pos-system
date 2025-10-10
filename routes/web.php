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
use App\Http\Controllers\CarModelController;
use App\Http\Controllers\MemberCardController;
use App\Http\Controllers\BranchController;


// Route Login
Route::get('login', [LoginController::class , 'loginPage']);
Route::post('login', [LoginController::class, 'fnLogin']);

Route::get('/imageCar/{path}',[CarModelController::class, 'imageCar']);
Route::get('/imageUser/{path}',[UsersController::class, 'imageUser']);
Route::get('/imageEmployee/{path}',[EmployeeController::class, 'imageEmployee']);


Route::middleware(['user_is_login'])->group(function () {
    // Route HomePage
    Route::get('',[HomeController::class, 'homePage']);
    // Route Logout
    Route::get('logout', [LoginController::class, 'fnLogout']);

    // Route Users
    Route::get('/users', [UsersController::class, 'usersIndex']);
    Route::get('/users/create', [UsersController::class, 'formUsers']);
    Route::post('/users/store', [UsersController::class, 'storeUsers']);
    Route::get('/users/edit/{id}',[UsersController::class, 'editUsers']);
    Route::post('/users/update/{id}',[UsersController::class, 'updateUsers']);
    Route::get('/users/destroy/{id}',[UsersController::class, 'destroyUsers']);


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
    Route::get('/employees/edit/{id}', [EmployeeController::class, 'editEmployee']);
    Route::post('/employees/update/{id}',[EmployeeController::class, 'updateEmployee']);
    Route::get('/employees/destroy/{id}',[EmployeeController::class, 'destroyEmployee']);
    Route::get('/employees/details/{id}',[EmployeeController::class, 'viewEmployee']);

    // Route SaleReport
    Route::get('/sale-reports', [SaleReportController::class, 'saleReportIndex']);
    Route::get('/sale-reports/create', [SaleReportController::class, 'createSaleReport']);
    Route::post('/sale-reports/store', [SaleReportController::class, 'storeSaleReport']);
    Route::get('/sale-reports/view/{id}', [SaleReportController::class, 'viewSaleReport']);
     
    // todo ----------------------- Route Setting -----------------------------------
    // Route Departments
    Route::get('/departments', [SettingController::class, 'departIndex']);
    Route::get('/departments/create', [SettingController::class, 'formDepart']);
    Route::post('/departments/store', [SettingController::class, 'createDepart']);
    Route::get('/departments/edit/{id}', [SettingController::class, 'editDepart']);

    // Route Positions
    Route::get('/positions', [SettingController::class, 'positionIndex']);
    Route::get('/positions/create',[SettingController::class, 'formPosition']);
    Route::post('/positions/store',[SettingController::class, 'createPosition']);

    // Route Branch
    Route::get('/branchs',[SettingController::class, 'branchIndex']);
    Route::get('/branch/create', [SettingController::class, 'formBranch']);
    Route::post('/branchs/store', [SettingController::class,'createBranch']);
    Route::get('/branch/edit/{id}', [SettingController::class, 'editBranch']);
    Route::post('/branch/update/{id}', [SettingController::class, 'update']);
    
    

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

    // Route Car Model
    Route::get('/car-models',[CarModelController::class, 'carModelIndex']);
    Route::get('/car-models/create', [CarModelController::class, 'createCarModel']);
    Route::post('/car-models/store',[CarModelController::class, 'storeCarModel']);

    // Route Car Image
    Route::get('/car-images',[CarModelController::class, 'carImageIndex']);
    Route::get('/car-images/create',[CarModelController::class, 'createCarImage']);
    Route::post('/car-images/store',[CarModelController::class, 'storeCarImage']);


    // Route Colors
    Route::get('/colors',[CarModelController::class, 'colorsIndex']);
    Route::get('/colors/create', [CarModelController::class, 'createColor']);
    Route::post('/colors/store', [CarModelController::class, 'storeColor']);

    //Route MemberCard
    Route::get('/member-cards',[MemberCardController::class, 'memberCardIndex']);
    Route::get('/member-cards/create',[MemberCardController::class, 'createMemberCard']);
    Route::post('/member-cards/store',[MemberCardController::class, 'storeMemberCard']);
    Route::get('/member-cards/edit/{id}',[MemberCardController::class, 'editMemberCard']);
    Route::post('/member-cards/update/{id}',[MemberCardController::class, 'updateMemberCard']);
    Route::get('/member-cards/destroy/{id}',[MemberCardController::class, 'destroyMemberCard']);
});
