<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Interfaces\JobVacancyRepositoryInterface;
use App\Interfaces\ArchiveJobApplicantRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use LogicException;

class JobVacancyService
{
    public function __construct(
        private JobVacancyRepositoryInterface $jobVacancy,
        private ArchiveJobApplicantRepositoryInterface $archiveJobApplicant,
        private JobApplicantService $jobApplicant,
        private TraineeshipService $traineeship,
        private RoleRepositoryInterface $role,
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

        if ($this->role->find('role_id')->division->is_active) {
            throw new LogicException('Divisi tidak aktif');
        }

        if ($this->jobVacancy->findSameRoleOnBranch($roleId, $branchId)) {
            throw new LogicException('Duplikat role');
        }

        $closeDate = isset($request['close_date']) ? $request['close_date'] : $jobVacancy->close_date;
        $openDate = isset($request['open_date']) ? $request['open_date'] : $jobVacancy->open_date;

        if ($closeDate <= $openDate) {
            throw new LogicException('close date tidak sesuai dengan open date');
        }
    }

    public function delete($id)
    {
        $jobVacancy = $this->jobVacancy->find($id);

        return DB::transaction(function() use ($jobVacancy) {
            $this->deleteApplicant($jobVacancy->jobapplicant, 0);

            if ($jobVacancy->is_intern) {
                $this->deleteApplicant($jobVacancy->traineeship, 1);
            }

            $this->jobVacancy->delete($jobVacancy);
        });
    }

    private function deleteApplicant($applicants, $isIntern) {
        foreach ($applicants as $applicant) {
            ($isIntern ? $this->traineeship : $this->jobApplicant)->delete($applicant, 'dihapus karena job vacancy terhapus');
        }
    }
}
