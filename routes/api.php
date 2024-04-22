<?php

use App\Http\Controllers\ArchiveApplicantController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\JobApplicantController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Internship\InternshipController;
use App\Http\Controllers\Internship\PartnershipController;
use App\Http\Controllers\Internship\TraineeshipController;
use App\Http\Controllers\Employee\EmployeeContractController;
use App\Http\Controllers\Employee\EmployeeEducationController;
use App\Http\Controllers\Internship\InterviewPointController;
use App\Http\Controllers\Internship\FilePartnershipController;
use App\Http\Controllers\Internship\InternshipContractController;

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

// Route get branch
Route::get('/branchs', BranchController::class);

// ---------- Job Vacancy ----------
Route::get('/job-vacancy/{id}/job-aplicant', [JobApplicantController::class, 'getByJobVacancy']);
Route::get('/job-vacancy/{id}/traineeship', [TraineeshipController::class, 'getByJobVacancy']);
Route::get('/job-vacancy/role', [JobVacancyController::class, 'role']);
Route::apiResource('job-vacancy', JobVacancyController::class);

// ---------- Zone ----------
Route::controller(ZoneController::class)->prefix('zone')->group(function() {
    Route::get('/provinces', 'getProvinces');
    Route::get('/{province}/regencies', 'getRegencies');
    Route::get('/{regency}/districts', 'getDistricts');
    Route::get('/{district}/villages', 'getVillages');
});

// ---------- Division ----------
Route::get('/division/{division}/role', [RoleController::class, 'index']);
Route::get('/division/{division}/employee', [DivisionController::class, 'getEmployee']);
// Route::get('/division/{division}/employee/archive', [DivisionController::class, 'getEmployeeArchive']);
Route::apiResource('division', DivisionController::class);

// ---------- Role ----------
Route::apiresource('role', RoleController::class);
// ---------- Level ----------
Route::apiResource('level', LevelController::class);

// ---------- Employee ----------
// Route::middleware(['jwt:api'])->group(function() {
    Route::post('/employee/store', [EmployeeController::class, 'storeFormOne']);
    Route::get('/employee/archive', [EmployeeController::class, 'getArchive']);
    Route::get('/employee/division-manager', [EmployeeController::class, 'getManager']);
    Route::post('/employee/{uuid}/update-complete', [EmployeeController::class, 'storeFormTwo']);
    Route::patch('/employee/{uuid}/delete', [EmployeeController::class, 'destroy']);
    Route::get('/employee/{uuid}/contract/history', [EmployeeContractController::class, 'index']);
    Route::get('/employee/{uuid}/education/history', [EmployeeEducationController::class, 'index']);
    Route::apiSingleton('employee.contract', EmployeeContractController::class)->creatable();
    Route::apiSingleton('employee.education', EmployeeEducationController::class)->creatable();
    Route::apiResource('employee', EmployeeController::class)->except(['store','destroy']);
// });

// Route::apiResource('sales', SalesController::class)->except(['store', 'destroy']);
// Route::apiResource('technician', TechnicianController::class)->except(['store', 'destroy']);

// ---------- Job Applicant ----------
Route::get('/job-aplicant/status/{status}', [JobApplicantController::class, 'find']);
Route::patch('job-aplicant/{id}/update-status', [JobApplicantController::class, 'changeStatus']);
Route::apiResource('job-aplicant', JobApplicantController::class)->except(['destroy']);

// ---------- Archive Applicant ----------
Route::get('/aplicant/archive/{id}', [ArchiveApplicantController::class, 'find']);
Route::get('/aplicant/archive', [ArchiveApplicantController::class, 'get']);

// ---------- Traineeship ----------
Route::apiResource('traineeship', TraineeshipController::class)->except(['destroy']);

// ---------- Internship ----------
Route::post('/internship/{idTraineeship}', [InternshipController::class, 'store']);
Route::get('/internship/{idInternship}/contract/history', [InternshipContractController::class, 'index']);
Route::apiResource('internship', InternshipController::class)->except(['store']);
Route::apiSingleton('internship.contract', InternshipContractController::class)->creatable()->except('destroy');

// ---------- Partnership ----------
Route::get('/partnership/{id}/file/history', [FilePartnershipController::class, 'index']);
Route::get('/partnership/{id}/{status}', [PartnershipController::class, 'findInternship']);
Route::get('/partnership/{id}/{status}/archive', [PartnershipController::class, 'findInternshipArchive']);
Route::apiSingleton('partnership.file', FilePartnershipController::class)->creatable()->except('destroy');
Route::apiResource('partnership', PartnershipController::class)->except('destroy');

// ---------- Interview Point ----------
Route::apiSingleton('{applicantType}/{id}/interview-point', InterviewPointController::class)->creatable()->except(['destroy']);


// Route::middleware(['jwt:api'
//         ])->group(function() {
//     Route::get('/get-current-user', function() {
//         return response()->json(['user' => auth()->user()->employee]);
//     });
//     Route::post('logout', LogoutController::class);
// });

// Route::middleware('guest:api')->group(function() {
//     Route::post('login', LoginController::class);
//     Route::post('register', RegisterController::class);
// });
