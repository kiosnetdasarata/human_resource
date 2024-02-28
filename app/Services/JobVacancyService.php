<?php 

namespace App\Services;

use App\Interfaces\ArchiveJobApplicantRepositoryInterface;
use App\Interfaces\Internship\TraineeshipRepositoryInterface;
use App\Interfaces\JobApplicantRepositoryInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Interfaces\JobVacancyRepositoryInterface;

class JobVacancyService
{
    public function __construct(
        private JobVacancyRepositoryInterface $jobVacancy,
        private ArchiveJobApplicantRepositoryInterface $archiveJobApplicant,
        private JobApplicantRepositoryInterface $jobApplicant,
        private TraineeshipRepositoryInterface $traineeship) { }

    public function getAll()
    {
        return $this->jobVacancy->getAll();
    }

    public function getRole()
    {
        return $this->jobVacancy->getRole();
    }

    public function find($id)
    {
        return $this->jobVacancy->find($id);
    }

    public function getApplicant($id)
    {
        return $this->jobVacancy->getJobApplicants($id);
    }

    public function getTraineeships($id) 
    {
        return $this->jobVacancy->getTraineeships($id);
    }

    public function findByRole($id)
    {
        return $this->jobVacancy->findByRole($id);
    }

    public function create($request)
    {
        $jobVacancy = $this->jobVacancy->findSameRoleOnBranch($request['role_id'], $request['branch_company_id']);
        if ($jobVacancy) throw new \Exception('duplikat role');

        $request = collect($request)->merge([
            'title' => Str::title($request['title']),
            'slug' => Str::slug($request['title'], '_'),
        ]);
        if ($request['close_date'] <= $request['open_date']) {
            throw new \Exception('close date tidak sesuai dengan open date');
        }
        return $this->jobVacancy->create($request);
    }

    public function update($id, $request)
    {
        $jobVacancy = $this->jobVacancy->find($id);
        $request = collect($request)->diffAssoc($jobVacancy);
        if (isset($request['title'])) {
            $request = $request->merge([
                'title' => Str::title($request['title']),
                'slug' => Str::slug($request['title'], '_'),
            ]);
        }
        if (isset($request['role_id'])) {
            $same = $this->jobVacancy->findSameRoleOnBranch($request['role_id'], $request['branch_company_id']);
            if ($same) throw new \Exception('duplikat role');
        }
        return DB::transaction(function () use ($jobVacancy,$request) {
            $this->jobVacancy->update($jobVacancy,$request->all());
            $vacancy = $this->jobVacancy->find($jobVacancy->id);
            if ($vacancy['close_date'] <= $vacancy['open_date']) {
                throw new \Exception('close date tidak sesuai dengan open date');
            }
        });
    }

    public function delete($id)
    {
        $jobVacancy = $this->jobVacancy->find($id);
        return DB::transaction(function() use ($jobVacancy) {
            if ($jobVacancy->jobapplicant->isNotEmpty()) {
                foreach ($jobVacancy->jobapplicant as $applicant) {
                    $data = collect($applicant)->merge([
                        'tanggal_lamaran' => $applicant->created_at,
                        'keterangan' => 'dihapus karena job vacancy terhapus',
                        'status_lamaran' => $applicant->status_tahap,
                        'is_intern' => 0,                        
                        'role_id' => $jobVacancy->role_id,
                        
                    ]);
                    $this->archiveJobApplicant->create($data->all());
                    $this->jobApplicant->delete($applicant);
                }
            }
            if ($jobVacancy->traineeship->isNotEmpty()) {
                foreach ($jobVacancy->traineeship as $applicant) {                    
                    $data = collect($applicant)->merge([
                        'tanggal_lamaran' => $applicant->created_at,
                        'keterangan' => 'dihapus karena job vacancy terhapus',
                        'status_lamaran' => $applicant->status_tahap,
                        'is_intern' => 1,
                        'no_tlpn' => $applicant->nomor_telepone,
                        'role_id' => $jobVacancy->role_id,
                    ]);
                    $this->archiveJobApplicant->create($data->all());
                    $this->traineeship->delete($applicant);
                }
            }
            $this->jobVacancy->delete($jobVacancy);
        });
    }

}