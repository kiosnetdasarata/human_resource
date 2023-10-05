<?php

namespace App\Http\Controllers\Internship;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PartnershipService;
use App\Http\Requests\ParnershipContract\StoreParnershipContractRequest;
use App\Http\Requests\ParnershipContract\UpdateParnershipContractRequest;

class FilePartnershipController extends Controller
{
    public function __construct(private PartnershipService $filePartnership) 
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return response()->json([
        //     'status' => 'success',
        //     'data' => $this->partnershipContract->getAllPartnershipContract(),
        //     'status_code' => 200,
        // ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParnershipContractRequest $request)
    {
        try {
            $this->filePartnership->createFilePartnership($request->validated());

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
            $partnershipContract = $this->filePartnership->getFilePartnership($slug);

            return response()->json([
                'status' => 'success',
                'data' => $partnershipContract,
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
    public function update(UpdateParnershipContractRequest $request, string $slug)
    {
        try {
            $this->filePartnership->updateFilePartnership($slug, $request->validated());

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
            // $this->filePartnership->delete($uuid);
            
            // return response()->json([
            //     'status' => 'success',
            //     'status_code' => 200,
            // ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }
}
