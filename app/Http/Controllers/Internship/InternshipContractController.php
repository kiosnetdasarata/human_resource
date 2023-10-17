<?php

namespace App\Http\Controllers\Internship;

use Illuminate\Http\Request;
use App\Services\InternshipService;
use App\Http\Controllers\Controller;
use App\Http\Requests\InternshipContract\StoreIntershipContractRequest;
use App\Http\Requests\InternshipContract\UpdateIntershipContractRequest;

class InternshipContractController extends Controller
{
    public function __construct(private InternshipService $internshipContract) 
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIntershipContractRequest $request)
    {
        try {
            $this->internshipContract->createinternshipContract($request->validated());

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
            $internshipContract = $this->internshipContract->findinternshipContract($slug);

            return response()->json([
                'status' => 'success',
                'data' => $internshipContract,
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
    public function update(UpdateIntershipContractRequest $request, string $slug)
    {
        // try {
        //     $this->internshipContract->updateInternshipContract($slug, $request->validated());

        //     return response()->json([
        //         'status' => 'success',
        //         'status_code' => 200
        //     ]);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => $e->getMessage(),
        //         'input' => $request->validated(),
        //         'status_code' => 500,
        //     ]);
        // }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        try {
            $this->internshipContract->deleteinternshipContract($uuid);
            
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
