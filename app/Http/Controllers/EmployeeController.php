<?php

namespace App\Http\Controllers;

use App\Services\EmployeeService;
use Illuminate\Routing\Controller;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Requests\Employee\FirstFormEmployeeRequest;
use App\Http\Requests\Employee\SecondFormEmployeeRequest;

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
        // if ($e instanceof ModelNotFoundException) {
        //     $message = 'modelnya gaada, periksa lagi routenya';
        // } elseif ($e instanceof RuntimeException) {
        //     $message = 'runtime error';
        // }
        return response()->json([
            'status' => 'error',
            'message' => isset($message) ? $message : $e->getMessage(),
            'input' => $input,
            'status_code' => 500,
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
                'data' => $this->employeeService->findEmployeeConfidential($uuid),
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        try {
            $this->employeeService->deleteEmployeePersonal($uuid);
            return response()->json([
                'status' => 'success',
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            $this->returnException($e);
        }
    }
}