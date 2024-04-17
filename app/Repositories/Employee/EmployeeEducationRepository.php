<?php

namespace App\Repositories\Employee;

use App\Models\Employee;
use App\Models\EmployeeEducation;
use App\Interfaces\Employee\EmployeeEducationRepositoryInterface;

class EmployeeEducationRepository implements EmployeeEducationRepositoryInterface
{

    public function __construct(
        private EmployeeEducation $employeeEducation,
        private Employee $employee
    )
    {
    }

    public function getAll($id)
    {
        return $this->employee->findOrFail($id)->educationHistory;
    }

    public function find($id)
    {
        return$this->employee->findOrFail($id)->education;
    }

    public function create($request)
    {
        return $this->employeeEducation->create($request);
    }

    public function update($id, $request)
    {
        return $this->find($id)->update($request);
    }

    public function delete($id)
    {
        return $this->find($id)->delete();
    }
}
