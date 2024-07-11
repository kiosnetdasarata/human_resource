<?php

namespace App\Repositories;

use App\Models\Role;
use App\Interfaces\RoleRepositoryInterface;

class RoleRepository implements RoleRepositoryInterface
{

    public function __construct(private Role $role)
    {
    }

    public function getAll($divisionId = null)
    {
        return $this->role
                    ->with(['division:id,nama_divisi'])
                    ->withCount('employee')
                    ->where('is_active', 1)
                    ->get();
    }

    public function find($id)
    {
        return $this->role->where([['id' => $id], ['is_active', 1]]);
    }

    public function create($request)
    {
        return $this->role->create($request);
    }

    public function update($role, $request)
    {
        return $role->update($request);
    }

    public function delete($role)
    {
        return $role->update(['is_active' => 0]);
    }
}
