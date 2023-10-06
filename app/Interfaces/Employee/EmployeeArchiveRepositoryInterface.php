<?php 

namespace App\Interfaces\Employee;

use App\Models\EmployeeArchive;

interface EmployeeArchiveRepositoryInterface
{
    public function getAll();
    public function find($mitraid);
    public function create($request);
    public function update(EmployeeArchive $employeeArchive, $request);
    public function delete(EmployeeArchive $employeeArchive);
}

?>