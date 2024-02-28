<?php 

namespace App\Interfaces;

use App\Models\JobApplicant;

interface JobApplicantRepositoryInterface
{
    public function getAll();
    public function find($id);
    public function findSlug($slug);
    public function search($key, $value);
    public function findWithTrashes($slug);
    public function create($request);
    public function update(JobApplicant $traineeship, $request);
    public function delete(JobApplicant $traineeship);
}