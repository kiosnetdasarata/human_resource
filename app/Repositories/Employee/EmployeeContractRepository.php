<?php 

namespace App\Repositories\Employee;

use App\Models\Employee;
use App\Models\EmployeeContract;
use Illuminate\Support\Facades\DB;
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
        $employee = $this->employee->with('employeeContractHistory')->find($id);
        return $employee->employeeContractHistory;
    }

    public function find($id)
    {
        $employee = $this->employee->with('employeeContract')->find($id);
        return $employee->employeeContract;
    }
    
    public function create($request)
    {
        return DB::transaction(function () use ($request) {            
            $this->employeeContract->create($request);        
            $this->employeeContractHistory->create($request);
        });
    }
    
    public function update($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {            
            $this->getAll($id)->last()->update($request);
            $this->find($id)->update($request);
        });
    }
    
    public function delete($employeeContract)
    {
        return $employeeContract->delete();
    }
}