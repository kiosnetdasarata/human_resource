<?php

namespace App\Http\Controllers;

use LogicException;
use App\Helpers\ResponseHelper;
use App\Interfaces\LevelRepositoryInterface;
use App\Http\Requests\Level\StoreLevelRequest;
use App\Http\Requests\Level\UpdateLevelRequest;

class LevelController extends Controller
{
    public function __construct(
        private LevelRepositoryInterface $level,
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
            $data = $this->level->getAll();
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
            $this->level->create($request->validated());
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
            $data = $this->level->find($id);
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
            $old = $this->level->find($id);
            $this->level->update($old, $request->validated());
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
            $data = $this->level->find($id);

            if ($data->employee->first()) {
                throw new LogicException('level ini masih memiliki karyawan aktif.');
            }
            $this->level->delete($data);
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }
}
