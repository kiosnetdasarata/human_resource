<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Services\EmployeeService;
use Illuminate\Routing\Controller;
use Illuminate\Support\MessageBag;
use Illuminate\Database\QueryException;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Requests\Employee\ArchiveEmployeeRequest;
use App\Http\Requests\Employee\HistoryEmployeeRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use PDOException;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        try {
            $password = $this->employeeService->store($request->validated());

            return response()->json([
                'status' => 'success',
                'password' => $password,
            ], 200);
            
        } catch(HttpResponseException $e) {      
            return $e->getResponse();
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
            $employee = $this->employeeService->find($id);

            return response()->json([
                'status' => 'success',
                'data' => $employee,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function history(HistoryEmployeeRequest $request, $uuid)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, $uuid)
    {
        try {
            $this->employeeService->update($uuid,$request->validated());
            
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
    public function destroy($uuid)
    {
        try {
            $this->employeeService->delete($this->employeeService->find($uuid));
            
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