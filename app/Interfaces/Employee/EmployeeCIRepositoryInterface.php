<?php 

namespace App\Interfaces\Employee;

use App\Models\EmployeeConfidentalInformation;

interface EmployeeCIRepositoryInterface
{
    public function getAll();
    public function find($nip);
    public function create($request);
    public function update(EmployeeConfidentalInformation $employeeConfidentalInformation, $request);
    public function delete(EmployeeConfidentalInformation $employeeConfidentalInformation);
}

?>