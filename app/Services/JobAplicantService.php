<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Interfaces\Internship\InterviewPointRepositoryInterface;
use App\Interfaces\JobVacancyRepositoryInterface;
use App\Interfaces\JobAplicantRepositoryInterface;

class JobAplicantService
{
    public function __construct(
        private JobAplicantRepositoryInterface $jobApplicant,
        private InterviewPointRepositoryInterface $interviewPoint,
        private JobVacancyRepositoryInterface $jobVacancy) 
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

    public function findSlug($id)
    {
        return $this->jobApplicant->findSlug($id);
    }

    public function search($key, $val)
    {
        return $this->jobApplicant->search($key, $val);
    }

    public function create($request)
    {
        $jobVacancy = $this->jobVacancy->find($request['vacancy_id']);
        $age = Carbon::parse($request['tanggal_lahir'])->diffInYears(Carbon::now());
        if (Carbon::now() > $jobVacancy['close_date'] || Carbon::now() < $jobVacancy['open_date'])
            throw new \Exception('vacancy belum dibuka / sudah ditutup',403);
        if ($age > $jobVacancy->max_umur || $age < $jobVacancy->min_umur)
            return true;

        $aplicant = collect($request)->merge([
            'file_cv' => 'filenya ada',
            'date' => Carbon::now(),
            'slug' => Str::slug($request['nama_lengkap'], '_'),
        ]);
        // $traineeship->put('file_cv', $request['file_cv']->storeAs('traineeship/cv', $traineeship['slug'].'_cv.pdf', 'gcs'));

        return $this->jobApplicant->create($aplicant->all());
    }

    public function update($id, $request) 
    {
        return DB::transaction(function() use ($id, $request){  
            $old = $this->find($id);
            $jobAplicant = collect($request)->diffAssoc($old);
            if (isset($jobAplicant['file_cv'])) {
                $jobAplicant->put('file_cv', 'test_cv');
                // $jobAplicant->put('file_cv', $request->file['file_cv']->storeAs('jobAplicant/cv', $jobAplicant['uuid'].'.pdf', 'gcs'));
            }
            if (isset($jobAplicant['nama_lengkap'])) {
                $jobAplicant->put('slug', Str::slug($jobAplicant['nama_lengkap']));
            }
            if (isset($request['status_tahap'])) {
                $oldStatus = $old->status_tahap;
                $newStatus = $request['status_tahap'];
                if (isset($request['status_tahap'])) {
                    $oldStatus = $old->status_tahap;
                    $newStatus = $request['status_tahap'];
                    if ($newStatus == 'Assesment' && $oldStatus != 'FU') {
                        throw new \Exception ('status jobAplicant tidak valid', 422);
                    } elseif ($newStatus == 'Lolos' || $old->hr_point_id == null) {
                        throw new \Exception ('hr point dari jobAplicant tidak ditemukan', 404);
                    } elseif ($newStatus == 'Tolak') {
                        $this->jobApplicant->delete($old);
                        if ($old->interviewPoint != null)
                            $this->interviewPoint->delete($old->interviewPoint);
                    }
                }
            }
            $this->jobApplicant->update($old, $jobAplicant->all());
        });
    }

    public function addInterviewPoint($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            $jobApplicant = $this->jobApplicant->find($id);
            if ($jobApplicant->hr_point_id != null) {
                return $this->interviewPoint->update($jobApplicant->hr_point_id, $request);
            } elseif ($jobApplicant->status_tahap != 'Assessment') {
                throw new \Exception('job aplicant harus pada tahap Assessment',422);
            }
            $poin = $this->interviewPoint->create($request);
            return $this->jobApplicant->update($jobApplicant, ['hr_point_id' => $poin->id]);
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
            throw new ModelNotFoundException('aplicant ini belum memiliki interview point');
        }
        return $this->interviewPoint->update($poin, $request);
    }

    public function deleteInterviewPoint($id)
    {
        $poin = $this->jobApplicant->find($id)->interviewPoint;
        if ($poin == null) {
            throw new ModelNotFoundException('aplicant ini belum memiliki interview point');
        }
        return $this->interviewPoint->delete($poin);
    }
}
?>