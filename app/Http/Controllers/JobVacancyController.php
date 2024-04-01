<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\JobVacancy\StoreJobVacancyRequest;
use App\Http\Requests\JobVacancy\UpdateJobVacancyRequest;
use App\Services\JobVacancyService;

class JobVacancyController extends Controller
{
    public function __construct(
        private JobVacancyService $jobVacancy,
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
        try{
            $data = $this->jobVacancy->getAll();
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }
    public function role() {
        try{
            $data = $this->jobVacancy->getRole();
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobVacancyRequest $request)
    {
        try {
            $this->jobVacancy->create($request->validated());
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
        try{
            $data = $this->jobVacancy->find($id);
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobVacancyRequest $request, string $id)
    {
        try {            
            $this->jobVacancy->update($id, $request->validated());
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
            $this->jobVacancy->delete($id);
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }
}
