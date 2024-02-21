<?php 

namespace App\Repositories;

use App\Interfaces\JobApplicantRepositoryInterface;
use App\Models\JobApplicant;

class JobApplicantRepository implements JobApplicantRepositoryInterface
{
    public function __construct(private JobApplicant $jobApplicant) 
    {
    }

    public function getAll()
    {
        return $this->jobApplicant->get();
    }

    public function search($key, $value)
    {
        return $this->jobApplicant->with(['interviewPoint', 'jobVacancy'])->where($key, $value)->withTrashed()->get();
    }

    public function find($id)
    {
        return $this->jobApplicant->with(['interviewPoint', 'jobVacancy'])->where('id', $id)->first();
    }

    public function findSlug($slug)
    {
        return $this->jobApplicant->where(function ($query) use ($slug) {
            $query->orWhere('slug', $slug);
            $query->orWhere('slug', 'REGEXP', '^'.$slug.'_[0-9]+$');
        })->get();
    }
    
    public function countSlug($slug)
    {
        return $this->jobApplicant->where(function ($query) use ($slug) {
            $query->orWhere('slug', $slug);
            $query->orWhere('slug', 'REGEXP', '^'.$slug.'_[0-9]+$');
        })->get();
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