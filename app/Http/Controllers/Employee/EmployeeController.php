<?php

namespace App\Http\Controllers\Employee;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Services\EmployeeService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Requests\Employee\FirstFormEmployeeRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Employee\SecondFormEmployeeRequest;

class EmployeeController extends Controller
{
    public function __construct(
        private EmployeeService $employeeService,
        private ResponseHelper $response)
    { }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    { 
        try {
            $this->authorize('view-any', Employee::class);
            $data = $this->employeeService->getAllEmployeePersonal();
            if (!count($data)) {
                throw new ModelNotFoundException();
            }

            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }
    public function getArchive()
    {
        try {
            $data = $this->employeeService->getEmployeeArchive();
            if (!count($data)) {
                throw new ModelNotFoundException();
            }
            
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeFormOne(FirstFormEmployeeRequest $request)
    {
        try {            
            $this->employeeService->firstForm($request->validated());
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }

    public function storeFormTwo($uuid, SecondFormEmployeeRequest $request)
    {
        try {
            $this->employeeService->secondForm($uuid, $request->validated());
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $data = $this->employeeService->findEmployeePersonal($id);
            if (!$data) { 
                throw new ModelNotFoundException('Data not found');
            } else $this->authorize('view', $data);

            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, $uuid)
    {
        try {
            $this->employeeService->updateEmployee($uuid, $request->validated());
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $uuid)
    {
        try {            
            $data = Validator::make($request->all(), ['status_terminate' => 'required']);
            if ($data->fails()) throw new ValidationException($data->errors()->first());

            $this->authorize('delete', Employee::class);
            $this->employeeService->deleteEmployeePersonal($data->validated(), $uuid);
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e, $data->validated());
        }
    }
}