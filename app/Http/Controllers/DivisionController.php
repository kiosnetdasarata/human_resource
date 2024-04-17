<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\Division\StoreDivisionRequest;
use App\Http\Requests\Division\UpdateDivisionRequest;
use App\Interfaces\Employee\EmployeeRepositoryInterface;
use App\Services\DivisionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DivisionController extends Controller
{
    public function __construct(
        private DivisionService $division,
        private EmployeeRepositoryInterface $employee,
        private ResponseHelper $response
        )
    {

    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = $this->division->get();
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDivisionRequest $request)
    {
        try {
            $this->division->create($request->validated());
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
            if ((int) $id) {
                $data = $this->division->find($id);
            } else {
                $data = $this->division->findSlug($id);
            }

            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    public function getEmployeeArchive($id)
    {
        try {
            $data = $this->employee->getByDivision($id);
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDivisionRequest $request, string $division)
    {
        try {
            $this->division->update($division, $request->validated());

            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $slug)
    // {
    //     try {
    //         $data = $this->division->find($slug);
    //         $this->division->delete($data);

    //         return $this->response->success();
    //     } catch (\Exception $e) {
    //         return $this->response->error($e);
    //     }
    // }
}
