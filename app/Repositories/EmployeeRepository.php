<?php 

namespace App\Repositories;

use App\Models\Employee;
use App\Interfaces\EmployeeRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmployeeRepository implements EmployeeRepositoryInterface
{

    public function __construct(private Employee $employee)
    {
    }

    public function getAll()
    {
        return $this->employee->get();
    }

    public function find($uuid)
    {
        return $this->employee->where('uuid', $uuid)->firstOrFail();
    }

    public function findSlug($slug)
    {
        return $this->employee->where('slug', 'LIKE', $slug.'%')->get();
    }
    
    public function create($request)
    {
        return $this->employee->create($request);
    }
    
    public function update($employee, $request)
    {
        return $employee->update($request);
    }
    
    public function delete($employee)
    {
        return $employee->delete();
    }
    
}

?>