<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Interfaces\JobAplicantRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Interfaces\Internship\InterviewPointRepositoryInterface;

class JobAplicantService
{
    public function __construct(
        private JobAplicantRepositoryInterface $jobApplicant,
        private InterviewPointRepositoryInterface $interviewPoint) 
    {
    }

    public function get()
    {
        return $this->jobApplicant->getAll();
    }

    public function find($id) 
    {
        return $this->jobApplicant->find($id);
    }

    public function create($request)
    {
        // if (Carbon::parse($request['tanggal_lahir'])->diffYears(Carbon::now()))
            // return true;

        $aplicant = collect($request)->merge([
            'file_cv' => 'filenya ada',
            'date' => Carbon::now(),
        ]);
        
        // $traineeship->put('file_cv', $request['file_cv']->storeAs('traineeship/cv', $traineeship['slug'].'_cv.pdf', 'gcs'));

        return $this->jobApplicant->create($aplicant->all());
    }

    public function update($id, $request) 
    {
        return DB::transaction(function() use ($id, $request){  
            $old = $this->find($id);
            $traineeship = collect($request)->diffAssoc($old);
            if (isset($traineeship['file_cv'])) {
                $traineeship->put('file_cv', 'test_cv');
                // $traineeship->put('file_cv', $request->file['file_cv']->storeAs('traineeship/cv', $traineeship['uuid'].'.pdf', 'gcs'));
            }
            if (isset($request['status_tahap'])) {
                $oldStatus = $old->status_tahap;
                $newStatus = $request['status_tahap'];
                if ($newStatus == 'Lolos' && $oldStatus != 'Assesment') {
                    throw new \Exception ('status traineeship tidak valid');
                } elseif ($newStatus == 'Assesment' && $oldStatus == null) {
                    throw new \Exception('job aplicant tidak memiliki hr point');
                } elseif ($newStatus == 'Tolak'){
                    $this->jobApplicant->delete($old);
                    if ($old->interviewPoint != null)
                        $this->interviewPoint->delete($old->interviewPoint);
                }
            }
            $this->jobApplicant->update($old, $traineeship->all());
        });
    }

    public function addInterviewPoint($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            $jobApplicant = $this->jobApplicant->find($id);
            if ($jobApplicant->hr_point_id != null) {
                throw new \Exception('job aplicant ini sudah memiliki interview point dengan id '. $jobApplicant->hr_point_id);
            } elseif ($jobApplicant->status_tahap != 'FU') {
                throw new \Exception('job aplicant harus pada tahap FU');
            }
            $this->interviewPoint->create($request);
            $this->jobApplicant->update($jobApplicant, ['hr_point_id' => $this->interviewPoint->latest()->id]);
        });
    }

    public function showInterviewPoint($id)
    {
        return $this->find($id)->interviewPoint;
    }

    public function updateInterviewPoint($id, $request) 
    {
        $poin = $this->jobApplicant->find($id)->interviewPoint;
        if ($poin == null) {
            throw new ModelNotFoundException('Traineeship ini belum memiliki interview point');
        }
        return $this->interviewPoint->update($poin, $request);
    }

    public function deleteInterviewPoint($id)
    {
        $poin = $this->jobApplicant->find($id)->interviewPoint;
        if ($poin == null) {
            throw new ModelNotFoundException('Traineeship ini belum memiliki interview point');
        }
        return $this->interviewPoint->delete($poin);
    }
}
?>