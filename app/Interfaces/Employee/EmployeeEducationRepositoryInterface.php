<?php 

namespace App\Interfaces\Employee;

use App\Models\EmployeeEducation;

interface EmployeeEducationRepositoryInterface
{
    public function getAll($id);
    public function find($id);
    public function create($request);
    public function update(EmployeeEducation $employeeEducation, $request);
    public function delete(EmployeeEducation $employeeEducation);
}

?>