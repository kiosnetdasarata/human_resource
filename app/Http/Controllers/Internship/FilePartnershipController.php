<?php

namespace App\Http\Controllers\Internship;

use App\Http\Controllers\Controller;
use App\Services\PartnershipService;
use App\Http\Requests\Partnership\StoreFilePartnershipRequest;
use App\Http\Requests\Partnership\UpdateFilePartnershipRequest;

class FilePartnershipController extends Controller
{
    public function __construct(private PartnershipService $filePartnership) 
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index($IdMitra)
    {
        try {
            $data = $this->filePartnership->getFilePartnerships($IdMitra);
            
            return response()->json([
                'status' => 'success',
                'data' => $data,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
                'status_code' => 500,
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($mitraId, StoreFilePartnershipRequest $request)
    {
        try {
            $this->filePartnership->createFilePartnership($mitraId, $request->validated());

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'input' => $request->validated(),
                'status_code' => $e->getCode() == null ? 500 : $e->getCode(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $mitraId)
    {
        try {
            $data = $this->filePartnership->getFilePartnership($mitraId);
            
            return response()->json([
                'status' => 'success',
                'data' => $data,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),                
                'trace' => $e->getTrace(),
                'status_code' => $e->getCode() == null ? 500 : $e->getCode(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFilePartnershipRequest $request, string $mitraId,)
    {
        try {
            $this->filePartnership->updateFilePartnership($mitraId, $request->validated());

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
    public function destroy(string $mitraId, string $type)
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
