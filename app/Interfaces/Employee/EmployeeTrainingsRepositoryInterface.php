<?php 

namespace App\Interfaces\Employee;

use App\Models\EmployeeTrainings;

interface EmployeeTrainingsRepositoryInterface
{
    public function getAll();
    public function find($mitraid);
    public function create($request);
    public function update(EmployeeTrainings $employeeTrainings, $request);
    public function delete(EmployeeTrainings $employeeTrainings);
}

?>