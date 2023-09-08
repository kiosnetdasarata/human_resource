<?php

namespace App\Services;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use App\Interfaces\EmployeeRepositoryInterface;
use App\Interfaces\JobTitleRepositoryInterface;
use App\Interfaces\StatusLevelRepositoryInterface;

class EmployeeService
{
    public function __construct(
        private EmployeeRepositoryInterface $employeeRepositoryInterface,
        private JobTitleRepositoryInterface $jobTitleRepositoryInterface,
        private StatusLevelRepositoryInterface $statusLevelRepositoryInterface,
        )
    {}

    public function getAll()
    {
        return $this->employeeRepositoryInterface->getAll();
    }

    public function find($uuid)
    {
        return $this->employeeRepositoryInterface->find($uuid);
    }

    public function store($request)
    {
        $sales = collect($request);
        $sales->merge([
            'uuid' => Uuid::uuid4()->getHex(),
            'level_id' => $this->statusLevelRepositoryInterface->getLevelByCommission($sales->komisi_id),
        ]);
        return $this->employeeRepositoryInterface->create($request);
    }

    public function update($id, $request)
    {
        $old = $this->find($id);
        $sales = collect($request);

        if (isset($sales->komisi_id) && $sales->komisi_id != $old->komisi_id) {
            $sales->put('level_id', $this->statusLevelRepositoryInterface->getLevelByCommission($sales->komisi_id));
        }
        
        return $this->employeeRepositoryInterface->update($old, $request);
    }

    public function delete($data)
    {
        return $this->employeeRepositoryInterface->delete($data);
    }
}

?>
