<?php

namespace App\Http\Controllers;

use App\Interfaces\DivisionRepositoryInterface;
use App\Http\Requests\Division\StoreDivisionRequest;
use App\Http\Requests\Division\UpdateDivisionRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        try {
            $data = $this->divisionRepositoryInterface->getAll();
            if (count($data)) {
                return response()->json([
                    'status' => 'success',
                    'data' => $data,
                    'status_code' => 200,
                ]);
            } else throw new ModelNotFoundException('data tidak ditemukan');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
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
                'status_code' => 200,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'input' => $request->validated(),
                'status_code' => 500,
            ]);
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
            return response()->json([
                'status' => 'success',
                'data' => $data,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }

    public function getEmployee($id)
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => $this->divisionRepositoryInterface->getEmployee($id)->employee,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
                'status_code' => 500,
            ]);
        }
    }

    public function getEmployeeArchive($id)
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => $this->divisionRepositoryInterface->getEmployeeArchive($id)->employeeArchive,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'status_code' => $e->getCode() ?? 404,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDivisionRequest $request, string $division)
    {
        try {
            $this->divisionRepositoryInterface->update($division, $request->validated());

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() == "" ? 'data tidak ditemukan':$e->getMessage(),
                'status_code' => 500,
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
                'status_code' => 200,
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() == "" ? 'data tidak ditemukan':$e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }
}
