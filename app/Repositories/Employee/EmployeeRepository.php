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
                'created_at' => $e->role->created_at,
                'updated_at' => $e->role->updated_at
            ];
        });
    }

    public function find($uuid)
    {
        return $this->employee->where('id', $uuid)->firstOrFail();
    }

    public function show($uuid)
    {
        return $this->employee->with(['employeeCI',
                'role',
                'employeeContractHistory',
                'employeeEducation' => function ($query) {
                    $query->orderBy('pendidikan_terakhir')
                        ->orderBy('created_at')
                        ->first();
                },])
                ->where('id', $uuid)
                ->firstOrFail();
    }

    public function findByDivision($division) 
    {
        return $this->employee->where('division_id', $division)->get();
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