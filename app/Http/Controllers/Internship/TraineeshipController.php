<?php

namespace App\Http\Controllers\Internship;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\TraineeshipService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Traineeship\StoreTraineeshipRequest;
use App\Http\Requests\Traineeship\UpdateTraineeshipRequest;

class TraineeshipController extends Controller
{
    public function __construct(
        private TraineeshipService $traineeship,
        private ResponseHelper $response
    ) { }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = $this->traineeship->get();
            if (!count($data)) throw new ModelNotFoundException();
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    public function getByJobVacancy($jobVacancyId)
    {
        try {
            $data = $this->traineeship->findByVacancy($jobVacancyId);
            if (!count($data)) throw new ModelNotFoundException();
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTraineeshipRequest $request)
    {
        try {
            $this->traineeship->create($request->validated());

            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        try {
            if ((int) $slug) {
                $data = $this->traineeship->find($slug, true);
            } else {
                $data = $this->traineeship->findTraineeshipSlug($slug);
            }
            if (!$data) throw new ModelNotFoundException('Data tidak ditemukan',404);
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
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
            else $this->traineeship->update($id, $request->validated());

            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }
}