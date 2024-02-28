<?php 

namespace App\Repositories;

use App\Models\Role;
use App\Models\JobVacancy;
use App\Interfaces\JobVacancyRepositoryInterface;
use InvalidArgumentException;

class JobVacancyRepository implements JobVacancyRepositoryInterface
{

    public function __construct(private JobVacancy $jobVacancy)
    {
    }

    public function getAll()
    {
        return $this->jobVacancy->get()->map(function ($e) {
            $applicant = collect($e->jobapplicant)->countBy('status_tahap');
            if ($e->is_intern) {
                $trainee = collect($e->traineeship)->countBy('status_tahap');
                $applicant = $applicant->mergeRecursive($trainee)->map(function ($value, $key) {
                    return is_array($value) ? array_sum($value) : $value;
                });
            }
            $data = collect($e)->merge([
                'role' => $e->role->nama_jabatan,
                'branch' => $e->branch->nama_branch,
                'applicant_count' => count($e->jobapplicant) + count($e->traineeship),
                'applicant_sum' => $applicant->all()
            ])->except(['jobapplicant', 'traineeship']);
            return $data;
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

    public function getTraineeships($id) 
    {
        return $this->find($id)->traineeship;
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

