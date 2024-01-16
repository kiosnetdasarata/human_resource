<?php

namespace App\Http\Controllers\Internship;

use Illuminate\Http\Request;
use App\Services\InternshipService;
use App\Http\Controllers\Controller;
use App\Services\JobAplicantService;
use App\Http\Requests\InterviewPoint\StoreInterviewPointRequest;
use App\Http\Requests\InterviewPoint\UpdateInterviewPointRequest;

class InterviewPointController extends Controller
{
    public function __construct(
        private InternshipService $internshipService,
        private JobAplicantService $jobAplicantService) {
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store($aplicantType, $id, StoreInterviewPointRequest $request)
    {
        try {
            if ($aplicantType == 'traineeship')
                $score = $this->internshipService->addInterviewPoint($id,$request->validated());
            else if ($aplicantType == 'job-aplicant')
                $score = $this->jobAplicantService->addInterviewPoint($id,$request->validated());
            else throw new \Exception ('Invalid route parameter');

            return response()->json([
                'status' => 'success',
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage() == null ? 'data aplicant tidak ditemukan' : $e->getMessage(),
                'input' => $request->validated(),
                'status_code' => $e->getCode() == 0 ? 500 : $e->getCode(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($aplicantType, $id)
    {
        try {
            if ($aplicantType == 'traineeship')
                $data = $this->internshipService->showInterviewPoint($id);
            else if ($aplicantType == 'job-aplicant')
                $data = $this->jobAplicantService->showInterviewPoint($id);
            else throw new \Exception ('Invalid route parameter');
            
            if ($data == null) {
                throw new \Exception('interview point tidak ditemukan',404);
            }
            return response()->json([
                'success' => true,
                'status_code' => 200,
                'data' => $data,
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
     * Update the specified resource in storage.
     */
    public function update($aplicantType, UpdateInterviewPointRequest $request, string $id)
    {
        try {
            if ($aplicantType == 'traineeship')
                $this->internshipService->updateInterviewPoint($id, $request->validated());
            else if ($aplicantType == 'job-aplicant')
                $this->jobAplicantService->updateInterviewPoint($id, $request->validated());
            else throw new \Exception ('Invalid route parameter');
            
            return response()->json([
                'success' => true,
                'status_code' => 200,
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
        //
    }
}
