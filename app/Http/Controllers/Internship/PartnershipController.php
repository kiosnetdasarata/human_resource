<?php

namespace App\Http\Controllers\Internship;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\PartnershipService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Partnership\StorePartnershipRequest;
use App\Http\Requests\Partnership\UpdatePartnershipRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PartnershipController extends Controller
{
    public function __construct(
        private PartnershipService $partnership,
        private ResponseHelper $response
    )
    {
        //
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = $this->partnership->get();
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePartnershipRequest $request)
    {
        try {
            $this->partnership->create($request->validated());
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }

    public function findInternship($id, $status)
    {
        try {
            $data = $this->partnership->getInternship($id, $status);
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    public function findInternshipArchive($id, $status)
    {
        try {
            $data = $this->partnership->getInternshipArchive($id, $status);
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        try {
            $data = $this->partnership->find($slug);
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, UpdatePartnershipRequest $request)
    {
        try {
            $this->partnership->update($id,$request->validated());
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        try {
            $this->partnership->delete($uuid);            
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }
}
