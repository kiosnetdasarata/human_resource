<?php

use App\Models\Employee;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\JobTitleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\StatusLevelController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EmployeeHistoryController;
use App\Http\Controllers\Internship\TraineeshipController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('/test', function () {
    // dd(Employee::all());
});
Route::post('/employee/store', [EmployeeController::class, 'storeFormOne']);
Route::get('/employee/confidential/{uuid}', [EmployeeController::class, 'showEmployeeDetails']);
Route::post('/employee/{uuid}/update-complete', [EmployeeController::class, 'storeFormTwo']);
Route::apiResource('employee', EmployeeController::class);
// ->only(['index, show, delete']);
// Route::middleware([
    // 'jwt:api',
    // 'is_human_resource,
    // ])->group(function() {
    //Route Zone
    Route::controller(ZoneController::class)->group(function() {
        Route::get('/provinces', 'getProvinces');
        Route::get('/{province}/regencies', 'getRegencies');
        Route::get('/{regency}/districts', 'getDistricts');
        Route::get('/{district}/villages', 'getVillages');
    });

    // Route get job title by division
    Route::get('/division/{division}/job-titles', [JobTitleController::class, 'index']);

    // Route get branch
    Route::get('/branchs', BranchController::class);

    //Route get level
    Route::get('/level', [LevelController::class, 'getLevels']);
    Route::get('/level/{level}/commissions', [LevelController::class, 'getCommissions']);

    Route::apiResource('division', DivisionController::class)->except(['show']);
    Route::apiResource('employee-history', EmployeeHistoryController::class)->except(['create']);
    Route::apiResource('job-title', JobTitleController::class)->except(['show']);
    Route::apiResource('sales', SalesController::class)->except(['create']);
    Route::apiResource('status-level', StatusLevelController::class)->except(['show']);
    Route::apiResource('technician', TechnicianController::class)->except(['create']);
    Route::apiResource('traineeship', TraineeshipController::class);
    Route::apiResource('employee', EmployeeController::class)->except(['create']);

    Route::post('/employees/create', [EmployeesController::class, 'storeFormOne']);
    Route::post('/employees/full-create/{uuid}', [EmployeesController::class, 'storeFormTwo']);

    // Route::get('/employee-archive/{nip}', [EmployeeController::class, 'show'])->withTrashed();

    Route::post('logout', LogoutController::class);
// });

Route::middleware('guest:api')->group(function() {
    Route::post('login', LoginController::class);
    Route::post('register', RegisterController::class);
});
