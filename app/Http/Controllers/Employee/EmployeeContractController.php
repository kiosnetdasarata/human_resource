<?php

namespace App\Http\Controllers\Employee;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreContractRequest;
use App\Http\Requests\Employee\UpdateContractRequest;
use App\Services\EmployeeContractService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmployeeContractController extends Controller
{
    public function __construct(
        private EmployeeContractService $service,
        private ResponseHelper $response
        ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        try {
            $data = $this->service->get($id);
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($id, StoreContractRequest $request)
    {
        try {
            $this->service->store($id, $request->validated());
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $data = $this->service->find($id);
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContractRequest $request, string $id)
    {
        try {
            $this->service->update($id, $request);
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    // {
    //     try {
    //         $this->service->deleteEmployeeContract($id);
    //         return $this->response->success();
    //     } catch (\Exception $e) {
    //         return $this->response->error($e);
    //     }
    // }
}
