<?php 

namespace App\Interfaces\Employee;

use App\Models\Employee;

interface EmployeeRepositoryInterface
{
    public function getAll();
    public function find($uuid, $table);
    public function findBySlug($slug);
    public function findWithTrashes();
    public function findBySlugWithTrashes($slug);
    public function create($request);
    public function update(Employee $employee, $request);
    public function delete(Employee $employee);
}

?>