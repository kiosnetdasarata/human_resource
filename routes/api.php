<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\JobTitleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\StatusLevelController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EmployeeHistoryController;
use App\Http\Controllers\ZoneController;

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
Route::middleware('jwt:api')->group(function() {
    Route::get('/province', [ZoneController::class, 'getProvinces']);
    Route::get('/{province}/regency', [ZoneController::class, 'getRegencies']);
    Route::get('/{regency}/district', [ZoneController::class, 'getDistricts']);
    Route::get('/{district}/village', [ZoneController::class, 'getVillages']);
    Route::get('/division/{division}/job-titles', [JobTitleController::class, 'index']);

    Route::apiResource('employee', EmployeeController::class);
    Route::apiResource('division', DivisionController::class);
    Route::apiResource('employee-history', EmployeeHistoryController::class);
    Route::apiResource('job-title', JobTitleController::class);
    Route::apiResource('sales', SalesController::class);
    Route::apiResource('status-level', StatusLevelController::class);
    Route::apiResource('technician', TechnicianController::class);

    Route::post('logout', LogoutController::class);
});
Route::middleware('guest:api')->group(function() {
    Route::post('login', LoginController::class);
    Route::post('register', RegisterController::class);
});
