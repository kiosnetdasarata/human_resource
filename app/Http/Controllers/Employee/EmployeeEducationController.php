<?php

namespace App\Http\Controllers\Employee;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreEducationRequest;
use App\Http\Requests\Employee\UpdateEducationRequest;
use App\Services\EmployeeEducationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmployeeEducationController extends Controller
{
    public function __construct(
        private EmployeeEducationService $employeeService,
        private ResponseHelper $response
        )
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        try {
            $data = $this->employeeService->get($id);
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($uuid, StoreEducationRequest $request)
    {
        try {
            $this->employeeService->storeData($uuid, $request);
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
            $data = $this->employeeService->find($id);
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEducationRequest $request, string $id)
    {
        try {
            $this->employeeService->update($id, $request->all());
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->employeeService->delete($id);
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }
}
