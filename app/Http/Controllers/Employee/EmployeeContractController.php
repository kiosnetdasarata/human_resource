<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreContractRequest;
use App\Services\EmployeeService;
use Illuminate\Http\Request;

class EmployeeContractController extends Controller
{
    public function __construct(private EmployeeService $employeeService) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        try {
            $data = $this->employeeService->getEmployeeContracts($id);
            if ($data)
            return response()->json([
                'success' => true,
                'data' => $data,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 500
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($id, StoreContractRequest $request)
    {
        try {
            $this->employeeService->storeEmployeeContract($id, $request->validated());
            return response()->json([
                'success' => true,
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'input' => $request->validated(),
                'status_code' => $e->getCode() == null ? 500 : $e->getCode(),
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
                'data' => $this->employeeService->findEmployeeContract($id),
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => $e->getCode() == null ? 500 : $e->getCode(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $this->employeeService->updateEmployeeContract($id, $request);

            return response()->json([
                'success' => true,
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'input' => $request->validated(),
                'status_code' => $e->getCode() == null ? 500 : $e->getCode(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->employeeService->deleteEmployeeContract($id);

            return response()->json([
                'success' => true,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => $e->getCode() == null ? 500 : $e->getCode(),
            ]);
        }
    }
}
