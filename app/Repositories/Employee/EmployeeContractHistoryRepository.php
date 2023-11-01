<?php 

namespace App\Repositories\Employee;

use App\Models\EmployeeContractHistory;
use App\Interfaces\Employee\EmployeeContractHistoryRepositoryInterface;

class EmployeeContractHistoryRepository implements EmployeeContractHistoryRepositoryInterface
{

    public function __construct(private EmployeeContractHistory $employeeContractHistory)
    {
    }

    public function getAll()
    {
        return $this->employeeContractHistory->get();
    }

    public function find($uuid)
    {
        return $this->employeeContractHistory->where('nip_id', $uuid)->get();
    }
    
    public function create($request)
    {
        return $this->employeeContractHistory->create($request);
    }
    
    public function update($employeeContractHistory, $request)
    {
        return $employeeContractHistory->update($request);
    }
    
    public function delete($employeeContractHistory)
    {
        return $employeeContractHistory->delete();
    }
}

?>