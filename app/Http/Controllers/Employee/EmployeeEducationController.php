<?php

namespace App\Http\Controllers\Employee;

use App\Services\EmployeeService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreEducationRequest;
use App\Http\Requests\Employee\UpdateEducationRequest;

class EmployeeEducationController extends Controller
{
    public function __construct(private EmployeeService $employeeService) 
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->employeeService->findEmployeePersonal($id, 'id')->employeeEducation,
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => $e->getCode(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($uuid, StoreEducationRequest $request)
    {
        try {
            $this->employeeService->addEducation($uuid, $request);
            
            return response()->json([
                'success' => true,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => $e->getCode(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            return response()->json([
                'success' => true,
                'data' =>$this->employeeService->findEmployeePersonal($id, 'id')->employeeEducation[0],
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => $e->getCode(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEducationRequest $request, string $id)
    {
        try {
            $this->employeeService->updateEducation($id, $request);
            return response()->json([
                'success' => true,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => $e->getCode(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->employeeService->deleteEducation($id);
            return response()->json([
                'success' => true,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => $e->getCode(),
            ]);
        }
    }
}
