<?php

namespace App\Repositories\Employee;

use App\Models\Employee;
use App\Models\EmployeeArchive;
use App\Interfaces\Employee\EmployeeRepositoryInterface;

class EmployeeRepository implements EmployeeRepositoryInterface
{

    public function __construct(
        private Employee $employee,
        private EmployeeArchive $archive
    )
    {
        //
    }

    public function getAll()
    {
        return $this->employee
                ->with(['role:id,nama_jabatan,divisi_id','role.division:id,nama_divisi'])
                ->get()
                ->map(function ($e) {
                    return [
                        'uuid' => $e->id,
                        'nip_pgwi' => $e->nip,
                        'nama' => $e->nama,
                        'divisi' => $e->role->division->nama_divisi,
                        'jabatan' => $e->role->nama_jabatan,
                        'created_at' => $e->created_at,
                        'updated_at' => $e->updated_at
                    ];
                });
    }

    public function getArchive()
    {
        return $this->archive->get();
    }

    public function find($uuid, $var = 'id')
    {
        return $this->employee->where($var, $uuid)->firstOrFail();
    }

    public function show($uuid)
    {
        return $this->employee
                    ->findOrFail($uuid)
                    ->load([
                        'employeeCI',
                        'role',
                        'contractsHistory',
                        'education'
                    ]);
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

    public function getManager()
    {
        return $this->employee
                ->where('level_id', 3)
                ->whereNotIn('nip', function($query) {
                    $query->select('manager_divisi')->from('divisions');;
                })
                ->get();
    }

    public function getByDivision($divisionId)
    {
        return $this->employee
                ->whereHas('role', function ($query) use ($divisionId) {
                    $query->where('divisi_id', $divisionId);
                })
                ->get();
    }

    public function create($request)
    {
        return $this->employee->create($request);
    }

    public function createArchive($request)
    {
        return $this->archive->create($request);
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
