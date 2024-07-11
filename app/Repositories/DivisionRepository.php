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
        return $this->division
                    ->with('manager:nip,nama')
                    ->withCount('employee')
                    ->where('is_active', 1)
                    ->get();
    }

    public function find($id)
    {
        return $this->division
                    ->with(['role', 'manager'])
                    ->withCount('employee')
                    ->where([['id', $id], ['is_active', 1]])
                    ->firstOrFail();
    }

    public function findSlug($slug)
    {
        return $this->division
                    ->with(['role', 'manager'])
                    ->where([['kode_divisi', $slug], ['is_active', 1]])
                    ->firstOrFail();
    }

    public function create($request)
    {
        return $this->division->create($request);
    }

    public function update($division, $request)
    {
        return $division->update($request);
    }

    public function delete($division)
    {
        return $division->update(['is_active' => 0]);
    }
}
