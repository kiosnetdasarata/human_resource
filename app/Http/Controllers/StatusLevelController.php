<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\StatusLevelRepositoryInterface;
use App\Http\Requests\StatusLevel\StatusLevelRequest;

class StatusLevelController extends Controller
{
    public function __construct(private StatusLevelRepositoryInterface $statusLevelRepositoryInterface)
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
                'data' => $this->statusLevelRepositoryInterface->getAll(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StatusLevelRequest $request)
    {
        try {
            $this->statusLevelRepositoryInterface->create($request->validated());

            return response()->json([
                'status' => 'success',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'input' => $request->validated(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $statusLevel = $this->statusLevelRepositoryInterface->find($id);

            return response()->json([
                'status' => 'success',
                'data' => $statusLevel,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StatusLevelRequest $request, $id)
    {
        try {
            $this->statusLevelRepositoryInterface->update($id,$request->validated());
            
            return response()->json([
                'status' => 'success'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'input' => $request->validated()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->statusLevelRepositoryInterface->delete($this->statusLevelRepositoryInterface->find($id));
            
            return response()->json([
                'status' => 'success',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}