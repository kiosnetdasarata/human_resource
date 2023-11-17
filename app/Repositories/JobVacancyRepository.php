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
            $applicant = collect($e->jobapplicant)->countBy('status_tahap');
            if ($e->is_intern == 1) {
                $trainee = $applicant->merge(collect($e->traineeship)->countBy('status_tahap'));
                $applicant = $applicant->merge($trainee)->map(function ($value, $key) use ($applicant, $trainee) {
                    return $applicant->has($key) ? $applicant[$key] + $value : $value;
                });
            }
            return $data = collect($e)->merge($applicant,[
                'role' => $e->role->nama_jabatan,
                'branch' => $e->branch->nama_branch,
            ])->except(['jobapplicant', 'traineeship'])->all();
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