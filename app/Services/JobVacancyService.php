<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Interfaces\JobVacancyRepositoryInterface;
use App\Interfaces\JobApplicantRepositoryInterface;
use App\Interfaces\ArchiveJobApplicantRepositoryInterface;
use App\Interfaces\Internship\TraineeshipRepositoryInterface;
use LogicException;

class JobVacancyService
{
    public function __construct(
        private JobVacancyRepositoryInterface $jobVacancy,
        private ArchiveJobApplicantRepositoryInterface $archiveJobApplicant,
        private JobApplicantRepositoryInterface $jobApplicant,
        private TraineeshipRepositoryInterface $traineeship
    )
    {
        //
    }

    public function getAll()
    {
        return $this->jobVacancy->getAll();
    }

    public function getRole()
    {
        return $this->jobVacancy->getRole();
    }
    public function getApplicant($id)
    {
        return $this->jobVacancy->getJobApplicants($id);
    }

    public function getTraineeships($id)
    {
        return $this->jobVacancy->getTraineeships($id);
    }

    public function findByRole($id)
    {
        return $this->jobVacancy->findByRole($id);
    }
    public function find($id)
    {
        return $this->jobVacancy->findMap($id);
    }

    public function create($request)
    {
        $this->validateData($request);

        $request = collect($request)->merge([
            'title' => Str::title($request['title']),
            'slug'  => Str::slug($request['title'], '_'),
        ]);

        return $this->jobVacancy->create($request);
    }

    public function update($id, $request)
    {
        $jobVacancy = $this->jobVacancy->find($id);
        $data = collect($request)->diffAssoc($jobVacancy);

        $this->validateData($data, $jobVacancy);

        if (isset($data['title'])) {
            $data->put('title', Str::title($data['title']))
                 ->put('slug', Str::slug($data['title'], '_'));
        }

        $this->jobVacancy->update($jobVacancy,$data->all());
    }

    private function validateData($request, $jobVacancy = null)
    {
        $roleId = isset($request['role_id']) ? $request['role_id'] : $jobVacancy->role_Id;
        $branchId = isset($request['branch_company_id']) ? $request['role_id'] : $jobVacancy->branch_company_id;

        if ($this->jobVacancy->findSameRoleOnBranch($roleId, $branchId)) {
            throw new LogicException('Duplikat role');
        }

        $closeDate = isset($request['close_data']) ? $request['close_data'] : $jobVacancy->close_date;
        $openDate = isset($request['open_data']) ? $request['open_data'] : $jobVacancy->open_date;

        if ($closeDate <= $openDate) {
            throw new LogicException('close date tidak sesuai dengan open date');
        }
    }

    public function delete($id)
    {
        $jobVacancy = $this->jobVacancy->find($id);

        return DB::transaction(function() use ($jobVacancy) {
            $this->deleteApplicant($jobVacancy->jobapplicant, 0, $jobVacancy->role_id);

            if ($jobVacancy->is_intern) {
                $this->deleteApplicant($jobVacancy->traineeship, 1, $jobVacancy->role_id);
            }

            $this->jobVacancy->delete($jobVacancy);
        });
    }

    private function deleteApplicant($applicants, $isIntern, $roleId) {
        foreach ($applicants as $applicant) {
            $data = [
                'tanggal_lamaran'   => $applicant->created_at,
                'keterangan'        => 'dihapus karena job vacancy terhapus',
                'status_lamaran'    => $applicant->status_tahap,
                'is_intern'         => $isIntern,
                'role_id'           => $roleId,
            ];

            if ($isIntern) {
                $data['no_tlpn'] = $applicant->nomor_telepone;
            }

            $this->archiveJobApplicant->create($data);

            ($isIntern ? $this->traineeship : $this->jobApplicant)->delete($applicants);
        }
    }
}
