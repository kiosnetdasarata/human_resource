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
            if ($e->is_intern == 0) {
                $aplicant = collect($e->jobapplicant);
            } else $aplicant = collect($e->traineeship);
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
        return $this->jobVacancy->with(['role', 'jobApplicant', 'traineeship'])->where('id', $id)->get()->firstOrFail();
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