<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Interfaces\DivisionRepositoryInterface;
use App\Http\Requests\Division\StoreDivisionRequest;
use App\Http\Requests\Division\UpdateDivisionRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DivisionController extends Controller
{
    public function __construct(
        private DivisionRepositoryInterface $divisionRepositoryInterface,
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
            $data = $this->divisionRepositoryInterface->getAll();
            if (!count($data)) throw new ModelNotFoundException('data tidak ditemukan');
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
            $this->divisionRepositoryInterface->create($request->validated());
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
                $data = $this->divisionRepositoryInterface->find($id);
            } else {
                $data = $this->divisionRepositoryInterface->findSlug($id);
            }

            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    public function getEmployee($id)
    {
        try {
            $data = $this->divisionRepositoryInterface->getEmployee($id);

            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    public function getEmployeeArchive($id)
    {
        try {
            $data = $this->divisionRepositoryInterface->getEmployeeArchive($id);
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
            $this->divisionRepositoryInterface->update($division, $request->validated());

            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        try {
            $data = $this->divisionRepositoryInterface->find($slug);
            $this->divisionRepositoryInterface->delete($data);

            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }
}
