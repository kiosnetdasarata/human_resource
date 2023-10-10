<?php 

namespace App\Repositories\Employee;

use App\Models\EmployeeHistory;
use App\Interfaces\Employee\EmployeeHistoryRepositoryInterface;

class EmployeeHistoryRepository implements EmployeeHistoryRepositoryInterface
{

    public function __construct(private EmployeeHistory $employeeHistory)
    {
    }

    public function getAll()
    {
        return $this->employeeHistory->get();
    }

    public function find($id)
    {
        return $this->employeeHistory->find($id);
    }
    
    public function create($request)
    {
        return $this->employeeHistory->create($request);
    }
    
    public function update($employeeHistory, $request)
    {
        return $employeeHistory->update($request);
    }
    
    public function delete($employeeHistory)
    {
        return $employeeHistory->delete();
    }
}

?>