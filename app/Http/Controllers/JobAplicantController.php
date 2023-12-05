<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EmployeeService;
use App\Services\JobAplicantService;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\JobAplicant\StoreJobAplicantRequest;
use Ramsey\Uuid\Type\Integer;

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

    public function find(Request $request, $status)
    {
        try {
            // $data = $request->getQueryString();
            // list($key, $value) = explode('=', $data);
            return response()->json([
                'success' => true,
                'data' => $this->jobAplicantService->search('status_tahap', $status),
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
    public function show($id)
    {
        try {
            if($id instanceOf Integer)
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
                'status_code' => $e->getCode() == 0 ? 500 : $e->getCode(),
                'trace' => $e->getTrace()
            ]);
        }
    }

    public function changeStatus($id, Request $request) 
    {
        try {
            $data = Validator::make($request->all(), ['status_tahap' => 'required|in:FU,Assesment,Tolak,Lolos']);
            if ($data->fails()) throw new \Exception($data->errors());

            $this->jobAplicantService->update($id, $data->validated());
            return response()->json([
                'success' => true,
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => $e->getCode() == 0 ? 500 : $e->getCode(),
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
