<?php 

namespace App\Repositories;

use App\Models\Role;
use App\Models\JobVacancy;
use Illuminate\Support\Str;
use App\Interfaces\JobVacancyRepositoryInterface;
use Nette\Utils\Arrays;
use PhpParser\Node\Expr\Cast\Array_;

class JobVacancyRepository implements JobVacancyRepositoryInterface
{

    public function __construct(private JobVacancy $jobVacancy)
    {
    }

    public function getAll()
    {
        return $this->jobVacancy->get()->map(function ($e) {
            $applicant = collect($e->jobapplicant)->countBy('status_tahap');
            if ($e->is_intern == 1) {
                $trainee = collect($e->traineeship)->countBy('status_tahap');
                $applicant = $applicant->mergeRecursive($trainee)->map(function ($value, $key) {
                    return is_array($value) ? array_sum($value) : $value;
                });
            }
            $data = collect($e)->merge([
                'role' => $e->role->nama_jabatan,
                'branch' => $e->branch->nama_branch,
                'applicant_count' => count($e->jobapplicant) + count($e->traineeship),
                'applicant_sum' => $applicant->all()
            ])->except(['jobapplicant', 'traineeship']);
            return $data;
        });
    }

    public function getRole()
    {
        $roleId = $this->jobVacancy->where('is_active', 1)->select('role_id')->distinct()->get();
        return Role::whereIn('id', $roleId)->get();
    }

    public function find($id)
    {
        $data = collect($this->jobVacancy->with(['role', 'jobapplicant', 'traineeship'])->where('id', $id)->firstOrFail());
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
    
    public function update($id, $request)
    {
        return $this->jobVacancy->find($id)->update($request);
    }
    
    public function delete($id)
    {
        return $this->jobVacancy->find($id)->delete();
    }
    
}

?>