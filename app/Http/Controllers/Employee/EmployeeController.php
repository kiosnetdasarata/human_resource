<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Services\EmployeeService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ItemNotFoundException;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Requests\Employee\FirstFormEmployeeRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Employee\SecondFormEmployeeRequest;

class EmployeeController extends Controller
{
    public function __construct(
        private EmployeeService $employeeService)
    {
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    { 
        try {
            $data = $this->employeeService->getAllEmployeePersonal();
            if (count($data) <= 0) {
                throw new ModelNotFoundException('data tidak ditemukan', 404);
            }
            return response()->json([
                'status' => 'success',
                'data' => $this->employeeService->getAllEmployeePersonal(),
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'status_code' => $e->getCode() == 0 ? 500 : $e->getCode(),
            ]);
        }
    }
    public function getArchive()
    {
        try {
            $data = $this->employeeService->getEmployeeArchive();
            if (!count($data)) throw new ModelNotFoundException('job applicant tidak ditemukan');
            else return response()->json([
                'status' => 'success',
                'data' => $data,
                'status_code' => 200,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage() ?? 'data not found',
                'status_code' => 404,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
                'status_code' => 404,
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeFormOne(FirstFormEmployeeRequest $request)
    {
        try {
            $this->employeeService->firstForm($request->validated());
            return response()->json([
                'status' => 'success',
                'status_code' => 200,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage() ?? 'data not found',
                'input' => $request->validated(),
                'status_code' => 404,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'status_code' => $e->getCode() == 0 ? 500 : $e->getCode(),
            ]);
        }
    }

    public function storeFormTwo($uuid, SecondFormEmployeeRequest $request)
    {
        try {
            $this->employeeService->secondForm($uuid, $request->validated());
            return response()->json([
                'status' => 'success',
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'input' => $request->validated(),
                'status_code' => $e->getCode() == 0 ? 500 : $e->getCode(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $employee = $this->employeeService->findEmployeePersonal($id);
            if ($employee == null) {
                throw new ModelNotFoundException('data tidak ditemukan', 404);
            }
            return response()->json([
                'status' => 'success',
                'data' => $employee,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'status_code' => $e->getCode() == 0 ? 500 : $e->getCode(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, $uuid)
    {
        try {
            $this->employeeService->updateEmployee($uuid, $request->validated());
            return response()->json([
                'status' => 'success',
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'input' => $request->validated(),
                'trace' => $e->getTrace(),
                'status_code' => $e->getCode() == 0 ? 500 : $e->getCode(),
            ]);
        }
    }

    public function showEmployeeDetails($uuid)
    {
        try {           
            return response()->json([
                'status' => 'success',
                'data' => $this->employeeService->findEmployeePersonal($uuid)->employeeCI,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'status_code' => $e->getCode() == 0 ? 404 : $e->getCode(),
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $uuid)
    {
        try {
            $data = Validator::make($request->all(), ['status_terminate' => 'required']);
            if ($data->fails()) throw new \Exception($data->errors()->first());
            
            $this->employeeService->deleteEmployeePersonal($data->validated(), $uuid);
            return response()->json([
                'status' => 'success',
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'status_code' => $e->getCode() == 0 ? 404 : $e->getCode(),
            ]);
        }
    }
}