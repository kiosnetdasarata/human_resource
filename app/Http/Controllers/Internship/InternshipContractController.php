<?php

namespace App\Http\Controllers\Internship;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\InternshipContract\StoreIntershipContractRequest;
use App\Http\Requests\InternshipContract\UpdateIntershipContractRequest;
use App\Services\InternshipContractService;

class InternshipContractController extends Controller
{
    public function __construct(
        private InternshipContractService $internshipContract,
        private ResponseHelper $response
    )
    {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function index($idInternship)
    {
        try {
            $data = $this->internshipContract->get($idInternship);
            if (!count($data)) throw new ModelNotFoundException();
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($idInternship, StoreIntershipContractRequest $request)
    {
        try {
            $this->internshipContract->create($idInternship,$request->validated());
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $data = $this->internshipContract->find($id);
            if (!$data) throw new ModelNotFoundException();
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIntershipContractRequest $request, string $id)
    {
        try {
            $this->internshipContract->update($id, $request->validated());
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $uuid)
    // {
    //     try {
    //         $this->internshipContract->delete($uuid);
            
    //         return $this->response->success();
    //     } catch (\Exception $e) {
    //         return $this->response->error($e);
    //     }
    // }
}