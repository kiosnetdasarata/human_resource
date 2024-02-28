<?php 

namespace App\Repositories;

use App\Interfaces\ArchiveJobApplicantRepositoryInterface;
use App\Models\ArchiveJobApplicant;

class ArchiveJobApplicantRepository implements ArchiveJobApplicantRepositoryInterface
{
    public function __construct(private ArchiveJobApplicant $archiveJobApplicant) { }
    
    public function getAllJobApplicant()
    {
        return $this->archiveJobApplicant->get();
    }

    public function getAllTranieeship()
    {
        return $this->archiveJobApplicant->get();
    }

    public function getTraineeshipByJobVacancy($vacancyId)
    {
        return $this->archiveJobApplicant
                    ->where('is_intern', 1)
                    ->where('vacancy_id', $vacancyId)
                    ->get();
    }

    public function getJobApplicantByJobVacancy($vacancyId)
    {
        return $this->archiveJobApplicant
                    ->where('is_intern', 0)
                    ->where('vacancy_id', $vacancyId)
                    ->get();
    }

    public function find($id)
    {
        return $this->archiveJobApplicant->where('id', $id)->firstOrFail();
    }

    public function create($request)
    {
        return $this->archiveJobApplicant->create($request);
    }
}
