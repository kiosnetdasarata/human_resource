<?php 

namespace App\Repositories;

use App\Models\Role;
use App\Models\JobVacancy;
use App\Interfaces\JobVacancyRepositoryInterface;

class JobVacancyRepository implements JobVacancyRepositoryInterface
{

    public function __construct(private JobVacancy $jobVacancy)
    {
    }

    public function getAll()
    {
        return $this->jobVacancy->with('role')->get();
    }

    public function getRole()
    {
        $roleId = $this->jobVacancy->where('is_active', 1)->select('role_id')->distinct()->get();
        return Role::whereIn('id', $roleId)->get();
    }

    public function find($id)
    {
        $jobVacancy = $this->jobVacancy->with('role')->findOrFail($id)->map(function ($e) {
            return[
                'id' => $e->id,
                'nama_divisi' => $e->nama_divisi,
                'jumlah_jabatan' => count($e->role),
                'created_at' => $e->created_at,
                'updated_at' => $e->updated_at
            ];
        });
        dd($jobVacancy);
    }

    public function findByRole($id)
    {
        return $this->jobVacancy->where('role_id', $id)->firstOrFail();
    }
    
    public function create($request)
    {
        return $this->jobVacancy->create($request);
    }
    
    public function update($jobVacancy, $request)
    {
        return $jobVacancy->update($request);
    }
    
    public function delete($jobVacancy)
    {
        return $jobVacancy->delete();
    }
    
}

?>