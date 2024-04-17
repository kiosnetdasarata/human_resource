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
        $query = $this->role->query();
        $query->when($divisionId, function ($q) use ($divisionId) {
            return $q->where('status', $divisionId);
        });

        return $query->with(['division', 'level'])->get();
    }

    public function find($id)
    {
        return $this->role->findOrFail($id);
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
        return $role->delete();
    }

}
