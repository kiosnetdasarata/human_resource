<?php

namespace App\Http\Controllers\Internship;

use App\Services\InternshipService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Traineeship\StoreTraineeshipRequest;
use App\Http\Requests\Traineeship\UpdateTraineeshipRequest;
use App\Models\Traineeship;

class TraineeshipController extends Controller
{
    public function __construct(private InternshipService $traineeship) 
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
                'data' => $this->traineeship->getAllTraineeship(),
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

    public function getByJobVacancy($jobVacancyId)
    {
        try {
            $data = $this->traineeship->findByVacancy($jobVacancyId);
            if (!count($data)) throw new ModelNotFoundException('data tidak ditemukan', 404);
            else return response()->json([
                'status' => 'success',
                'data' => $data,
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTraineeshipRequest $request)
    {
        try {
            $this->traineeship->createTraineeship($request->validated());

            return response()->json([
                'success' => true,
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
     * Display the specified resource.
     */
    public function show($slug)
    {
        try {
            if ((int) $slug) {
                $traineeship = $this->traineeship->findTraineeship($slug, true);
            } else {
                $traineeship = $this->traineeship->findTraineeshipSlug($slug);
            }
            if (!$traineeship) throw new ModelNotFoundException('Data tidak ditemukan',404);
            return response()->json([
                'success' => true,
                'data' => $traineeship,
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() == '' ? 'Unknown Error' : $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTraineeshipRequest $request, string $id)
    {
        try {
            $data = $request->validated();
            if (isset($data['status_tahap'])) $this->traineeship->updateStatus($id, $data['status_tahap']);
            else $this->traineeship->updateTraineeship($id, $request->validated());

            return response()->json([
                'success' => true,
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
}
