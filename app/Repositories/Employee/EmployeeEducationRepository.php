<?php 

namespace App\Repositories\Employee;

use App\Models\EmployeeEducation;
use App\Interfaces\Employee\EmployeeEducationRepositoryInterface;

class EmployeeEducationRepository implements EmployeeEducationRepositoryInterface
{

    public function __construct(private EmployeeEducation $employeeEducation)
    {
    }

    public function getAll()
    {
        return $this->employeeEducation->with('employee')->get();
    }

    public function find($id)
    {
        return $this->employeeEducation->with('employee')->find($id);
    }
    
    public function create($request)
    {
        return $this->employeeEducation->create($request);
    }
    
    public function update($employeeEducation, $request)
    {
        return $employeeEducation->update($request);
    }
    
    public function delete($employeeEducation)
    {
        return $employeeEducation->delete();
    }
}

?>