<?php 

namespace App\Repositories;

use App\Models\Role;
use Nette\Utils\Arrays;
use App\Models\JobVacancy;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Cast\Array_;
use App\Interfaces\JobVacancyRepositoryInterface;
use App\Models\ArchiveJobApplicant;

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
        return $this->jobVacancy->where('role_id', $id)->get();
    }
    
    public function create($request)
    {
        $jobVacancy = $this->jobVacancy->where('role_id', $request['role_id'])->where('branch_company_id', $request['branch_company_id'])->first();
        if ($jobVacancy) throw new \Exception('duplikat role');

        $request = collect($request)->put('slug', Str::slug($request['title'], '_'));
        if ($request['close_date'] <= $request['open_date']) {
            throw new \Exception('close date tidak sesuai dengan open date');
        }
        return $this->jobVacancy->create($request->all());
    }
    
    public function update($id, $request)
    {
        $jobVacancy = $this->jobVacancy->find($id);
        $request = collect($request)->diffAssoc($jobVacancy);
        if (isset($request['title'])) {
            $request->put('slug', Str::slug($request['title'], '_'));
        }
        if (isset($request['role_id'])) {
            $jobVacancy = $this->jobVacancy->where('role_id', $request['role_id'])->where('branch_company_id', $request['branch_company_id'])->first();
            if ($jobVacancy) throw new \Exception('duplikat role');
        }
        if ($request['close_date'] <= $request['open_date']) {
            throw new \Exception('close date tidak sesuai dengan open date');
        }
        return $this->jobVacancy->find($id)->update($request);
    }
    
    public function delete($id)
    {
        $jobVacancy = $this->jobVacancy->findOrFail($id);
        if ($jobVacancy->jobapplicant->isNotEmpty()) {
            DB::transaction(function() use ($jobVacancy) {
                foreach ($jobVacancy->jobapplicant as $aplicant) {
                    ArchiveJobApplicant::create($aplicant);
                    $aplicant->delete();
                }
            });
        }
        return $jobVacancy->delete();
    }    
}

?>