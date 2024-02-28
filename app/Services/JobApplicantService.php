<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Interfaces\Internship\InterviewPointRepositoryInterface;
use App\Interfaces\JobVacancyRepositoryInterface;
use App\Interfaces\JobApplicantRepositoryInterface;

class JobApplicantService
{
    public function __construct(
        private JobApplicantRepositoryInterface $jobApplicant,
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

    public function findSlug($slug)
    {
        return $this->jobApplicant->findSlug($slug);
    }

    public function search($key, $val)
    {
        $data = $this->jobApplicant->search($key, $val);
        if (count($data)) return $data;
        else throw new ModelNotFoundException('data tidak ditemukan',404);
    }

    public function getByVacancy($id)
    {
        $data = $this->jobVacancy->getJobApplicants($id);
        if (count($data)) return $data;
        else throw new ModelNotFoundException('data tidak ditemukan',404);
    }

    public function create($request)
    {
        $jobVacancy = $this->jobVacancy->find($request['vacancy_id']);
        $age = Carbon::parse($request['tanggal_lahir'])->diffInYears(now());
        if (now() > $jobVacancy['close_date'] || now() < $jobVacancy['open_date'])
            throw new \Exception('vacancy belum dibuka / sudah ditutup',403);
        if ($age > $jobVacancy['max_umur'] || $age < $jobVacancy['min_umur'])
            throw new \Exception('umur tidak valid', 422);
        
        $slug = $this->generateSlug($request['nama_lengkap']);
        $Applicant = collect($request)->merge([
            'nama_lengkap' => Str::title($request['nama_lengkap']),
            'file_cv' => uploadToGCS($request['file_cv'], $slug .'_'. $jobVacancy['role']['nama_jabatan'] . '_cv' , 'Applicant/file_cv'),
            'date' => now(),
            'slug' => $slug,
            'role_id' => $jobVacancy['role_id']
        ]);
        return $this->jobApplicant->create($Applicant->all());
    }

    private function generateSlug($name)
    {
        $list = $this->findSlug($name);
        
        $slug = Str::slug($name,'_');
        if (count($list)) {
            $int = $list->sortBy('slug')->last()->slug;
            $int = explode('_', $int);
            $slug = $slug . '_' . (int) end($int) + 1;
        }
        return $slug;
    }

    public function update($id, $request) 
    {
        return DB::transaction(function() use ($id, $request){  
            $old = $this->find($id);
            $jobApplicant = collect($request)->diffAssoc($old);
            if (isset($jobApplicant['nama_lengkap'])) {
                $jobApplicant->put('nama_lengkap', Str::title($jobApplicant['nama_lengkap']));
                $jobApplicant->put('slug', $this->generateSlug($request['nama_lengkap']));
            }
            if (isset($jobApplicant['file_cv'])) {
                $jobApplicant->put('file_cv', uploadToGCS($request['file_cv'], $old->slug .'_'. $old->role->nama_jabatan . '_cv', 'Applicant/file_cv'));
            }
            $this->jobApplicant->update($old, $jobApplicant->all());
        });
    }

    public function updateStatus($id, $status) 
    {
        $jobApplicant = $this->find($id);        
        if ($jobApplicant) throw new ModelNotFoundException('data already deleted');

        return DB::transaction(function() use ($jobApplicant, $status) {
            $oldStatus = $jobApplicant->status_tahap;
            if ($status == 'Assesment' && $oldStatus != 'FU') {
                throw new \Exception ('status jobApplicant tidak valid', 422);
            } elseif ($status == 'Lolos' && $jobApplicant->hr_point_id == null) {
                throw new \Exception ('hr point dari jobApplicant tidak ditemukan', 404);
            }
            
            $this->update($jobApplicant, ['status' => $status]);

            if ($status == 'Lolos' || $status == 'Tolak') {
                if ($jobApplicant->interviewPoint) $this->interviewPoint->delete($jobApplicant->interviewPoint);
                $this->jobApplicant->delete($jobApplicant);
            }
        });
    }

    public function addInterviewPoint($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            $jobApplicant = $this->jobApplicant->find($id);
            if ($jobApplicant->hr_point_id) {
                return $this->interviewPoint->update($jobApplicant->hr_point_id, $request);
            } elseif ($jobApplicant->status_tahap != 'Assesment') {
                throw new \Exception('job Applicant harus pada tahap Assesment',422);
            }
            $poin = $this->interviewPoint->create($request);
            $this->jobApplicant->update($jobApplicant, ['hr_point_id' => $poin->id]);
        });
    }

    public function showInterviewPoint($id)
    {
        return $this->find($id)->interviewPoint;
    }

    public function updateInterviewPoint($id, $request) 
    {
        $poin = $this->jobApplicant->find($id)->interviewPoint; 
        if (!$poin) throw new ModelNotFoundException('Applicant ini belum memiliki interview point');
        return $this->interviewPoint->update($poin, $request);
    }

    public function deleteInterviewPoint($id)
    {
        $poin = $this->jobApplicant->find($id)->interviewPoint;
        if (!$poin) throw new ModelNotFoundException('Applicant ini belum memiliki interview point');
        return $this->interviewPoint->delete($poin);
    }
}
