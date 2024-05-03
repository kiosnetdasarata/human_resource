<?php

namespace App\Interfaces\Employee;

interface EmployeeCIRepositoryInterface
{
    // public function find($uuid);
    public function create($request);
    public function update($employeeConfidentalInformation, $request);
    public function delete($employeeConfidentalInformation);
}
