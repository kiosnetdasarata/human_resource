<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Dotenv\Exception\ValidationException;
use Google\Cloud\Core\Exception\ConflictException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Interfaces\JobApplicantRepositoryInterface;
use App\Interfaces\Internship\TraineeshipRepositoryInterface;
use App\Interfaces\Internship\InterviewPointRepositoryInterface;

class InterviewPointService
{
    public function __construct(
        private InterviewPointRepositoryInterface $interviewPoint,
        private TraineeshipRepositoryInterface $traineeship,
        private JobApplicantRepositoryInterface $jobApplicant,
    )
    {
        //
    }
    
    public function store($id, $request, $isIntern)
    {
        return DB::transaction(function ()  use ($id, $request, $isIntern) {
            $isIntern = $this->validate($isIntern);

            $applicant = ($isIntern ? $this->traineeship : $this->jobApplicant)->find($id);

            if ($applicant->hr_point_id) {
                throw new ConflictException('job Applicant ini sudah memiliki interview point dengan id '. $applicant->hr_point_id);
            } elseif ($applicant->status_tahap != 'Assesment') {
                throw new ValidationException('job Applicant harus pada tahap Assesment');
            }

            $poin = $this->interviewPoint->create($request);
            ($isIntern ? $this->traineeship : $this->jobApplicant)->update($id, ['hr_point_id' => $poin->id]);
        });
    }
    
    public function find($id, $isIntern)
    {
        $isIntern = $this->validate($isIntern);

        $poin = $this->interviewPoint->find($id, $isIntern);
        if ($poin) return $poin;
        throw new ModelNotFoundException();
    }

    public function update($id, $request, $isIntern)
    {      
        $poin = $this->find($id, $isIntern);

        return $this->interviewPoint->update($poin, $request);
    }

    public function delete($interviewPoint)
    {
        return $this->interviewPoint->delete($interviewPoint);
    }

    private function validate($isIntern)
    {
        $isIntern = Str::lower($isIntern);
        if ($isIntern == 'traineeship') {
            return true;
        } elseif ($isIntern == 'job-applicant') {
            return false;
        } else throw new ModelNotFoundException();
    }
}