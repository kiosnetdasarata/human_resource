<?php

namespace App\Interfaces\Employee;

interface EmployeeRepositoryInterface
{
    public function getAll();
    public function getArchive();
    public function getManager($division);
    public function getByDivision($divisionId);
    public function getArchiveEmployees($divisionId);
    public function show($uuid, $var);
    // public function findBySlug($slug);
    public function allowance($uuid);
    public function findWithTrashes();
    // public function findBySlugWithTrashes($slug);
    public function find($uuid);
    public function create($request);
    public function attachAllowance($employee, $allowanceId);
    public function detachAllowance($employee, $allowanceId);
    public function createArchive($request);
    public function update($employee, $request);
    public function delete($employee);
}
