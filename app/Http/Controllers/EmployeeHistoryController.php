<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeHistory;
use App\Services\EmployeeHistoryService;
use App\Http\Requests\EmployeeHistory\StoreEmployeeHistoryRequest;
use App\Http\Requests\EmployeeHistory\UpdateEmployeeHistoryRequest;

class EmployeeHistoryController extends Controller
{
    public function __construct(private EmployeeHistoryService $employeeHistoryService)
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
                'data' => $this->employeeHistoryService->getAll(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeHistoryRequest $request)
    {
        try {
            $this->employeeHistoryService->store($request->validated());

            return response()->json([
                'status' => 'success',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'input' => $request->validated(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => $this->employeeHistoryService->find($id),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeHistoryRequest $request, $id)
    {
        try {
            $this->employeeHistoryService->update($id,$request->validated());
            
            return response()->json([
                'status' => 'success'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'input' => $request->validated()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->employeeHistoryService->delete($this->employeeHistoryService->find($id));
            
            return response()->json([
                'status' => 'success',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
