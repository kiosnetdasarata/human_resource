<?php 

namespace App\Repositories\Employee;

use App\Models\EmployeeArchive;
use App\Interfaces\Employee\EmployeeArchiveRepositoryInterface;

class EmployeeArchiveRepository implements EmployeeArchiveRepositoryInterface
{

    public function __construct(private EmployeeArchive $employeeArchive)
    {
    }

    public function getAll()
    {
        return $this->employeeArchive->get();
    }

    public function find($id)
    {
        return $this->employeeArchive->find($id);
    }
    
    public function create($request)
    {
        return $this->employeeArchive->create($request);
    }
    
    public function update($employeeArchive, $request)
    {
        return $employeeArchive->update($request);
    }
    
    public function delete($employeeArchive)
    {
        return $employeeArchive->delete();
    }
}

?>