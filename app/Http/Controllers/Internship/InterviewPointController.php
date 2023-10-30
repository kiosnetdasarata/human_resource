<?php

namespace App\Http\Controllers\Internship;

use Illuminate\Http\Request;
use App\Services\InternshipService;
use App\Http\Controllers\Controller;
use App\Http\Requests\InterviewPoint\StoreInterviewPointRequest;
use App\Http\Requests\InterviewPoint\UpdateInterviewPointRequest;

class InterviewPointController extends Controller
{
    public function __construct(private InternshipService $internshipService) {
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store($idTraineeship, StoreInterviewPointRequest $request)
    {
        try {
            $this->internshipService->addInterviewPoint($idTraineeship,$request->validated());

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage() == null ? 'data Traineeship tidak ditemukan' : $e->getMessage(),
                'input' => $request->validated(),
                'status_code' => 500,
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $data = $this->internshipService->showInterviewPoint($id);
            if ($data == null) {
                $data = 'traineeship belum memiliki interview point';
            }
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
     * Update the specified resource in storage.
     */
    public function update(UpdateInterviewPointRequest $request, string $id)
    {
        try {
            $this->internshipService->updateInterviewPoint($id, $request->validated());
            return response()->json([
                'success' => true,
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
