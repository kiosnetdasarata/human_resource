<?php 

namespace App\Repositories;

use App\Interfaces\JobAplicantRepositoryInterface;
use App\Models\JobApplicant;

class JobAplicantRepository implements JobAplicantRepositoryInterface
{
    public function __construct(private JobApplicant $jobApplicant) 
    {
    }

    public function getAll()
    {
        return $this->jobApplicant->get();
    }

    public function find($id)
    {
        return $this->jobApplicant->with('interviewPoint')->where('id', $id)->get()->first();
    }

    public function findWithTrashes($id)
    {
        return $this->jobApplicant->withTrashed()->findOrFail($id);
    }

    public function create($request)
    {
        return $this->jobApplicant->create($request);
    }

    public function update($jobApplicant, $request)
    {
        return $jobApplicant->update($request);
    }

    public function delete($jobApplicant)
    {
        return $jobApplicant->delete();
    }


}