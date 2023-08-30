<?php

namespace App\Http\Controllers;

use App\Models\JobTitle;
use Illuminate\Http\Request;
use App\Http\Requests\JobTitle\JobTitleRequest;
use App\Interfaces\JobTitleRepositoryInterface;

class JobTitleController extends Controller
{
    public function __construct(private JobTitleRepositoryInterface $jobTitleRepositoryInterface)
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index($division = null)
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => $this->jobTitleRepositoryInterface->getAll($division),
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
    public function store(JobTitleRequest $request)
    {
        try {
            $this->jobTitleRepositoryInterface->create($request->validated());

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
            return response()->json([
                'status' => 'success',
                'data' => $this->jobTitleRepositoryInterface->find($id),
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
    public function update(JobTitleRequest $request, $id)
    {
        try {
            $this->jobTitleRepositoryInterface->update($id,$request->validated());
            
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
            $this->jobTitleRepositoryInterface->delete($this->jobTitleRepositoryInterface->find($id));
            
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
