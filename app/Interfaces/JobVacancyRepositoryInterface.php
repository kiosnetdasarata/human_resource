<?php 

namespace App\Interfaces;

interface JobVacancyRepositoryInterface
{
    public function getAll();
    public function getRole();
    public function find($id);
    public function getTraineeships($id);
    public function getJobApplicants($id);
    public function findSameRoleOnBranch($roleId, $branchId);
    public function findByRole($roleId);
    public function create($request);
    public function update($jobVacancy, $request);
    public function delete($jobVacancy);

}