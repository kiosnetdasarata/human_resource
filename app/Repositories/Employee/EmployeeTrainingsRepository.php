<?php 

namespace App\Repositories;

use App\Models\EmployeeTrainings;
use App\Interfaces\Employee\EmployeeTrainingsRepositoryInterface;

class EmployeeTrainingsRepository implements EmployeeTrainingsRepositoryInterface
{

    public function __construct(private EmployeeTrainings $employeeTrainings)
    {
    }

    public function getAll()
    {
        return $this->employeeTrainings->get();
    }

    public function find($id)
    {
        return $this->employeeTrainings->find($id);
    }
    
    public function create($request)
    {
        return $this->employeeTrainings->create($request);
    }
    
    public function update($employeeTrainings, $request)
    {
        return $employeeTrainings->update($request);
    }
    
    public function delete($employeeTrainings)
    {
        return $employeeTrainings->delete();
    }
}

?>