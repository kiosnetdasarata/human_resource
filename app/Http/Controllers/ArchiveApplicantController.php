<?php

namespace App\Http\Controllers;

use App\Interfaces\ArchiveJobApplicantRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ArchiveApplicantController extends Controller
{
    public function __construct(private ArchiveJobApplicantRepositoryInterface $archive) { }

    public function getApplicant()
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
