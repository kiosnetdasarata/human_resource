<?php

namespace App\Http\Controllers;

use App\Services\EmployeeService;
use Illuminate\Routing\Controller;
use App\Http\Requests\Employee\StoreEmployeeRequest;
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
        return response()->json([
            'status' => 'success',
            'data' => $this->employeeService->getAllEmployeePersonal(),
            'status_code' => 200,
        ]);
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
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'input' => $request->validated(),
                'status_code' => 500,
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
                'status_code' => 500,
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $employee = $this->employeeService->findEmployeePersonal($id, 'uuid');
            return response()->json([
                'status' => 'success',
                'data' => $employee,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(UpdateEmployeeRequest $request, $uuid)
    // {
    //     try {
    //         $this->employeeService->updateEmployeePersonal($uuid, $request->validated());
    //         return response()->json([
    //             'status' => 'success',
    //             'status_code' => 200,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $e->getMessage(),
    //             'input' => $request->validated(),
    //             'status_code' => 500,
    //         ]);
    //     }
    // }

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
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }
}