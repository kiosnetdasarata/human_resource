<?php 

namespace App\Repositories\Internship;

use App\Interfaces\Internship\InterviewPointRepositoryInterface;
use App\Models\InterviewPoint;
use App\Models\JobApplicant;
use App\Models\Traineeship;

class InterviewPointRepository implements InterviewPointRepositoryInterface
{
    public function __construct(
        private InterviewPoint $internshipPoint,
        private Traineeship $traineeship,
        private JobApplicant $jobApplicant)
    {
    }

    public function find($id, $isIntern)
    {
        return ($isIntern ? $this->traineeship : $this->jobApplicant)->find($id)->interviewPoint;
    }

    public function create($request)
    {
        return $this->internshipPoint->create($request);
    }

    public function update($internshipPoint, $request)
    {
        return $internshipPoint->update($request);
    }

    public function delete($internshipPoint)
    {
        return $internshipPoint->delete();
    }

    // public function avg($id)
    // {
    //     $iP = $this->find($id);
    //     $nilai = 0;
    //     foreach($iP as $key => $nilai) {
    //         $nilai += (int)$nilai;
    //     }
    // }
}