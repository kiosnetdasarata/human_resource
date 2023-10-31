<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Services\EmployeeService;
use Illuminate\Routing\Controller;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Requests\Employee\FirstFormEmployeeRequest;
use App\Http\Requests\Employee\SecondFormEmployeeRequest;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function __construct(private EmployeeService $employeeService)
    {
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    { 
        try {
            return response()->json([
                'status' => 'success',
                'data' => $this->employeeService->getAllEmployeePersonal(),
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return $this->returnException($e);
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
        } catch (\Exception $e) {
            return $this->returnException($e, $request->validated());
        }
    }

    public function returnException($e, $input = [])
    {
        dd($e);
        return response()->json([
            'status' => 'error',
            'message' => isset($message) ? $message : $e->getMessage(),
            'input' => $input,
            'status_code' => 500,
            'line' => $e->getTrace(),
        ]);
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
            return $this->returnException($e, $request->validated());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $employee = $this->employeeService->findEmployeePersonal($id, 'id');
            if ($employee == null) {
                throw new \Exception('data tidak ditemukan');
            }
            return response()->json([
                'status' => 'success',
                'data' => $employee,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return $this->returnException($e);
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
            return $this->returnException($e, $request->validated());
        }
    }

    public function showEmployeeDetails($uuid)
    {
        try {           
            return response()->json([
                'status' => 'success',
                'data' => $this->employeeService->findEmployeePersonal($uuid, 'id')->employeeCI,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $uuid)
    {
        try {
            $data = Validator::make($request->all(), ['status_terminate' => 'required']);
            if ($data->fails()) throw new \Exception($data->errors());
            
            $this->employeeService->deleteEmployeePersonal($data->validated(), $uuid);
            return response()->json([
                'status' => 'success',
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            $this->returnException($e);
        }
    }
}