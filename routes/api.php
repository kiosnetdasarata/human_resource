<?php

use App\Http\Controllers\ArchiveApplicantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\JobApplicantController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Internship\InternshipController;
use App\Http\Controllers\Internship\PartnershipController;
use App\Http\Controllers\Internship\TraineeshipController;
use App\Http\Controllers\Employee\EmployeeContractController;
use App\Http\Controllers\Employee\EmployeeEducationController;
use App\Http\Controllers\Internship\InterviewPointController;
use App\Http\Controllers\Internship\FilePartnershipController;
use App\Http\Controllers\Internship\InternshipContractController;
use App\Models\Partnership;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|


Sebelum komplain link gabisa jalanin dulu "php artisan route:cache"
*/

// Route get branch
Route::get('/branchs', BranchController::class);
//Route JobVacancy
Route::get('/job-vacancy/{id}/job-aplicant', [JobApplicantController::class, 'getByJobVacancy']);
Route::get('/job-vacancy/{id}/job-aplicant/archive', [ArchiveApplicantController::class, 'getJobApplicant']);
Route::get('/job-vacancy/{id}/traineeship', [TraineeshipController::class, 'getByJobVacancy']);
Route::get('/job-vacancy/{id}/traineeship/archive', [ArchiveApplicantController::class, 'getTraineeship']);

Route::get('/job-vacancy/role', [JobVacancyController::class, 'role']);
Route::apiResource('job-vacancy', JobVacancyController::class);
//Route Zone
Route::controller(ZoneController::class)->prefix('zone')->group(function() {
    Route::get('/provinces', 'getProvinces');
    Route::get('/{province}/regencies', 'getRegencies');
    Route::get('/{regency}/districts', 'getDistricts');
    Route::get('/{district}/villages', 'getVillages');
});
// Route get job title by division
Route::get('/division/{division}/role', [RoleController::class, 'index']);
Route::get('/division/{division}/employee', [DivisionController::class, 'getEmployee']);
Route::get('/division/{division}/employee/archive', [DivisionController::class, 'getEmployeeArchive']);
Route::apiResource('division', DivisionController::class);
// Route get Job Title
Route::apiresource('role', RoleController::class);
// Route get Level
Route::apiResource('level', LevelController::class);


/*
Sebelum komplain link gabisa jalanin dulu "php artisan route:cache"
*/

//Punya Al, form 3 pake updatenya employee resource ln.61
Route::middleware(['jwt:api'])->group(function() {
    Route::post('/employee/store', [EmployeeController::class, 'storeFormOne']);
    Route::get('/employee/archive', [EmployeeController::class, 'getArchive']);
    Route::post('/employee/{uuid}/update-complete', [EmployeeController::class, 'storeFormTwo']);
    Route::get('/employee/{uuid}/contract/history', [EmployeeContractController::class, 'index']);
    Route::get('/employee/{uuid}/education/history', [EmployeeEducationController::class, 'index']);
    Route::patch('/employee/{uuid}/delete', [EmployeeController::class, 'destroy']);
    Route::apiSingleton('employee.contract', EmployeeContractController::class)->creatable();
    Route::apiSingleton('employee.education', EmployeeEducationController::class)->creatable();
    Route::apiResource('employee', EmployeeController::class)->except(['store','destroy']);
});

Route::apiResource('sales', SalesController::class)->except(['store', 'destroy']);
Route::apiResource('technician', TechnicianController::class)->except(['store', 'destroy']);

Route::get('/aplicant/archive/{id}', [ArchiveApplicantController::class, 'find']);
Route::get('/job-aplicant/status/{status}', [JobApplicantController::class, 'find']);
Route::get('/job-aplicant/archive', [ArchiveApplicantController::class, 'getJobApplicant']);
Route::patch('job-aplicant/{id}/update-status', [JobApplicantController::class, 'changeStatus']);
Route::apiResource('job-aplicant', JobApplicantController::class);

//Punya Aul
Route::get('/traineeship/archive', [ArchiveApplicantController::class, 'getTraineeship']);
Route::apiResource('traineeship', TraineeshipController::class)->except(['destroy']);

Route::post('/internship/{idTraineeship}', [InternshipController::class, 'store']); //create internship pake ini,
Route::apiResource('internship', InternshipController::class)->except(['store']);
Route::get('/internship/{idInternship}/contract/history', [InternshipContractController::class, 'index']);
Route::apiSingleton('internship.contract', InternshipContractController::class)->creatable()->except('destroy');


Route::get('/partnership/{IdMitra}/file/history', [FilePartnershipController::class, 'index']);
Route::apiSingleton('partnership.file', FilePartnershipController::class)->creatable()->except('destroy');
Route::get('/partnership/{id}/{status}', [PartnershipController::class, 'findInternship']);
Route::get('/partnership/{id}/{status}/archive', [PartnershipController::class, 'findInternshipArchive']);
Route::apiSingleton('partnership.file', FilePartnershipController::class)->creatable()->except('destroy');
Route::apiResource('partnership', PartnershipController::class)->except('destroy');

Route::apiSingleton('{ApplicantType}/{id}/interview-point', InterviewPointController::class)->creatable()->except(['destroy']);


Route::middleware(['jwt:api'
        ])->group(function() {
    Route::get('/get-current-user', function() {
        return response()->json(['user' => auth()->user()->employee]);
    });
    Route::post('logout', LogoutController::class);    
});

Route::post('/test-file', function(Request $request) {
    dd(uploadToGCS($request->file,null, '/test'));
});

Route::middleware('guest:api')->group(function() {
    Route::post('login', LoginController::class);
    Route::post('register', RegisterController::class);
});