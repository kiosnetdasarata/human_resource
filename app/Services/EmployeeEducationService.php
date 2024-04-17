<?php

namespace App\Services;

use App\Interfaces\Employee\EmployeeRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Interfaces\Employee\EmployeeEducationRepositoryInterface;
use LogicException;

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
        $this->validateData($uuid, $request);

        $request['nip_id'] = $this->employee->find($uuid)->nip;

        return $this->employeeEducation->create($request);
    }

    public function update($uuid, $request)
    {
        $data = collect($request)->diffAssoc($this->employeeEducation->find($uuid))->all();

        $this->validateData($uuid, $data);

        $this->employeeEducation->update($uuid, $data);
    }

    public function delete($id)
    {
        return $this->employeeEducation->delete($id);
    }

    private function validateData($uuid, $request)
    {
        if (isset($request['tahun_lulus']) && $request['tahun_lulus'] > date('Y')) {
            throw new LogicException('tahun lulus tidak boleh lebih besar dibanding tahun sekarang');
        }

        $history = $this->employeeEducation->getAll($uuid);
        if (count($history)) {
            foreach ($history as $data) {
                $oldPendidikan = $data['pendidikan_terakhir'];
                $newPendidikan = $request['pendidikan_terakhir'];
                if ($newPendidikan == 'Sarjana') return;

                if ($oldPendidikan == $newPendidikan) {
                    throw new LogicException('pendidikan_terakhir jenjang '. $newPendidikan. ' sudah ada');
                }

                $arr = ['Sarjana', 'SMK/SMA', 'SMP'];
                if (array_search($oldPendidikan, $arr) < array_search($newPendidikan, $arr) &&
                    $data['tahun_lulus'] <= $request['tahun_lulus']) {
                    throw new LogicException ('tahun lulus tidak valid');
                }
            }
        }
    }
}
