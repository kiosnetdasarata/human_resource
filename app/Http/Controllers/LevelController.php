<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Interfaces\LevelRepositoryInterface;
use App\Http\Requests\Level\StoreLevelRequest;
use App\Http\Requests\Level\UpdateLevelRequest;

class LevelController extends Controller
{
    public function __construct(
        private LevelRepositoryInterface $levelRepositoryInterface,
        private ResponseHelper $response,
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
            $data = $this->levelRepositoryInterface->getAll();
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLevelRequest $request)
    {
        try {
            $this->levelRepositoryInterface->create($request->validated());
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
            $data = $this->levelRepositoryInterface->find($id);
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLevelRequest $request, $id)
    {
        try {
            $this->levelRepositoryInterface->update($this->levelRepositoryInterface->find($id),$request->validated());
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
            $this->levelRepositoryInterface->delete($this->levelRepositoryInterface->find($id));
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }
}
