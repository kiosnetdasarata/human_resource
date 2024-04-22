<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\JobVacancy;
use App\Interfaces\JobVacancyRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class JobVacancyRepository implements JobVacancyRepositoryInterface
{

    public function __construct(private JobVacancy $jobVacancy)
    {
        //
    }

    public function getAll()
    {
        return $this->jobVacancy
                ->with('role:id,nama_jabatan', 'branch:id,nama_branch', 'jobapplicant', 'traineeship')
                ->get()
                ->map(function ($e) {
                    return collect($e)->merge([
                        'role' => $e->role->nama_jabatan,
                        'branch' => $e->branch->nama_branch,
                        'applicant_count' => count($e->jobapplicant) + count($e->traineeship),
                        'applicant_sum' => $e->countApplicantsByStatus()
                    ]);
                });
    }

    public function getRole()
    {
        $roleId = $this->jobVacancy->where('is_active', 1)->select('role_id')->distinct()->get();
        return Role::whereIn('id', $roleId)->get();
    }

    public function find($id)
    {
        return $this->jobVacancy->find($id);
    }

    public function findMap($id)
    {
        $jobVacancy = $this->find($id)->load('role');
        return collect($jobVacancy)->merge([
            'branch'            => $jobVacancy->branch->nama_branch,
            'applicant_count'   => count($jobVacancy->jobapplicant) + count($jobVacancy->traineeship),
            'applicant_sum'     => $jobVacancy->countApplicantsByStatus()
        ]);
    }

    public function getTraineeships($id)
    {
        return $this->find($id)->traineeship ?? throw new ModelNotFoundException('isIntern is false');
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

