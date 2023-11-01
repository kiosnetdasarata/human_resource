<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sales\StoreSalesRequest;
use App\Http\Requests\Sales\UpdateSalesRequest;
use App\Interfaces\SalesRepositoryInterface;

class SalesController extends Controller
{
    public function __construct(private SalesRepositoryInterface $sales)
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
                'data' => $this->sales->getAll(),
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'status_code' => $e->getCode() == null ? 500 : $e->getCode(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSalesRequest $request)
    {
        abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->sales->find($id),
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSalesRequest $request, $id)
    {
        try {
            
            $this->sales->update($id,$request->validated());
            
            return response()->json([
                'success' => true,
                'status' => 'success',
                'status_code' => 200,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
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
            $this->sales->delete($id);
            
            return response()->json([
                'success' => true,
                'status_code' => 200,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }
}
