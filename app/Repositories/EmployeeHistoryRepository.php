<?php 

namespace App\Repositories;

use App\Models\EmployeeHistory;
use App\Interfaces\EmployeeHistoryRepositoryInterface;

class EmployeeHistoryRepository implements EmployeeHistoryRepositoryInterface
{

    public function __construct(private EmployeeHistory $employeeHistory)
    {
    }

    public function getAll()
    {
        return $this->employeeHistory->get();
    }

    public function find($uuid)
    {
        return $this->employeeHistory->find($uuid);
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