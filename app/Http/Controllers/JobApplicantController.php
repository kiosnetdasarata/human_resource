<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Services\JobApplicantService;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\JobApplicant\StoreJobApplicantRequest;
use App\Http\Requests\JobVacancy\UpdateJobVacancyRequest;

class JobApplicantController extends Controller
{
    public function __construct(
        private JobApplicantService $jobApplicantService,
        private ResponseHelper $response
        )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = $this->jobApplicantService->get();
            if (!count($data)) throw new ModelNotFoundException();
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobApplicantRequest $request)
    {
        try {
            $this->jobApplicantService->create($request);
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }

    public function find($status)
    {
        try {
            $data = $this->jobApplicantService->search('status_tahap', $status);
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($slug) 
    {
        try {
            if ((int) $slug) {
                $data = $this->jobApplicantService->find($slug);
            } else {
                $data = $this->jobApplicantService->findSlug($slug)->firstOrFail();
                if ($slug != $data->slug) throw new ModelNotFoundException();
            } 
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    public function getByJobVacancy($id)
    {
        try {
            $data = $this->jobApplicantService->getByVacancy($id);
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobVacancyRequest $request, string $id)
    {
        try {
            $this->jobApplicantService->update($id, $request);
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }

    public function changeStatus($id, Request $request) 
    {
        try {
            $data = Validator::make($request->all(), ['status_tahap' => 'required|in:FU,Assesment,Tolak']);
            if ($data->fails()) throw new ValidationException($data->errors()->first());
            $this->jobApplicantService->updateStatus($id, $data->validate());
            return $this->response->success();
        } catch (\Exception $e) {
            return $this->response->error($e, $request->validated());
        }
    }
}
