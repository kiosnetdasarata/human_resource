<?php

namespace App\Interfaces\Employee;

interface EmployeeEducationRepositoryInterface
{
    public function getAll($id);
    public function find($id);
    public function create($request);
    public function update($employeeEducation, $request);
    public function delete($employeeEducation);
}
