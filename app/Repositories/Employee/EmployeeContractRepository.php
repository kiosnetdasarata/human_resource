<?php

namespace App\Repositories\Employee;

use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\EmployeeContractHistory;
use App\Interfaces\Employee\EmployeeContractRepositoryInterface;

class EmployeeContractRepository implements EmployeeContractRepositoryInterface
{

    public function __construct(
        private EmployeeContract $employeeContract,
        private EmployeeContractHistory $employeeContractHistory,
        private Employee $employee
    )
    {}

    public function getAll($id)
    {
        return $this->employee->findOrFail($id)->contractHistory;
    }

    public function find($id)
    {
        return $this->employee->findOrFail($id)->contract;
    }

    public function create($request)
    {
        $this->employeeContract->create($request);
        $this->employeeContractHistory->create($request);
    }

    public function update($id, $request)
    {
        $this->getAll($id)->last()->update($request);
        $this->find($id)->update($request);
    }

    public function delete($employeeContract)
    {
        return $employeeContract->delete();
    }
}
