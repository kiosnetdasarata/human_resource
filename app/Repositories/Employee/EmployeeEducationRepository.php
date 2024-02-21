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
        $employee = $this->employee->with('employeeEducation')->find($id);
        return $employee->employeeEducation;
    }

    public function find($id)
    {
        $employee =  $this->employee->with(['employeeEducation' => function ($query) {
                    $query->orderBy('pendidikan_terakhir')
                        ->orderBy('created_at')
                        ->first();
                }])->find($id);
        return $employee->employeeEducation->first();
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

?>