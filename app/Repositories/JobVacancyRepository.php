<?php 

namespace App\Repositories;

use App\Models\JobVacancy;
use App\Interfaces\JobVacancyRepositoryInterface;

class JobVacancyRepository implements JobVacancyRepositoryInterface
{

    public function __construct(private JobVacancy $jobVacancy)
    {
    }

    public function getAll()
    {
        return $this->jobVacancy->get();
    }

    public function find($id)
    {
        return $this->jobVacancy->findOrFail($id);
    }

    public function findByRole($id)
    {
        return $this->jobVacancy->where('role_id', $id)->firstOrFail();
    }
    
    public function create($request)
    {
        return $this->jobVacancy->create($request);
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

?>