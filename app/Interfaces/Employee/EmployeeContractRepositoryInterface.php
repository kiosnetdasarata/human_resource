<?php

namespace App\Interfaces\Employee;

interface EmployeeContractRepositoryInterface
{
    public function getAll($uuid);
    public function find($uuid);
    public function create($request);
    public function update($employeeContract, $request);
    public function delete($employeeContract);
}
