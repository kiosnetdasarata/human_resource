<?php

namespace App\Services;

use Ramsey\Uuid\Uuid;
use App\Helpers\FileHelper;
use Illuminate\Support\Facades\DB;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\Employee\EmployeeRepositoryInterface;
use App\Interfaces\Employee\EmployeeContractRepositoryInterface;

class EmployeeContractService
{
    public function __construct(
        private EmployeeContractRepositoryInterface $contract,
        private EmployeeRepositoryInterface $employee,
        private UserRepositoryInterface $user,
        private FileHelper $file
    )
    {
        //
    }

    public function get($uuid)
    {
        return $this->contract->getAll($uuid);
    }

    public function find($uuid)
    {
        return $this->contract->find($uuid);
    }

    public function store($uuid, $request)
    {
        return DB::transaction(function () use ($uuid, $request) {
            $this->storeContract($uuid, $request);
        });
    }

    public function storeContract($uuid, $request)
    {
        $employee = $this->employee->find($uuid);

        $this->delete($uuid);

        $data = collect($request)->merge([
            'id'            => Uuid::uuid4()->getHex(),
            'nip_id'        => $employee->nip,
            'file_terms'    => $this->file->uploadToGCS($request['file_terms'],$employee->nip.'_file_terms_'.$request['start_kontrak'],'employee/file_terms'),
            'kontrak_ke'    => (count($this->get($uuid)) + 1),
        ])->all();

        $this->contract->create($data);
        $this->user->setIsactive($employee->user, true);
    }

    public function update($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            $data = collect($request)->diffAssoc($this->find($id));

            if ($data->has('file_terms')) {
                $data->put('file_terms', $this->file->uploadToGCS($request['file_terms'],$request['nip_id'].'_file_terms','employee/file_terms'));
            }

            $this->contract->update($id, $data->all());
        });
    }

    public function delete($uuid)
    {
        $contract = $this->find($uuid);

        $this->user->setIsactive($contract->employee->user, false);
        $this->contract->delete($contract);
    }
}
