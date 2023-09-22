<?php

namespace App\Services;

use Ramsey\Uuid\Uuid;
use App\Interfaces\SalesRepositoryInterface;
use App\Interfaces\StatusLevelRepositoryInterface;

class SalesService
{
    public function __construct(
        private SalesRepositoryInterface $salesRepositoryInterface,
        private StatusLevelRepositoryInterface $statusLevelRepositoryInterface
    )
    {
    }

    public function getAll()
    {
        return $this->salesRepositoryInterface->getAll();
    }

    public function find($uuid)
    {
        return $this->salesRepositoryInterface->find($uuid);
    }

    public function store($request)
    {
        $sales = collect($request)->merge([
            'uuid' => Uuid::uuid4()->getHex(),
            'level_id' => $this->statusLevelRepositoryInterface->getCommission($request['komisi_id'])->level_id,
        ]);

        return $this->salesRepositoryInterface->create($sales->all());
    }

    public function update($id, $request)
    {
        $old = $this->find($id);
        $sales = collect($request->validate())->diffAssoc($old);

        if (isset($sales['komisi_id'])) {
            $sales->put('level_id', $this->statusLevelRepositoryInterface->getCommission($sales['komisi_id'])->level_id);
        }
        
        return $this->salesRepositoryInterface->update($old, $sales->all());
    }

    public function delete($data)
    {
        return $this->salesRepositoryInterface->delete($this->find($data));
    }
}

?>
