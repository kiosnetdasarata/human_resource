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
                ->select(['id,nip,nama,role_id,created_at,updated_at'])
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

    public function allowance($uuid)
    {
        return $this->find($uuid)->allowance;
    }

    public function getArchive()
    {
        return $this->archive->get();
    }

    public function show($uuid, $var)
    {
        return $this->employee->where($var, $uuid)->firstOrFail();
    }

    public function find($uuid)
    {
        return $this->employee
                    ->findOrFail($uuid)
                    ->load([
                        'branch',
                        'level',
                        'employeeCI',
                        'role.division',
                        'contractsHistory',
                        'village.district.regency.province',
                        'education'
                    ]);
    }

    // public function findBySlug($slug)
    // {
    //     return $this->employee->where('slug', 'LIKE','%'. $slug.'%')->get();
    // }

    public function findWithTrashes()
    {
        return $this->employee->withTrashed()->get();
    }

    // public function findBySlugWithTrashes($slug)
    // {
    //     return $this->employee->withTrashed()->where('slug', 'LIKE','%'. $slug.'%')->get();
    // }

    public function getManager($request)
    {
        return $this->employee
                ->whereRelation('level', function ($q) {
                    $q->whereIn('kode_level', [2,3]);
                })
                ->whereNotIn('nip', function ($q) use ($request) {
                    $q->select('manager_divisi')
                        ->from('divisions')
                        ->when($request->has('division'), function ($q) use ($request) {
                            return $q->where('id', '!=', $request->query('division'));
                        });
                })
                ->get();
    }

    public function getByDivision($divisionId)
    {
        return $this->employee
                    ->whereRelation('division', 'divisi_id', '=', $divisionId)
                    ->get();
    }

    public function create($request)
    {
        return $this->employee->create($request);
    }

    public function attachAllowance($employee, $allowances)
    {
        return $employee->allowance()->syncWithoutDetaching($allowances);
    }

    public function detachAllowance($employee, $allowances)
    {
        return $employee->allowance()->detach($allowances);
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
