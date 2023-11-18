<?php

namespace App\Http\Controllers;

use App\Interfaces\RoleRepositoryInterface;
use App\Http\Requests\Role\StoreRoleRequest;
use Illuminate\Support\ItemNotFoundException;

class RoleController extends Controller
{
    public function __construct(private RoleRepositoryInterface $roleRepositoryInterface)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index($division = null)
    {
        try {
            $data = $this->roleRepositoryInterface->getAll($division);
            if (count($data) <= 0) {
                throw new ItemNotFoundException('data tidak ditemukan');
            }
            return response()->json([
                'status' => 'success',
                'data' =>$data,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'status_code' => $e->getCode() == 0 ? 404 : $e->getCode(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        try {
            $this->roleRepositoryInterface->create($request->validated());

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'input' => $request->validated(),
                'status_code' => $e->getCode() == 0 ? 500 : $e->getCode(),
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
                'data' => $this->roleRepositoryInterface->find($id),
                'status_code' => 200,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'status_code' => $e->getCode() == 0 ? 404 : $e->getCode(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(UpdateJobTitleRequest $request, $id)
    // {
    //     try {
    //         $this->roleRepositoryInterface->update($id,$request->validated());
            
    //         return response()->json([
    //             'status' => 'success'
    // 'status_code' =>200,
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $e->getMessage(),
    //             'input' => $request->validated(),
    // 'status_code' => $e->getCode() == 0 ? 500 : $e->getCode(),
    //         ], );
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->roleRepositoryInterface->delete($this->roleRepositoryInterface->find($id));
            
            return response()->json([
                'status' => 'success',
                'status_code' => 200,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'status_code' => $e->getCode() == 0 ? 404 : $e->getCode(),
            ]);
        }
    }
}
