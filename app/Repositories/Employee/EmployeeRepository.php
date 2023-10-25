<?php 

namespace App\Repositories\Employee;

use App\Models\Employee;
use App\Interfaces\Employee\EmployeeRepositoryInterface;

class EmployeeRepository implements EmployeeRepositoryInterface
{

    public function __construct(private Employee $employee)
    {
    }

    public function getAll()
    {
        return $this->employee->with('role')->get()->map(function ($e) {
            return [
                'uuid' => $e->id,
                'nip_pgwi' => $e->nip,
                'nama' => $e->nama,
                'divisi' => $e->role->division->nama_divisi,
                'jabatan' => $e->role->nama_jabatan,
            ];
        });
    }

    public function find($uuid, $table)
    {
        return $this->employee->with(['employeeCI', 'employeeContract', 'role'])
                ->where($table, $uuid)->get()->firstOrFail();
    }

    public function findBySlug($slug)
    {
        return $this->employee->where('slug', 'LIKE','%'. $slug.'%')->get();
    }

    public function findWithTrashes()
    {
        return $this->employee->withTrashed()->get();
    }

    public function findBySlugWithTrashes($slug)
    {
        return $this->employee->withTrashed()->where('slug', 'LIKE','%'. $slug.'%')->get();
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