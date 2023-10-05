<?php

namespace App\Http\Controllers\Internship;

use App\Http\Controllers\Controller;
use App\Http\Requests\Traineeship\StoreTraineeshipRequest;
use App\Http\Requests\Traineeship\UpdateTraineeshipRequest;
use App\Services\InternshipService;

class TraineeshipController extends Controller
{
    public function __construct(private InternshipService $traineeship) 
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->traineeship->getAllTraineeship(),
            'status_code' => 200,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTraineeshipRequest $request)
    {
        try {
            $this->traineeship->createTraineeship($request->validated());

            return response()->json([
                'status' => 'success',
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
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        try {
            $traineeship = $this->traineeship->findTraineeship($slug);

            return response()->json([
                'status' => 'success',
                'data' => $traineeship,
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
    public function update(UpdateTraineeshipRequest $request, string $slug)
    {
        try {
            $this->traineeship->updateTraineeship($slug, $request->validated());

            return response()->json([
                'status' => 'success',
                'status_code' => 200
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
            $this->traineeship->deleteTraineeship($uuid);
            
            return response()->json([
                'status' => 'success',
                'status_code' => 200,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }
}
