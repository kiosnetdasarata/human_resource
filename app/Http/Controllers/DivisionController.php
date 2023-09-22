<?php

namespace App\Http\Controllers;

use App\Interfaces\DivisionRepositoryInterface;
use App\Http\Requests\Division\StoreDivisionRequest;
use App\Http\Requests\Division\UpdateDivisionRequest;

class DivisionController extends Controller
{
    public function __construct(private DivisionRepositoryInterface $divisionRepositoryInterface)
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->divisionRepositoryInterface->getAll()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDivisionRequest $request)
    {
        try {
            $this->divisionRepositoryInterface->create($request->validated());

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
    public function show(string $slug)
    {
        // try {
        //     return response()->json([
        //         'status' => 'success',
        //         'data' => $this->divisionRepositoryInterface->find($slug),
        //     ]);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => $e->getMessage()
        //     ]);
        // }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDivisionRequest $request, string $slug)
    {
        try {
            $this->divisionRepositoryInterface->update($this->divisionRepositoryInterface->find($slug), $request->validated());

            return response()->json([
                'status' => 'success',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
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

            return response()->json([
                'status' => 'success',
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
