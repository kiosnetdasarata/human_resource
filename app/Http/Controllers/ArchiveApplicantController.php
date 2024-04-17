<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use App\Interfaces\JobVacancyRepositoryInterface;
use App\Interfaces\ArchiveJobApplicantRepositoryInterface;

class ArchiveApplicantController extends Controller
{
    public function __construct(
        private ArchiveJobApplicantRepositoryInterface $archive,
        private JobVacancyRepositoryInterface $vacancy,
        private ResponseHelper $response
    ) { }

    public function get(Request $request)
    {
        try {
            $data = $this->archive->query($request);

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
