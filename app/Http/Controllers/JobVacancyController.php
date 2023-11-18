<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobVacancy\StoreJobVacancyRequest;
use Illuminate\Http\Request;
use App\Interfaces\JobVacancyRepositoryInterface;

class JobVacancyController extends Controller
{
    public function __construct(private JobVacancyRepositoryInterface $jobVacancy) 
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            return response()->json([
                'status' => 'success',
                'data' => $this->jobVacancy->getAll(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
                'status_code' => $e->getCode(),
            ]);
        }
    }
    public function role() {
        try{
            return response()->json([
                'status' => 'success',
                'data' => $this->jobVacancy->getRole(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
                'status_code' => $e->getCode(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobVacancyRequest $request)
    {
        try {
            $this->jobVacancy->create($request->all());

            return response()->json([
                'success' => true,
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => $e->getCode() == 0 ? 500 : $e->getCode(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            return response()->json([
                'status' => 'success',
                'data' => $this->jobVacancy->find($id),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
                'status_code' => $e->getCode(),
                
                'route' => $e->getTrace(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
