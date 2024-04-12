<?php

namespace App\Interfaces\Internship;

use App\Models\InternshipContract;

interface InternshipContractRepositoryInterface
{
    public function getAll($uuid);
    public function find($uuid);
    public function create($request);
    public function update($internshipContract, $request);
    public function delete($internshipContract);
}
