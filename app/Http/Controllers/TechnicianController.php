<?php

namespace App\Http\Controllers;

use App\Interfaces\TechnicianRepositoryInterface;
use App\Http\Requests\Technician\StoreTechnicianRequest;
use App\Http\Requests\Technician\UpdateTechnicianRequest;

class TechnicianController extends Controller
{
    public function __construct(private TechnicianRepositoryInterface $technicianRepositoryInterface)
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->technicianRepositoryInterface->getAll(),
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTechnicianRequest $request)
    {
        try {
            $this->technicianRepositoryInterface->create($request->validated());

            return response()->json([
                'success' => true,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'input' => $request->validated(),
                'status_code' => 500,
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $technician = $this->technicianRepositoryInterface->find($id);

            return response()->json([
                'success' => true,
                'data' => $technician,
                'status_code' => 200,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTechnicianRequest $request, $id)
    {
        try {
            $this->technicianRepositoryInterface->update($id,$request->validated());
            
            return response()->json([
                'success' => true,
                'status_code' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'input' => $request->validated(),
                'status_code' => 500,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->technicianRepositoryInterface->delete($this->technicianRepositoryInterface->find($id));
            
            return response()->json([
                'success' => true,
                'status_code' => 200,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }
}
