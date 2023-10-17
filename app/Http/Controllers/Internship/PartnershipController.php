<?php

namespace App\Http\Controllers\Internship;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parnership\StoreParnershipRequest;
use App\Http\Requests\Parnership\UpdateParnershipRequest;
use App\Services\PartnershipService;
use Illuminate\Http\Request;

class PartnershipController extends Controller
{
    public function __construct(private PartnershipService $partnership) 
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->partnership->getAllPartnership(),
            'status_code' => 200,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParnershipRequest $request)
    {
        try {
            $this->partnership->createPartnership($request->validated());

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
            $partnership = $this->partnership->getPartnership($slug);

            return response()->json([
                'status' => 'success',
                'data' => $partnership,
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
    public function update(UpdateParnershipRequest $request, string $slug)
    {
        try {
            $this->partnership->updatePartnership($slug, $request->validated());

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
            $this->partnership->deletePartnership($uuid);
            
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
