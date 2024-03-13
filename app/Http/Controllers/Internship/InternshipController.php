<?php

namespace App\Http\Controllers\Internship;

use App\Http\Controllers\Controller;
use App\Http\Requests\Internship\StoreInternshipRequest;
use App\Http\Requests\Internship\UpdateInternshipRequest;
use App\Services\InternshipService;

class InternshipController extends Controller
{
    public function __construct(private InternshipService $internshipService) 
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->internshipService->getAllInternship(),
            'status_code' => 200,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($idTraineenship, StoreInternshipRequest $request)
    {
        try {
            $this->internshipService->createInternship($idTraineenship, $request->validated());

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'input' => $request->validated(),
                'trace' => $e->getTrace(),
                'status_code' => 500,
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        try {
            $internship = $this->internshipService->findInternship($uuid);

            return response()->json([
                'status' => 'success',
                'data' => $internship,
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
    public function update(UpdateInternshipRequest $request, string $uuid)
    {
        try {
            $data = $this->internshipService->updateInternship($uuid, $request->validated());

            return response()->json([
                'status' => 'success',
                'data' => $data,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'input' => $request->validated(),
                'status_code' => 500,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        try {
            $this->internshipService->deleteInternship($uuid);
            
            return response()->json([
                'status' => 'success',
                'status_code' => 200,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'line' => $e->getTrace(),
                'status_code' => 500,
            ]);
        }
    }
}
