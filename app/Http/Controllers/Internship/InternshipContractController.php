<?php

namespace App\Http\Controllers\Internship;

use Illuminate\Http\Request;
use App\Services\InternshipService;
use App\Http\Controllers\Controller;
use App\Http\Requests\InternshipContract\StoreIntershipContractRequest;
use App\Http\Requests\InternshipContract\UpdateIntershipContractRequest;

class InternshipContractController extends Controller
{
    public function __construct(private InternshipService $internshipService) 
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index($idInternship)
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => $this->internshipService->getInternshipContracts($idInternship),
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
                'status_code' => $e->getCode(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($idInternship, StoreIntershipContractRequest $request)
    {
        try {
            $this->internshipService->createinternshipContract($idInternship,$request->validated());

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
    public function show($id)
    {
        try {
            $internshipContract = $this->internshipService->getinternshipContract($id);

            return response()->json([
                'status' => 'success',
                'data' => $internshipContract,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIntershipContractRequest $request, string $id)
    {
        try {
            $this->internshipService->updateInternshipContract($id, $request->validated());

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
            $this->internshipService->deleteinternshipContract($uuid);
            
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
