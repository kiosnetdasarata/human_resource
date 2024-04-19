<?php

namespace App\Services;

use Carbon\Carbon;
use LogicException;
use App\Helpers\FileHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Interfaces\JobVacancyRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Interfaces\ArchiveJobApplicantRepositoryInterface;
use App\Interfaces\Internship\TraineeshipRepositoryInterface;

class TraineeshipService
{
    public function __construct(
        private ArchiveJobApplicantRepositoryInterface $archiveJobApplicant,
        private TraineeshipRepositoryInterface $traineeship,
        private JobVacancyRepositoryInterface $jobVacancy,
        private FileHelper $file,
    )
    {
        //
    }

    public function get()
    {
        return $this->traineeship->getAll();
    }

    public function find($id, $withtrashes = false)
    {
        return $withtrashes ? $this->traineeship->findWithTrashes($id) : $this->traineeship->find($id);
    }

    public function findTraineeshipSlug($name)
    {
        return $this->traineeship->findBySlug(Str::slug($name, '_'));
    }

    public function findByVacancy($id)
    {
        return $this->jobVacancy->getTraineeships($id);
    }

    public function create($request)
    {
        $jobVacancy = $this->jobVacancy->find($request['vacancy_id']);
        $this->validateData($request, $jobVacancy);

        $slug = $this->generateTraineeshipSlug($request['nama_lengkap']);
        $traineeship = collect($request)->merge([
            'nama_lengkap'      => Str::title($request['nama_lengkap']),
            'slug'              => $slug,
            'tanggal_lamaran'   => now()->format('Y-m-d'),
            'file_cv'           => $this->file->uploadToGCS($request['file_cv'], $slug .'_'. $jobVacancy['role']['nama_jabatan'] . '_cv','traineeship/cv')
        ]);

        return $this->traineeship->create($traineeship->all());
    }

    public function update($id, $request)
    {
        $old = $this->traineeship->find($id);

        $data = collect($request)->diffAssoc($old);
        $this->validateData($data, $old->jobVacancy);

        return DB::transaction(function() use ($old, $data){
            if (isset($data['nama_lengkap'])) {
                $data->put('nama_lengkap', Str::title($data['nama_lengkap']))
                     ->put('slug', $this->generateTraineeshipSlug($data['nama_lengkap']));
            }

            if (isset($data['file_cv'])) {
                $link = $this->file->uploadToGCS($data['file_cv'], $old->slug .'_'. $old->role->nama_jabatan . '_cv', 'traineeship/file_cv');
                $data->put('file_cv', $link);
            }

            $this->traineeship->update($old, $data->all());
        });
    }

    public function updateStatus($id, $status)
    {
        $old = $this->traineeship->find($id);
        if (!$old) throw new ModelNotFoundException();

        return DB::transaction(function () use ($old, $status) {
            $oldStatus = $old->status_tahap;

            if ($status == 'Assesment' && $oldStatus != 'FU') {
                throw new LogicException('status jobApplicant tidak valid');
            }

            $this->traineeship->update($old, ['status_tahap' => $status]);

            if ($status == 'Tolak' ||$status == 'Lolos') {
                $this->delete($old);
            }
        });
    }

    private function validateData($request, $jobVacancy)
    {
        if (now() > $jobVacancy['close_date'] || now() < $jobVacancy['open_date']) {
            throw new ModelNotFoundException('vacancy belum dibuka / sudah ditutup');
        }
        if (isset($request['tanggal_lahir'])) {
            $age = Carbon::parse($request['tanggal_lahir'])->diffInYears(now());
            if ($age > $jobVacancy['max_umur'] || $age < $jobVacancy['min_umur']) {
                throw new LogicException('umur tidak valid');
            }
        }

        if (isset($request['tahun_lulus']) && $request['tahun_lulus'] >= date('Y')) {
            throw new LogicException('tahun lulus tidak valid');
        }
    }


    private function generateTraineeshipSlug($name)
    {
        $list = $this->findTraineeshipSlug($name);

        $slug = Str::slug($name,'_');
        if (count($list)) {
            $int    = $list->sortBy('slug')->last()->slug;
            $int    = explode('_', $int);
            $slug   = $slug . '_' . (int) end($int) + 1;
        }

        return $slug;
    }

    private function delete($traineeship)
    {
        $data = collect($traineeship)->merge([
            'tanggal_lamaran'   => $traineeship->created_at,
            'keterangan'        => 'dihapus karena job vacancy terhapus',
            'status_lamaran'    => $traineeship->status_tahap,
            'is_intern'         => 1,
            'no_tlpn'           => $traineeship->nomor_telepone,
            'role_id'           => $traineeship->role_id,
        ]);

        $this->archiveJobApplicant->create($data->all());
        $this->traineeship->delete($traineeship);
    }
}
