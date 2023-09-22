<?php

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
use App\Models\Employee;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::middleware([
    // 'jwt:api',
    // 'is_active,
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

    Route::apiResource('employee', EmployeeController::class);
    Route::apiResource('division', DivisionController::class)->except(['show']);
    Route::apiResource('employee-history', EmployeeHistoryController::class)->except(['create']);
    Route::apiResource('job-title', JobTitleController::class)->except(['show']);
    Route::apiResource('sales', SalesController::class)->except(['create']);
    Route::apiResource('status-level', StatusLevelController::class)->except(['show']);
    Route::apiResource('technician', TechnicianController::class)->except(['create']);

    // Route::get('/employee-archive/{nip}', [EmployeeController::class, 'show'])->withTrashed();

    Route::post('logout', LogoutController::class);
// });

Route::middleware('guest:api')->group(function() {
    Route::post('login', LoginController::class);
    Route::post('register', RegisterController::class);
});
