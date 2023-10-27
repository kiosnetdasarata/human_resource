<?php

namespace App\Http\Controllers\Internship;

use Illuminate\Http\Request;
use App\Services\InternshipService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Internship\StoreInterviewPointRequest;

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
    // public function show(string $id)
    // {
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, string $id)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(string $id)
    // {
    //     //
    // }
}
