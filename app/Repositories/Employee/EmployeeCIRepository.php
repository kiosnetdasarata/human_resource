<?php 

namespace App\Repositories\Employee;

use App\Models\EmployeeConfidentalInformation;
use App\Interfaces\Employee\EmployeeCIRepositoryInterface;

class EmployeeCIRepository implements EmployeeCIRepositoryInterface
{

    public function __construct(private EmployeeConfidentalInformation $employeeConfidentalInformation)
    {
    }

    public function getAll()
    {
        return $this->employeeConfidentalInformation->get();
    }

    public function find($uuid)
    {
        return $this->employeeConfidentalInformation->findOrFail($uuid);
    }

    public function create($request)
    {
        return $this->employeeConfidentalInformation->create($request);
    }
    
    public function update($employeeConfidentalInformation, $request)
    {
        return $employeeConfidentalInformation->update($request);
    }
    
    public function delete($employeeConfidentalInformation)
    {
        return $employeeConfidentalInformation->delete();
    }
}

?>