<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\StatusLevelController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EmployeeHistoryController;
use App\Http\Controllers\Internship\InternshipController;
use App\Http\Controllers\Internship\PartnershipController;
use App\Http\Controllers\Internship\TraineeshipController;
use App\Http\Controllers\Internship\FilePartnershipController;

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
/*
Sebelum komplain link gabisa jalanin dulu "php artisan route:cache"
*/

// Route get branch
Route::get('/branchs', BranchController::class);
//Route JobVacancy
Route::get('job-vacancy/role', [JobVacancyController::class, 'role']);
Route::apiResource('job-vacancy', JobVacancyController::class)->only('index', 'show');
//Route Zone
Route::controller(ZoneController::class)->prefix('zone')->group(function() {
    Route::get('/provinces', 'getProvinces');
    Route::get('/{province}/regencies', 'getRegencies');
    Route::get('/{regency}/districts', 'getDistricts');
    Route::get('/{district}/villages', 'getVillages');
});
// Route get job title by division
Route::get('/division/{division}/role', [RoleController::class, 'index']);
Route::apiResource('division', DivisionController::class)->except(['show']);
// Route get Job Title
Route::apiResource('role', RoleController::class)->except(['show']);


/*
Sebelum komplain link gabisa jalanin dulu "php artisan route:cache"
*/

//Punya Al, form 3 pake updatenya employee resource ln.61
Route::post('/employee/store', [EmployeeController::class, 'storeFormOne']);
Route::post('/employee/{uuid}/update-complete', [EmployeeController::class, 'storeFormTwo']);
Route::get('/employee/confidential/{uuid}', [EmployeeController::class, 'showEmployeeDetails']);
Route::apiResource('employee', EmployeeController::class)->except(['create']); 

//Punya Aul
Route::apiResource('traineeship', TraineeshipController::class);
Route::post('/internship/{idTraineeship}', [InternshipController::class, 'store']); //create internship pake ini,
Route::apiResource('internship', InternshipController::class)->except(['create']);
Route::get('partnership/{mitraId}/file', [FilePartnershipController::class, 'index']);
Route::get('partnership/{mitraId}/file/{type}', [FilePartnershipController::class, 'show']);
Route::post('partnership/{mitraId}/file', [FilePartnershipController::class, 'store']);

Route::apiResource('partnership', PartnershipController::class);


// Route::middleware([
    // 'jwt:api',
    // 'is_human_resource,
    // ])->group(function() {
    //Route get level
    Route::get('/level', [LevelController::class, 'getLevels']);
    Route::get('/level/{level}/commissions', [LevelController::class, 'getCommissions']);

    Route::apiResource('employee-history', EmployeeHistoryController::class)->except(['create']);

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
