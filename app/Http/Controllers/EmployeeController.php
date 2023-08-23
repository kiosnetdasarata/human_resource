<?php

namespace App\Http\Controllers;

use App\Services\EmployeeService;
use Illuminate\Routing\Controller;
use Illuminate\Support\MessageBag;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            'data' => $this->employeeService->getAll(),
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $employee = $this->employeeService->store($request->validated());

        if ($employee instanceof MessageBag) {
            return response()->json([
                'status' => 'error',
                'message' => $employee,
                'data' => $request->all(),
            ],422);
        }

        return response()->json([
            'status' => 'success',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $employee = $this->employeeService->find($id);

            return response()->json([
                'status' => 'success',
                'data' => $employee,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, $id)
    {
        try {
            $newEmployee = $this->employeeService->update($id,$request->validated());
            
            if ($newEmployee instanceof MessageBag) {
                return response()->json([
                    'status' => 'error',
                    'message' => $newEmployee,
                    'data' => $request,
                ], 422);
            }
            
            return response()->json([
                'status' => 'success'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $employee = $this->employeeService->find($id);
            $this->employeeService->delete($employee);
            
            return response()->json([
                'status' => 'success',
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
}