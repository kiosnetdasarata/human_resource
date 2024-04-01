<?php

namespace App\Http\Controllers\Internship;

use App\Helpers\ResponseHelper;
use App\Services\InternshipService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Internship\StoreInternshipRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Internship\UpdateInternshipRequest;

class InternshipController extends Controller
{
    public function __construct(
        private InternshipService $internshipService,
        private ResponseHelper $response
        )
    { }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = $this->internshipService->get();
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($idTraineenship, StoreInternshipRequest $request)
    {
        try {
            $this->internshipService->create($idTraineenship, $request->validated());
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        try {
            $data = $this->internshipService->find($uuid);
            if (!$data) throw new ModelNotFoundException();
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInternshipRequest $request, string $uuid)
    {
        try {
            $data = $this->internshipService->update($uuid, $request->validated());
            return $this->response->success($data);
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
            $this->internshipService->delete($uuid);            
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }
}
