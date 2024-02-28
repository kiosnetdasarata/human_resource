<?php

namespace App\Http\Controllers;

use App\Interfaces\ArchiveJobApplicantRepositoryInterface;
use App\Interfaces\JobVacancyRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ArchiveApplicantController extends Controller
{
    public function __construct(
        private ArchiveJobApplicantRepositoryInterface $archive,
        private JobVacancyRepositoryInterface $vacancy
    ) { }

    public function getJobApplicant()
    {
        try {
            $data = $this->archive->getAllJobApplicant();
            if (count($data)) {
                return response()->json([
                    'status' => 'success',
                    'data' => $data,
                    'status_code' => 200,
                ]);
            } else throw new ModelNotFoundException('applicant tidak ditemukan', 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
                'trace' => $e->getTrace(),
                'status_code' => 500,
            ]);
        }
    }

    public function getTraineeship()
    {
        try {
            $data = $this->archive->getAllTranieeship();
            if (count($data)) {
                return response()->json([
                    'status' => 'success',
                    'data' => $data,
                    'status_code' => 200,
                ]);
            } else throw new ModelNotFoundException('applicant tidak ditemukan', 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }

    public function getJobApplicantByJobVacancy($id)
    {
        try {
            $data = $this->archive->getJobApplicantByJobVacancy($id);
            if (count($data)) {
                return response()->json([
                    'status' => 'success',
                    'data' => $data,
                    'status_code' => 200,
                ]);
            } else throw new ModelNotFoundException('applicant tidak ditemukan', 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
                'trace' => $e->getTrace(),
                'status_code' => 500,
            ]);
        }
    }

    public function getTraineeshipByJobVacancy($id)
    {
        try {
            $data = $this->archive->getTraineeshipByJobVacancy($id);
            if (count($data)) {
                return response()->json([
                    'status' => 'success',
                    'data' => $data,
                    'status_code' => 200,
                ]);
            } else throw new ModelNotFoundException('applicant tidak ditemukan', 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
                'status_code' => 500,
            ]);
        }
    }

    public function find($id)
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => $this->archive->find($id),
                'status_code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
                'status_code' => 404,
            ]);
        }
    }
}
