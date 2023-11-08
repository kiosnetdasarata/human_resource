<?php 

namespace App\Interfaces;

use App\Models\JobApplicant;

interface JobAplicantRepositoryInterface
{
    public function getAll();
    public function find($slug);
    public function findWithTrashes($slug);
    public function create($request);
    public function update(JobApplicant $traineeship, $request);
    public function delete(JobApplicant $traineeship);
}

?>