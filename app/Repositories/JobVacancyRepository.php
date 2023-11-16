<?php 

namespace App\Repositories;

use App\Models\Role;
use App\Models\JobVacancy;
use Illuminate\Support\Str;
use App\Interfaces\JobVacancyRepositoryInterface;

class JobVacancyRepository implements JobVacancyRepositoryInterface
{

    public function __construct(private JobVacancy $jobVacancy)
    {
    }

    public function getAll()
    {
        return $this->jobVacancy->with(['role', 'branch'])->get()->map(function ($e) {
            $aplicant = collect($e->jobAplicant);
            if ($e->is_intern == 1) {
                $aplicant = $aplicant->merge($e->traineeship);
            }
            $data = collect($e)->merge([
                'screening' => count(array_filter($aplicant->all(), function($data) {
                    return $data['status_tahap'] == 'Screening';
                })),
                'fu' => count(array_filter($aplicant->all(), function($data) {
                    return $data['status_tahap'] == 'FU';
                })),
                'assesment' => count(array_filter($aplicant->all(), function($data) {
                    return $data['status_tahap'] == 'Assesment';
                })),
                'role' => $e->role->nama_jabatan,
                'branch' => $e->branch->nama_branch,
            ]);
            return $data->except(['jobapplicant', 'traineeship'])->all();
        });
    }

    public function getRole()
    {
        $roleId = $this->jobVacancy->where('is_active', 1)->select('role_id')->distinct()->get();
        return Role::whereIn('id', $roleId)->get();
    }

    public function find($id)
    {
        $data = collect($this->jobVacancy->with(['role', 'jobapplicant', 'traineeship'])->where('id', $id)->get()->firstOrFail());
        return $data['is_intern'] == 0 ? 
            $data->except('traineeship')->all() : $data->all();
    }

    public function findByRole($id)
    {
        return $this->jobVacancy->where('role_id', $id)->firstOrFail();
    }
    
    public function create($request)
    {
        $request = collect($request)->put('slug', Str::slug($request['title'], '_'));
        if ($request['close_date'] <= $request['open_date']) {
            throw new \Exception('close date tidak sesuai dengan open date');
        }
        return $this->jobVacancy->create($request->all());
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