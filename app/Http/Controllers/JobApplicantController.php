<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JobApplicantService;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\JobApplicant\StoreJobApplicantRequest;
use App\Http\Requests\JobApplicant\UpdateJobApplicantRequest;
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
            $data = $this->jobApplicantService->get();
            if (!count($data)) throw new ModelNotFoundException();
            return response()->json([
                'success' => true,
                'data' => $this->jobApplicantService->get(),
                'status_code' => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage() ?? 'data not found',
                'status_code' => 404,
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
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage() ?? 'data not found',
                'input' => $request->validated(),
                'status_code' => 404,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'input' => $request->validated(),
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
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage() ?? 'data not found',
                'status_code' => 404,
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
            if ((int) $slug) {
                $jobApplicant = $this->jobApplicantService->find($slug);
            } else {
                $jobApplicant = $this->jobApplicantService->findSlug($slug)->firstOrFail();
                if ($slug != $jobApplicant->slug) throw new ModelNotFoundException('data tidak ditemukan',404);
            } 
            if (!$jobApplicant) throw new ModelNotFoundException('data tidak ditemukan',404);
            return response()->json([
                'success' => true,
                'data' => $jobApplicant,
                'status_code' => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage() ?? 'data not found',
                'status_code' => 404,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }

    public function getByJobVacancy($id)
    {
        try {
            $data = $this->jobApplicantService->getByVacancy($id);
            if (!count($data)) throw new ModelNotFoundException('job applicant tidak ditemukan');
            else return response()->json([
                'status' => 'success',
                'data' => $data,
                'status_code' => 200,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage() ?? 'data not found',
                'status_code' => 404,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
                'status_code' => 404,
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
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage() ?? 'data not found',
                'input' => $request->validated(),
                'status_code' => 404,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'input' => $request->validated(),
                'status_code' => 500,
            ]);
        }
    }

    public function changeStatus($id, Request $request) 
    {
        try {
            $data = Validator::make($request->all(), ['status_tahap' => 'required|in:FU,Assesment,Tolak']);

            $this->jobApplicantService->updateStatus($id, $data->validated());
            return response()->json([
                'success' => true,
                'status_code' => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage() ?? 'data not found',
                'status_code' => 404,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => $e->getCode() == 0 ? 500 : $e->getCode(),
            ]);
        }
    }
}
