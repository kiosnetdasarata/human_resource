<?php 

namespace App\Interfaces\Employee;

use App\Models\EmployeeEducation;

interface EmployeeEducationRepositoryInterface
{
    public function getAll();
    public function find($mitraid);
    public function create($request);
    public function update(EmployeeEducation $employeeEducation, $request);
    public function delete(EmployeeEducation $employeeEducation);
}

?>