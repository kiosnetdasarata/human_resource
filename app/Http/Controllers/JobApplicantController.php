<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JobApplicantService;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\JobApplicant\StoreJobApplicantRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class JobApplicantController extends Controller
{
    public function __construct(private JobApplicantService $jobApplicantService) 
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
                'data' => $this->jobApplicantService->get(),
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => $e->getCode() == 0 ? 404 : $e->getCode(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobApplicantRequest $request)
    {
        try {
            $this->jobApplicantService->create($request);
            return response()->json([
                'success' => true,
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTrace(),
                'status_code' => $e->getCode() == 0 ? 500 : $e->getCode(),
            ]);
        }
    }

    public function find($status)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->jobApplicantService->search('status_tahap', $status),
            ]);          
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => $e->getCode() == 0 ? 404 : $e->getCode(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($slug) 
    {
        try {
            $jobApplicant = (function () use($slug) {
                if (((int) $slug) == 0) {
                    $jobApplicant = $this->jobApplicantService->findSlug($slug)->firstOrFail();
                    if ($slug != $jobApplicant->slug) throw new ModelNotFoundException('data tidak ditemukan',404);
                    return $jobApplicant;
                } else
                    return $jobApplicant = $this->jobApplicantService->find($slug);
            })();
            if (!$jobApplicant) throw new ModelNotFoundException('data tidak ditemukan',404);
            return response()->json([
                'success' => true,
                'data' => $jobApplicant,
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage() == null ? 'data tidak ditemukan' : $e->getMessage(),
                'status_code' => $e->getCode() == 0 ? 404 : $e->getCode(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $this->jobApplicantService->update($id, $request);
            return response()->json([
                'success' => true,
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTrace(),
                'status_code' => $e->getCode() == 0 ? 500 : $e->getCode(),
            ]);
        }
    }

    public function changeStatus($id, Request $request) 
    {
        try {
            $data = Validator::make($request->all(), ['status_tahap' => 'required|in:FU,Assesment,Tolak,Lolos']);
            if ($data->fails()) throw new \Exception($data->errors());

            $this->jobApplicantService->update($id, $data->validated());
            return response()->json([
                'success' => true,
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTrace(),  
                'status_code' => $e->getCode() == 0 ? 500 : $e->getCode(),
            ]);
        }
    }
}
