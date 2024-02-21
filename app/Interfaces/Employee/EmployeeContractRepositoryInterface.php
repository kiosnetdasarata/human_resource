<?php 

namespace App\Interfaces\Employee;

use App\Models\EmployeeContract;

interface EmployeeContractRepositoryInterface
{
    public function getAll($id);
    public function find($id);
    public function create($request);
    public function update(EmployeeContract $employeeContract, $request);
    public function delete(EmployeeContract $employeeContract);
}

?>