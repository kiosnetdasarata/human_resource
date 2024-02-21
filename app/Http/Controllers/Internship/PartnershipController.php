<?php

namespace App\Http\Controllers\Internship;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partnership\StorePartnershipRequest;
use App\Http\Requests\Partnership\UpdatePartnershipRequest;
use App\Services\PartnershipService;
use InvalidArgumentException;

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
        try {
            return response()->json([
                'success' => true,
                'data' => $this->partnership->getAllPartnership(),
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePartnershipRequest $request)
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

    public function findInternship($id, $status)
    {
        try {
            if($status != 'magang' && $status != 'internship') throw new InvalidArgumentException('status tidak valid', 422);
            $partnership = $this->partnership->getInternship($id, $status);

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

    public function findInternshipArchive($id, $status)
    {
        try {
            if($status != 'magang' && $status != 'internship') throw new InvalidArgumentException('status tidak valid', 422);
            $partnership = $this->partnership->getInternshipArchive($id, $status);
            
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
    public function update($id, UpdatePartnershipRequest $request)
    {
        try {
            $this->partnership->updatePartnership($id,$request->validated());

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
