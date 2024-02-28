<?php

namespace App\Http\Controllers\Internship;

use App\Http\Controllers\Controller;
use App\Services\PartnershipService;
use App\Http\Requests\Partnership\StoreFilePartnershipRequest;
use App\Http\Requests\Partnership\UpdateFilePartnershipRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            if(!count($data)) throw new ModelNotFoundException();
            return response()->json([
                'status' => 'success',
                'data' => $data,
                'status_code' => 200,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage() ?? 'data not found',
                'status_code' => 404,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
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
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage() ?? 'data not found',
                'input' => $request->validated(),
                'status_code' => 404,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
                'input' => $request->validated(),
                'status_code' => 500,
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
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage() ?? 'data not found',
                'status_code' => 404,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'status_code' => 500,
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
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage() ?? 'data not found',
                'input' => $request->validated(),
                'status_code' => 404,
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

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage() ?? 'data not found',
                'status_code' => 404,
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
