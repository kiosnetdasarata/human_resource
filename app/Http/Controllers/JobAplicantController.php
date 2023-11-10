<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobAplicant\StoreJobAplicantRequest;
use App\Services\EmployeeService;
use App\Services\JobAplicantService;
use Illuminate\Http\Request;

class JobAplicantController extends Controller
{
    public function __construct(private JobAplicantService $jobAplicantService) 
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->jobAplicantService->get(),
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
    public function store(StoreJobAplicantRequest $request)
    {
        try {
            $this->jobAplicantService->create($request);
            return response()->json([
                'success' => true,
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->jobAplicantService->find($id),
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $this->jobAplicantService->update($id, $request);
            return response()->json([
                'success' => true,
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->jobAplicantService->get(),
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
}
