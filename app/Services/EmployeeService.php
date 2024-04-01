<?php

namespace App\Services;

use Ramsey\Uuid\Uuid;
use App\Helpers\FileHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\SalesRepositoryInterface;
use App\Interfaces\TechnicianRepositoryInterface;
use Google\Cloud\Core\Exception\ConflictException;
use App\Interfaces\Employee\EmployeeRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Interfaces\Employee\EmployeeCIRepositoryInterface;
use App\Interfaces\Employee\EmployeeArchiveRepositoryInterface;

class EmployeeService
{
    public function __construct(
        private EmployeeRepositoryInterface $employee,
        private EmployeeCIRepositoryInterface $employeeCI,
        private SalesRepositoryInterface $sales,
        private TechnicianRepositoryInterface $technician,
        private UserRepositoryInterface $user,
        private EmployeeArchiveRepositoryInterface $employeeArchive,
        private FileHelper $file,
        private EmployeeEducationService $education,
        private EmployeeContractService $contract,
    )
    { 
        //
    }

    public function getAllEmployeePersonal($withtrashes = false)
    {
        return $withtrashes ? $this->employee->findWithTrashes() : $this->employee->getAll();
    }

    public function getEmployeeArchive()
    {
        $data = $this->employeeArchive->getAll();
        
        if (!count($data)) { 
            throw new ModelNotFoundException('Data not found');
        } else return $data;
    }

    public function findEmployeePersonal($uuid)
    {
        $data = $this->employee->show($uuid);
        
        if (!$data) { 
            throw new ModelNotFoundException('Data not found');
        } else return $data;
    }

    public function firstForm($request)
    {
        return DB::transaction(function ()  use ($request) {
            $employeePersonal = $this->storePersonal($request);

            $employeePersonal = collect($request)->merge($employeePersonal)->all();

            $this->storeConfidential($employeePersonal);
            $this->education->store($employeePersonal['id'], $employeePersonal);
            $this->createUser($employeePersonal);
        });
    }

    public function secondForm($uuid, $request)
    {
        return DB::transaction(function () use ($uuid, $request) {
            $employee = $this->employee->find($uuid);

            if ($employee->employeeContract) {
                throw new ConflictException('Data contract is exist. You have already filled out this form');
            }
            
            $this->updateConfidential($employee->employeeCI, $request);
            $this->contract->storeContract($uuid, $request);            
            $this->user->setIsactive($employee->user, true);
        });
    }

    public function update($uuid, $request)
    {
        return DB::transaction(function () use ($uuid, $request) {
            $employee = $this->employee->find($uuid);

            $this->updatePersonal($employee, $request);
            $this->updateConfidential($employee->employeeCI, $request);
        });
    }

    public function delete($request, $uuid)
    {
        return DB::transaction(function ()  use ($uuid, $request) {
            $employee = $this->employee->find($uuid);

            $this->contract->delete($uuid);
            $this->employeeCI->delete($employee->employeeCI);
            $this->employee->delete($employee);            
            $this->user->setIsactive($employee->user, false);
            $this->storeArchive($employee, $request);
        });
    }

    private function generateNip($jenisKelamin)
    {
        $prefix = now()->format('ym') . ($jenisKelamin == 'Laki-Laki' ? '1' : '0');
        return $prefix . count($this->getAllEmployeePersonal(true));
    }

    private function createUser($request)
    {
        $data = [
            'id'        => Uuid::uuid4()->getHex(),
            'nip_id'    => $request['nip'],
            'slug'      => $request['slug'],
            'is_active' => 0,
            'password'  => 'Password1',
        ];
        $this->user->create($data);
    }

    private function createSales($employee)
    {
        $data = [
            'id'        => Uuid::uuid4()->getHex(),
            'nip_id'    => $employee['nip'],
            'slug'      => $employee['slug'],
            'no_tlpn'   => $employee['no_tlpn'],
            'level_id'  => $employee['level_sales_id'],
        ];
        return $this->sales->create($data);
    }

    private function createTechnician($employee)
    {
        $data = [
            'id'        => Uuid::uuid4()->getHex(),
            'nip_id'    => $employee['nip'],
            'slug'      => $employee['slug']
        ];
        return $this->technician->create($data);
    }

    private function storePersonal($request)
    {
        $nip = $this->generateNip($request['jenis_kelamin']);
        $slug = Str::slug($request['nama'], '_');

        $data = collect($request)->merge([                
            'id'            => Uuid::uuid4()->getHex(),
            'nip'           => $nip,
            'slug'          => $slug,
            'foto_profil'   => $this->file->uploadToGCS($request['foto_profil'], $nip.'_cv.pdf','employee/'.$nip),
        ])->all();

        $data = $this->employee->create($data);
        
        if ($data['role_id'] == 2) {
            $this->createSales($data);
        } elseif ($data['role_id'] == 3) {
            $this->createTechnician($data);
        }

        return $data;
    }

    private function updatePersonal($old, $request)
    {
        return DB::transaction(function () use ($old, $request) {
            $data = collect($request)->diffAssoc($old);

            if (isset($data['nama'])) {
                $data->put('nama', Str::title($data['nama']))
                     ->put('slug', Str::slug($data['nama']));

                $this->user->update($old->user, $data['slug']);

                if ($old->sales) {
                    $this->sales->update($old['nip'], ['slug' => $data['slug']]);
                } elseif ($old->technician) {
                    $this->technician->update($old['nip'], ['slug' => $data['slug']]);            
                }
            }

            if ($data->has('foto_profil')) {
                $data->put('foto_profil', $this->file->uploadToGCS($data['foto_profil'], $old['nip'].'_cv','employee/file_cv'));
            }

            $this->employee->update($old, $data->all());
        });
    }

    private function storeArchive($employee, $request)
    {
        $data = collect($employee->toArray())
        ->merge($employee->employeeCI->toArray())
        ->merge([
            'divisi_id'         => $employee->role->divisi_id,
            'tanggal_terminate' => now(),
            'status_terminate'  => $request
        ])->all();

        $this->employeeArchive->create($data);
    }

    private function storeConfidential($request)
    {
        $nip = $request['nip'];
        $data = collect($request)->merge([
            'id'        => Uuid::uuid4()->getHex(),
            'nip_id'    => $nip,
            'foto_ktp'  => $this->file->uploadToGCS($request['foto_ktp'], $nip.'_ktp', 'employee/foto_ktp'),
            'foto_kk'   => $this->file->uploadToGCS($request['foto_kk'], $nip.'_kk', 'employee/foto_kk'),
            'file_cv'   => $this->file->uploadToGCS($request['file_cv'], $nip.'_cv', 'employee/file_cv'),
        ])->all();
        
        $this->employeeCI->create($data);
    }

    private function updateConfidential($old, $request)
    {
        $employee = collect($request)->diffAssoc($old);

        if ($employee->has('foto_ktp')) {
            $employee->put('foto_ktp', $this->file->uploadToGCS($request['foto_ktp'],$old['nip_id'].'_ktp','employee/foto_ktp'));
        }

        if ($employee->has('foto_kk')) {
            $employee->put('foto_kk', $this->file->uploadToGCS($request['foto_kk'],$old['nip_id'].'_kk','employee/foto_kk'));
        }

        if ($employee->has('file_cv')) {
            $employee->put('file_cv', $this->file->uploadToGCS($request['file_cv'],$old['nip_id'].'_cv','employee/file_cv'));
        }

        $this->employeeCI->update($old, $employee->all());
    }
}