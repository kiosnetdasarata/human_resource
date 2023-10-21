<?php 

namespace App\Repositories\Employee;

use App\Models\EmployeeContract;
use App\Interfaces\Employee\EmployeeContractRepositoryInterface;

class EmployeeContractRepository implements EmployeeContractRepositoryInterface
{

    public function __construct(private EmployeeContract $employeeContract)
    {
    }

    public function getAll()
    {
        return $this->employeeContract->with('employee')->get();
    }

    public function find($id)
    {
        return $this->employeeContract->find($id);
    }
    
    public function create($request)
    {
        return $this->employeeContract->create($request);
    }
    
    public function update($employeeContract, $request)
    {
        return $employeeContract->update($request);
    }
    
    public function delete($employeeContract)
    {
        return $employeeContract->delete();
    }
}

?>