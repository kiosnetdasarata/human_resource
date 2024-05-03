<?php

namespace App\Services;

use LogicException;
use Illuminate\Support\Facades\DB;
use App\Interfaces\AllowanceRepositoryInterface;
use App\Interfaces\Employee\EmployeeRepositoryInterface;

class AllowanceService
{
    public function __construct(
        private AllowanceRepositoryInterface $allowance,
        private EmployeeRepositoryInterface $employee,
    )
    {
        //
    }

    public function findByEmployee($uuid)
    {
        return $this->employee->allowance($uuid);
    }

    public function eligibleAllowance($uuid)
    {
        return $this->allowance->getWithCondition($uuid);
    }

    public function createEmployee($uuid, $request)
    {
        $employee = $this->employee->find($uuid);
        return DB::transaction(function () use ($request, $employee) {
            $allowances = $this->allowance->find($request->keys());
            $this->validate($allowances, $employee);

            $this->employee->attachAllowance($employee, $request);
            $this->allowance->attachLevel($allowances, $employee['nip'], 0);
        });
    }

    private function validate($allowances, $employee)
    {
        $level = $employee->level_id;
        $masa = $employee->getMasaKerjaAttribute();

        foreach($allowances as $allowance) {
            [$min, $max] = [$allowance->min_level, $allowance->max_level];

            if ($min < $level || $level < $max) {
                throw new LogicException('Level tidak sesuai');
            }

            switch ($allowance->id) {
                case 1: case 2: case 3: if ($masa > 6) break;
                case 4: if ($masa > 36) break;
                case 5: if ($masa > 60) break;
                default: throw new LogicException('syarat tidak sesuai');
            }
        }
    }

    public function delete($request, $uuid)
    {
        $employee = $this->employee->find($uuid);
        return DB::transaction(function() use($employee, $request) {
            $this->employee->detachAllowance($employee, $request['allowance_id']);
            $this->allowance->detachLevel($request['allowance_id'], $employee->nip);
        });
    }
}
