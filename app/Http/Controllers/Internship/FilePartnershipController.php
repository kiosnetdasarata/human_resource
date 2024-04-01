<?php

namespace App\Http\Controllers\Internship;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\PartnershipService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Partnership\StoreFilePartnershipRequest;
use App\Http\Requests\Partnership\UpdateFilePartnershipRequest;

class FilePartnershipController extends Controller
{
    public function __construct(
        private PartnershipService $filePartnership,
        private ResponseHelper $response
    )
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index($IdMitra)
    {
        try {
            $data = $this->filePartnership->getFile($IdMitra);
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($mitraId, StoreFilePartnershipRequest $request)
    {
        try {
            $this->filePartnership->createFile($mitraId, $request->validated());

            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $mitraId)
    {
        try {
            $data = $this->filePartnership->findFile($mitraId);
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFilePartnershipRequest $request, string $mitraId,)
    {
        try {
            $this->filePartnership->updateFile($mitraId, $request->validated());

            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $mitraId, string $type)
    // {
    //     try {
    //         $this->filePartnership->delete($uuid);
            
    //         return response()->json([
    //             'status' => 'success',
    //             'status_code' => 200,
    //         ]);

    //     } catch (ModelNotFoundException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'error' => $e->getMessage() ?? 'data not found',
    //             'status_code' => 404,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $e->getMessage(),
    //             'status_code' => 500,
    //         ]);
    //     }
    // }
}
