<?php 

namespace App\Repositories;

use App\Models\Employee;
use App\Interfaces\Employee\EmployeeRepositoryInterface;

class EmployeeRepository implements EmployeeRepositoryInterface
{

    public function __construct(private Employee $employee)
    {
    }

    public function getAll()
    {
        return $this->employee->get()->map(function ($e) {
            return [
                'nip_pgwi' => $e->nip_pgwi,
                'nama' => $e->nama,
                'divisi' => $e->division->nama_divisi,
                'jabatan' => $e->jobTitle->nama_jabatan,
            ];
        });
    }

    public function find($uuid, $table)
    {
        return $this->employee->where($table, $uuid)->firstOrFail();
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