<?php

namespace App\Interfaces\Employee;

use App\Models\Employee;

interface EmployeeRepositoryInterface
{
    public function getAll();
    public function getArchive();
    public function getManager();
    public function getByDivision($divisionId);
    public function find($uuid, $var = 'id');
    public function findBySlug($slug);
    public function findWithTrashes();
    public function findBySlugWithTrashes($slug);
    public function show($uuid);
    public function create($request);
    public function createArchive($request);
    public function update($employee, $request);
    public function delete($employee);
}
