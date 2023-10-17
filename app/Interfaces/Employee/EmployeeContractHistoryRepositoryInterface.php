<?php 

namespace App\Interfaces\Employee;

use App\Models\EmployeeContractHistory;

interface EmployeeContractHistoryRepositoryInterface
{
    public function getAll();
    public function find($mitraid);
    public function create($request);
    public function update(EmployeeContractHistory $employeeContractHistory, $request);
    public function delete(EmployeeContractHistory $employeeContractHistory);
}

?>