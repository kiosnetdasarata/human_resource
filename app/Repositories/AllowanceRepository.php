<?php

namespace App\Repositories;

use Ramsey\Uuid\Uuid;
use App\Models\Employee;
use App\Models\AllowanceCategory;
use App\Interfaces\AllowanceRepositoryInterface;

class AllowanceRepository implements AllowanceRepositoryInterface
{
    public function __construct(
        private AllowanceCategory $allowance,
        private Employee $employee
    )
    {
        //
    }

    public function get()
    {
        return $this->allowance->get();
    }

    public function getWithCondition($uuid)
    {
        $data = $this->employee->find($uuid);
        $level = $data->level_id;
        $masa = $data->getMasaKerjaAttribute();
        $listOld = $data->allowance->pluck('id');

        return $this->allowance
                    ->where('min_level', '>=', $level)
                    ->where('max_level', '<=', $level)
                    ->whereNotIn('id', $listOld)
                    ->where(function ($q) use ($masa) {
                        if ($masa > 60) return $q->whereNot('id', 6);
                        if ($masa > 36) return $q->whereNotIn('id', [5,6]);
                        if ($masa > 6) return $q->whereNotIn('id', [4,5,6]);
                        return $q->where('id', [1,6]);
                    })->get();
    }

    public function find($id)
    {
        return $this->allowance->findOrFail($id);
    }

    public function getByEmployee($uuid)
    {
        return $this->allowance->whereRelation('employee', $uuid);
    }
}
