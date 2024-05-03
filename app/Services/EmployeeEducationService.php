<?php

namespace App\Services;

use LogicException;
use App\Interfaces\Employee\EmployeeRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Interfaces\Employee\EmployeeEducationRepositoryInterface;

class EmployeeEducationService
{
    public function __construct(
        private EmployeeEducationRepositoryInterface $employeeEducation,
        private EmployeeRepositoryInterface $employee
    )
    {
        //
    }

    public function get($uuid)
    {
        return $this->employeeEducation->getAll($uuid);
    }

    public function find($uuid)
    {
        $data = $this->employeeEducation->find($uuid);

        if (!$data) throw new ModelNotFoundException();
        else return $data;
    }

    public function store($uuid, $request) {
        $employee = $this->employee->find($uuid);
        $this->validateData($employee, $request);

        return $this->employeeEducation->create($request);
    }

    public function update($uuid, $request)
    {
        $data = collect($request)->diffAssoc($this->find($uuid))->all();

        $this->validateData($uuid, $data);

        $this->employeeEducation->update($uuid, $data);
    }

    public function delete($id)
    {
        return $this->employeeEducation->delete($id);
    }

    private function validateData($employee, $request)
    {
        if (isset($request['tahun_lulus']) && $request['tahun_lulus'] > date('Y')) {
            throw new LogicException('tahun lulus tidak boleh lebih besar dibanding tahun sekarang');
        }

        $history = $employee->educationHistory;
        if (count($history)) {
            foreach ($history as $data) {
                $newPendidikan = $request['pendidikan_terakhir'];
                if ($newPendidikan == 'Sarjana') return;

                $oldPendidikan = $data['pendidikan_terakhir'];

                $arr = [
                    'Sarjana'   => 3,
                    'SMA'       => 2,
                    'SMK'       => 2,
                    'SMP'       => 1
                ];

                if (($arr[$oldPendidikan] - $arr[$newPendidikan]) * ($data['tahun_lulus'] - $request['tahun_lulus']) <= 0) {
                    throw new LogicException ('tahun lulus tidak valid');
                }
            }
        }
    }
}
