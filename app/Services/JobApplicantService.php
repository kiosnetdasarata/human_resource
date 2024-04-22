<?php

namespace App\Services;

use Carbon\Carbon;
use App\Helpers\FileHelper;
use App\Interfaces\ArchiveJobApplicantRepositoryInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Interfaces\JobVacancyRepositoryInterface;
use App\Interfaces\JobApplicantRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use LogicException;

class JobApplicantService
{
    public function __construct(
        private JobApplicantRepositoryInterface $jobApplicant,
        private InterviewPointService $interviewPoint,
        private ArchiveJobApplicantRepositoryInterface $archive,
        private JobVacancyRepositoryInterface $jobVacancy,
        private FileHelper $file,
    )
    {
        //
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
        return $this->jobApplicant->search($key, $val);
    }

    public function getByVacancy($id)
    {
        return $this->jobVacancy->getJobApplicants($id);
    }

    public function create($request)
    {
        $jobVacancy = $this->jobVacancy->find($request['vacancy_id']);

        $this->validateData($jobVacancy, $request);

        $slug = $this->generateSlug($request['nama_lengkap']);

        $data = collect($request)->merge([
            'nama_lengkap'  => Str::title($request['nama_lengkap']),
            'file_cv'       => $this->file->uploadToGCS($request['file_cv'], $slug .'_'. $jobVacancy['role']['nama_jabatan'] . '_cv' , 'Applicant/file_cv'),
            'date'          => now(),
            'slug'          => $slug,
            'role_id'       => $jobVacancy['role_id']
        ])->all();

        return $this->jobApplicant->create($data);
    }

    public function update($id, $request)
    {
        return DB::transaction(function() use ($id, $request){
            $old = $this->find($id);
            $data = collect($request)->diffAssoc($old);

            $this->validateData($old->jobVacancy, $data);

            if (isset($data['nama_lengkap'])) {
                $data->put('nama_lengkap', Str::title($data['nama_lengkap']))
                     ->put('slug', $this->generateSlug($request['nama_lengkap']));
            }

            if (isset($data['file_cv'])) {
                $data->put('file_cv', $this->file->uploadToGCS($request['file_cv'], $old->slug .'_'. $old->role->nama_jabatan . '_cv', 'Applicant/file_cv'));
            }

            $this->jobApplicant->update($old, $data->all());
        });
    }

    public function updateStatus($id, $status)
    {
        $jobApplicant = $this->find($id);

        return DB::transaction(function() use ($jobApplicant, $status) {
            $oldStatus = $jobApplicant->status_tahap;

            if ($status == 'Assesment' && $oldStatus != 'FU') {
                throw new LogicException('status tidak valid');
            } elseif ($status == 'Lolos' && $jobApplicant->hr_point_id == null) {
                throw new ModelNotFoundException('hr point not found');
            }

            $this->jobApplicant->update($jobApplicant, ['status' => $status]);

            if ($status == 'Lolos' || $status == 'Tolak') {
                $this->delete($jobApplicant, 'status menjadi' + $status);
            }
        });
    }

    private function validateData($jobVacancy, $jobApplicant)
    {
        if (now() > $jobVacancy['close_date'] || now() < $jobVacancy['open_date']){
            throw new LogicException('vacancy belum dibuka / sudah ditutup');
        }

        if (isset($jobApplicant['tanggal_lahir'])) {
            $age = Carbon::parse($jobApplicant['tanggal_lahir'])->diffInYears(now());
            if ($age > $jobVacancy['max_umur'] || $age < $jobVacancy['min_umur']) {
                throw new LogicException('umur tidak valid');
            }
        }
    }

    private function generateSlug($name)
    {
        $list = $this->findSlug($name);

        $slug = Str::slug($name,'_');
        if (count($list)) {
            $int    = $list->sortBy('slug')->last()->slug;
            $int    = explode('_', $int);
            $slug   = $slug . '_' . (int) end($int) + 1;
        }

        return $slug;
    }

    public function delete($applicant, $ket)
    {
        $this->interviewPoint->delete($applicant->interviewPoint);

        $data = collect($applicant)->merge([
            'tanggal_lamaran'   => $applicant->created_at,
            'status_lamaran'    => $applicant->status_tahap,
            'is_intern'         => 0,
            'keterangan'        => $ket,
            'role_id'           => $applicant->role_id,
        ]);
        $this->archive->create($data);
        $this->jobApplicant->delete($applicant);
    }
}
