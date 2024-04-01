<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Interfaces\JobVacancyRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Interfaces\ArchiveJobApplicantRepositoryInterface;

class ArchiveApplicantController extends Controller
{
    public function __construct(
        private ArchiveJobApplicantRepositoryInterface $archive,
        private JobVacancyRepositoryInterface $vacancy,
        private ResponseHelper $response
    ) { }

    public function getJobApplicant()
    {
        try {
            $data = $this->archive->getAllJobApplicant();
            if (!count($data)) {
                throw new ModelNotFoundException();
            }
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    public function getTraineeship()
    {
        try {
            $data = $this->archive->getAllTranieeship();
            if (!count($data)) {
                throw new ModelNotFoundException();
            }
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    public function getJobApplicantByJobVacancy($id)
    {
        try {
            $data = $this->archive->getJobApplicantByJobVacancy($id);
            if (!count($data)) {
                throw new ModelNotFoundException();
            }
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    public function getTraineeshipByJobVacancy($id)
    {
        try {
            $data = $this->archive->getTraineeshipByJobVacancy($id);
            if (!count($data)) {
                throw new ModelNotFoundException();
            }
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }

    public function find($id)
    {
        try {
            $data = $this->archive->find($id);
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }
    }
}
