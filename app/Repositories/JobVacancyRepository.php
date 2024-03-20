<?php 

namespace App\Repositories;

use App\Models\Role;
use App\Models\JobVacancy;
use App\Interfaces\JobVacancyRepositoryInterface;
use DomainException;

class JobVacancyRepository implements JobVacancyRepositoryInterface
{

    public function __construct(private JobVacancy $jobVacancy)
    {
    }

    public function getAll()
    {
        return $this->jobVacancy->get()->map(function ($e) {
            return $this->map($e)->except(['jobapplicant', 'traineeship']);
        });
    }

    public function getRole()
    {
        $roleId = $this->jobVacancy->where('is_active', 1)->select('role_id')->distinct()->get();
        return Role::whereIn('id', $roleId)->get();
    }

    public function find($id)
    {
        return $this->jobVacancy->with('role')->where('id', $id)->firstOrFail();
    }

    public function findMap($id)
    {
        return $this->map($this->find($id));
    }

    private function map($jobVacancy)
    {
        $applicant = collect($jobVacancy->jobapplicant)->countBy('status_tahap');
        if ($jobVacancy->is_intern) {
            $trainee = collect($jobVacancy->traineeship)->countBy('status_tahap');
            $applicant = $applicant->mergeRecursive($trainee)->map(function ($value, $key) {
                return is_array($value) ? array_sum($value) : $value;
            });
        }
        return collect($jobVacancy)->merge([
            'role' => $jobVacancy->role->nama_jabatan,
            'branch' => $jobVacancy->branch->nama_branch,
            'applicant_count' => count($jobVacancy->jobapplicant) + count($jobVacancy->traineeship),
            'applicant_sum' => $applicant->all()
        ]);
    }

    public function getTraineeships($id) 
    {
        return $this->find($id)->traineeship ?? throw new DomainException('isIntern is false');
    }

    public function getJobApplicants($id)
    {

        return $this->find($id)->jobApplicant;
    }

    public function findByRole($roleId)
    {
        return $this->jobVacancy->where('role_id', $roleId)->get();
    }

    public function findSameRoleOnBranch($roleId, $branch)
    {
        return $this->jobVacancy->where('role_id', $roleId)->where('branch_company_id', $branch)->first();
    }
    
    public function create($request)
    {
        return $this->jobVacancy->create($request->all());
    }
    
    public function update($jobVacancy, $request)
    {
        return $jobVacancy->update($request);
    }
    
    public function delete($jobVacancy)
    {
        return $jobVacancy->delete();
    }
}

