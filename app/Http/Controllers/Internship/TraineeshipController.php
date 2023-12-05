<?php

namespace App\Http\Controllers\Internship;

use App\Http\Controllers\Controller;
use App\Http\Requests\Traineeship\StoreTraineeshipRequest;
use App\Http\Requests\Traineeship\UpdateTraineeshipRequest;
use App\Services\InternshipService;
use Ramsey\Uuid\Type\Integer;

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
        try {
            return response()->json([
                'success' => true,
                'data' => $this->traineeship->getAllTraineeship(),
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTraineeshipRequest $request)
    {
        try {
            $this->traineeship->createTraineeship($request->validated());

            return response()->json([
                'success' => true,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'input' => $request->validated(),
                'status_code' => 500,
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        try {
            if (((int) $slug) != 0)
                $traineeship = $this->traineeship->findTraineeship($slug);
            else $traineeship = $this->traineeship->findTraineeshipSlug($slug)->first();
            return response()->json([
                'success' => true,
                'data' => $traineeship,
                'status_code' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() == '' ? 'Data tidak ditemukan' : $e->getMessage(),
                'status_code' => 500
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTraineeshipRequest $request, string $id)
    {
        try {
            $this->traineeship->updateTraineeship($id, $request->validated());

            return response()->json([
                'success' => true,
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
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

    }

    
}
