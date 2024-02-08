<?php 

namespace App\Interfaces\Employee;

use App\Models\Employee;

interface EmployeeRepositoryInterface
{
    public function getAll();
    public function find($uuid);
    public function findBySlug($slug);
    public function findWithTrashes();
    public function findBySlugWithTrashes($slug);
    public function findByDivision($division);
    public function show($uuid);
    public function create($request);
    public function update(Employee $employee, $request);
    public function delete(Employee $employee);
}

?>