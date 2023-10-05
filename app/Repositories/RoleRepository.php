<?php 

namespace App\Repositories;

use App\Models\Role;
use App\Interfaces\RoleRepositoryInterface;

class RoleRepository implements RoleRepositoryInterface
{

    public function __construct(private Role $role)
    {
    }

    public function getAll($division = null)
    {
        $query = $this->role;
        if ($division != null) {
            $query = $query->where('divisi_id', $division);
        }
        return $query->with('divisi')->get();
    }

    public function find($kodeJabatan)
    {
        return $this->role->where('divisi_id', $kodeJabatan)->firstOrFail();
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

?>