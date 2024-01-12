<?php

namespace App\Http\Controllers;

use App\Interfaces\LevelRepositoryInterface;
use App\Http\Requests\Level\StoreLevelRequest;
use App\Http\Requests\Level\UpdateLevelRequest;

class LevelController extends Controller
{
    public function __construct(private LevelRepositoryInterface $levelRepositoryInterface)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => $this->levelRepositoryInterface->getAll(),
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'status_code' => 500
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLevelRequest $request)
    {
        try {
            $this->levelRepositoryInterface->create($request->validated());

            return response()->json([
                'status' => 'success',
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'input' => $request->validated(),
                'status_code' => 500
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => $this->levelRepositoryInterface->find($id),
                'status_code' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'status_code' => 500
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLevelRequest $request, $id)
    {
        try {
            $this->levelRepositoryInterface->update($this->levelRepositoryInterface->find($id),$request->validated());
            
            return response()->json([
                'status' => 'success',
                'status_code' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'input' => $request->validated(),
                'status_code' => 500
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->levelRepositoryInterface->delete($this->levelRepositoryInterface->find($id));
            
            return response()->json([
                'status' => 'success',
                'status_code' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'status_code' => 500
            ]);
        }
    }
}
