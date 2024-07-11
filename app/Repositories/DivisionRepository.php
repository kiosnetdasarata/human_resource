<?php

namespace App\Repositories;

use App\Models\Division;
use App\Interfaces\DivisionRepositoryInterface;
use App\Models\Employee;

class DivisionRepository implements DivisionRepositoryInterface
{

    public function __construct(private Division $division, protected Employee $employee)
    {
    }

    public function getAll()
    {
        // return $this->division
        //             ->with(['role:id', 'manager:nip,nama'])
        //             ->get()
        //             ->map(function ($e) {
        //                 return [
        //                     'id'                => $e->id,
        //                     'nama_divisi'       => $e->nama_divisi,
        //                     'kode_divisi'       => $e->kode_divisi,
        //                     'supervisor'        => $e->manager->nama,
        //                     'no_tlpn'           => $e->no_tlpn,
        //                     'jumlah_jabatan'    => count($e->role),
        //                     'created_at'        => $e->created_at,
        //                     'updated_at'        => $e->updated_at
        //                 ];
        //             });

        return $this->division->with('manager:nip,nama')->withCount('employee')->get();
    }

    public function find($id)
    {
        // return $this->division->find($id)->load(['role', 'manager']);
        return $this->division->find($id)->with(['role', 'manager'])->withCount('employee')->first();
    }

    public function findSlug($slug)
    {
        return $this->division
                    ->with(['role', 'manager'])
                    ->where('kode_divisi', $slug)
                    ->firstOrFail();
    }

    public function create($request)
    {
        return $this->division->create($request);
    }

    public function update($old, $request)
    {
        return $old->update($request);
    }

    public function delete($division)
    {
        return $division->delete();
    }

    public function getManagers()
    {
        return $this->employee->whereNotHas('leaderOf')->get();
    }
}
