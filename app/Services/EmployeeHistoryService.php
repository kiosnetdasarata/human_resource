<?php

namespace App\Services;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use App\Interfaces\SalesRepositoryInterface;
use App\Interfaces\EmployeeRepositoryInterface;
use App\Interfaces\JobTitleRepositoryInterface;
use App\Interfaces\TechnicianRepositoryInterface;
use App\Interfaces\EmployeeHistoryRepositoryInterface;

class EmployeeHistoryService
{
    public function __construct(
        private EmployeeHistoryRepositoryInterface $employeeHistoryRepositoryInterface,
        private EmployeeRepositoryInterface $employeeRepositoryInterface,
        private JobTitleRepositoryInterface $jobTitleRepositoryInterface,
        private SalesRepositoryInterface $salesRepositoryInterface,
        private TechnicianRepositoryInterface $technicianRepositoryInterface,
    )
    {
    }

    public function getAll()
    {
        return $this->employeeHistoryRepositoryInterface->getAll();
    }

    public function find($uuid)
    {
        return $this->employeeHistoryRepositoryInterface->find($uuid);
    }

    private function getDivision($jobTitle)
    {
        return $this->jobTitleRepositoryInterface->find($jobTitle)->division_id;
    }

    public function store($request)
    {        
        DB::beginTransaction();

        try {
            $employee = $this->employeeRepositoryInterface->find($request['pgwi_nip']);
            $employeeHistory = collect($request)->merge([
                    'uuid' => Uuid::uuid4()->getHex(),
                    'tgl_mulai_kerja' => $employee->tgl_mulai_kerja,
                    'job_titles_id' => $employee->job_title_id,
                    'divisions_id' => $employee->job_title->division_id,
                ]);

            if ($employeeHistory->keterangan == 'Pindah Divisi') {
                $this->employeeRepositoryInterface->update($employee, [
                    'divisi_id' => $employeeHistory->after_divisi_id,
                    'job_title_id' => $this->getDivision($employeeHistory->after_divisi_id),
                ]);

                if ($employee->after_division_id == 4) {
                    $this->salesRepositoryInterface->create([
                        'karyawan_nip' => $employeeHistory->karyawan_nip,
                        'komisi_id' => $employeeHistory->komisi_id,
                    ]);
                } else if ($employee->after_division_id == 5) {
                    $this->salesRepositoryInterface->create([
                        'team_id' => $employeeHistory->team_id,
                        'employees_nip' => $employeeHistory->karyawan_nip,
                        'katim' => $employeeHistory->katim,
                    ]);
                } 

                if ($employee->divisions_id == 5) {
                    $this->technicianRepositoryInterface->delete($employee->technician());
                } else if ($employee->divisions_id == 4) {
                    $this->salesRepositoryInterface->delete($employee->sales());
                }

            } else {
                $employeeHistory->merge([
                    'after_job_title_id' => $employee->job_title_id,
                    'after_division_id' => $employee->job_title->division_id,
                ]);
            }

            dd($employeeHistory);
            $this->employeeHistoryRepositoryInterface->create($employeeHistory->toArray());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return $e;
        }       
    }

    public function update($id, $request)
    {        
        DB::beginTransaction();

        try {
            $old = $this->find($id);
            $employee = $this->employeeRepositoryInterface->find($request['pgwi_nip']);
            $employeeHistory = collect($request)->diffAssoc($old);

            if (isset($employeeHistory->after_job_title_id)) {
                $this->employeeRepositoryInterface->update($employee, [
                    'divisi_id' => $this,
                    'job_title_id' => $employeeHistory->after_job_title,
                ]);

                if ($employeeHistory->after_division_id == 4) {
                    $this->salesRepositoryInterface->create([
                        'uuid' => Uuid::uuid4()->getHex(),
                        'karyawan_nip' => $employeeHistory->karyawan_nip,
                        'komisi_id' => $employeeHistory->komisi_id,
                        'level_id' => $employeeHistory->level_id,
                    ]);
                } else if ($employeeHistory->after_division_id == 5) {
                    $this->salesRepositoryInterface->create([
                        'uuid' => Uuid::uuid4()->getHex(),
                        'team_id' => $employeeHistory->team_id,
                        'employees_nip' => $employeeHistory->karyawan_nip,
                        'katim' => $employeeHistory->katim,
                    ]);
                } 

                if ($employeeHistory->divisions_id == 5) {
                    $this->technicianRepositoryInterface->delete($employee->technician());
                } else if ($employeeHistory->divisions_id == 4) {
                    $this->salesRepositoryInterface->delete($employee->sales());
                }

            }
            dd($employeeHistory);
            $this->employeeHistoryRepositoryInterface->update($this->find($id), $employeeHistory->toArray());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return $e;
        }       
    }

    public function delete($data)
    {
        return $this->employeeHistoryRepositoryInterface->delete($this->find($data));
    }
}

?>
