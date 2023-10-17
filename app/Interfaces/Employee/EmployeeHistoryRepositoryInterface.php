<?php 

namespace App\Interfaces\Employee;

use App\Models\EmployeeHistory;

interface EmployeeHistoryRepositoryInterface
{
    public function getAll();
    public function find($mitraid);
    public function create($request);
    public function update(EmployeeHistory $employeeHistory, $request);
    public function delete(EmployeeHistory $employeeHistory);
}

?>