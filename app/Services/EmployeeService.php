<?php

namespace App\Services;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\SalesRepositoryInterface;
use App\Interfaces\TechnicianRepositoryInterface;
use App\Interfaces\Employee\EmployeeRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Interfaces\Employee\EmployeeCIRepositoryInterface;
use App\Interfaces\Employee\EmployeeArchiveRepositoryInterface;
use App\Interfaces\Employee\EmployeeContractHistoryRepositoryInterface;
use App\Interfaces\Employee\EmployeeContractRepositoryInterface;
use App\Interfaces\Employee\EmployeeEducationRepositoryInterface;

class EmployeeService
{
    public function __construct(
        private EmployeeRepositoryInterface $employee,
        private EmployeeCIRepositoryInterface $employeeCI,   
        private RoleRepositoryInterface $role,
        private SalesRepositoryInterface $sales,
        private TechnicianRepositoryInterface $technician,
        private UserRepositoryInterface $user,
        private EmployeeArchiveRepositoryInterface $employeeArchive,
        private EmployeeContractRepositoryInterface $employeeContract,
        private EmployeeEducationRepositoryInterface $employeeEducation
    )
    {}

    public function firstForm($request)
    {
        return DB::transaction(function ()  use ($request) {
            $employeePersonal = $this->storeEmployeePersonal($request);
            
            $employeePersonal->put('nip_id', $employeePersonal['nip']);
            $this->storeEmployeeConfidential($employeePersonal->all());
            
            $this->employeeEducation->create($employeePersonal->all());
            
            $this->user->create([
                'nip_id'    => $employeePersonal['nip'],
                'slug'      => $employeePersonal['slug'],
                'id'        => Uuid::uuid4()->getHex(),
                'is_active' => 0,
                'password'  => 'Password1',
            ]);
        });
    }

    public function secondForm($uuid, $request)
    {
        return DB::transaction(function () use ($uuid, $request) {
            $employee = $this->employee->find($uuid);
            
            if ($employee->employeeContract)
                throw new \Exception('data is exist',422);
                
            $this->updateEmployeeConfidential($employee->employeeCI, $request);
            $this->storeEmployeeContract($uuid, collect($request)->merge(['nip_id' => $employee->nip])->all());
            $this->user->setIsactive($employee->user(), true);
        });
    }

    public function updateEmployee($uuid, $request)
    {
        return DB::transaction(function () use ($uuid, $request) {
            $this->updateEmployeePersonal($uuid, $request);
            $this->updateEmployeeConfidential($uuid, $request);
        });
    }

    public function getAllEmployeePersonal($withtrashes = false)
    {
        return $withtrashes ? $this->employee->findWithTrashes() : $this->employee->getAll();
    }

    public function getEmployeeArchive()
    {
        return $this->employeeArchive->getAll();
    }

    public function findEmployeePersonal($uuid)
    {
        return $this->employee->show($uuid);
    }

    public function findSlugEmployeePersonal($name, $withtrashes = false)
    {
        return $withtrashes ? $this->employee->findBySlugWithTrashes($name) : $this->employee->findBySlug(Str::slug($name,'_'));
    }

    private function storeEmployeePersonal($request)
    {
        return DB::transaction(function ()  use ($request) {
            $nip = now()->format('ym') . ($request['jenis_kelamin'] == 'Laki-Laki' ? '1' : '0') . count($this->getAllEmployeePersonal(true));
            $data = collect($request)->merge([
                'slug'          => Str::slug($request['nama'], '_'),
                'id'            => Uuid::uuid4()->getHex(),
                'foto_profil'   => uploadToGCS($request['foto_profil'], $nip.'_cv.pdf','employee/'.$nip),
                'nip'           => $nip
            ]);

            $this->employee->create($data->all());
            
            if ($data['role_id'] == 2) {
                $this->sales->create([
                    'id'        => Uuid::uuid4()->getHex(),
                    'nip_id'    => $data['nip'],
                    'slug'      => Str::slug($request['nama'], '_'),
                    'no_tlpn'   => $data['no_tlpn'],
                    'level_id'  => $data['level_sales_id'],
                ]);
            } else if ($data['role_id'] == 3) {
                $this->technician->create([
                    'id'        => Uuid::uuid4()->getHex(),
                    'nip_id'    => $data['nip'],
                    'slug'      => Str::slug($request['nama'], '_')
                ]);
            }
        });
    }

    public function updateEmployeePersonal($uuid, $request)
    {
        return DB::transaction(function () use ($uuid, $request) {
            $old = $this->employee->find($uuid);
            $employee = collect($request)->diffAssoc($old);

            if (isset($employee['nama'])) {
                $employee = $employee->merge([
                                'nama'  => Str::title($employee['nama']),
                                'slug'  => Str::slug($employee['nama']),
                            ]);

                $this->user->update($old->user, $employee['slug']);

                if (!$old->sales()) {
                    $this->sales->update($old['nip'], ['slug' => $employee['slug']]);
                } else if (!$old->technician()) {
                    $this->technician->update($old['nip'], ['slug' => $employee['slug']]);            
                }
            }
            if ($employee->has('foto_profil')) {
                $employee->put('foto_profil', uploadToGCS($employee['foto_profil'], $old['nip'].'_cv','employee/file_cv'));
            }

            $this->employee->update($old, $employee->all());
        });
    }

    public function deleteEmployeePersonal($request, $uuid)
    {
        return DB::transaction(function ()  use ($uuid, $request) {
            $employee = $this->employee->find($uuid);

            $contract = $employee->employeeContract;
            if (!$contract) {                
                $this->deleteEmployeeContract($uuid);
            }

            $this->employeeCI->delete($employee->employeeCI);
            $this->employee->delete($employee);
            $this->user->setIsactive($employee->user, false);

            $this->employeeArchive->create(
                collect($employee->toArray())
                    ->merge($employee->employeeCI->toArray())
                    ->merge($request)
                    ->merge([
                        'tanggal_terminate' => now(),
                        'divisi_id' => $employee->role->divisi_id,
                    ])->all()
            );
        });
    }

    private function storeEmployeeConfidential($request)
    {        
        $this->employeeCI->create(
            collect($request)->merge([
                'id'        => Uuid::uuid4()->getHex(),
                'nip_id'    => $request['nip'],
                'foto_ktp'  => uploadToGCS($request['foto_ktp'],$request['nip_id'].'_ktp','employee/foto_ktp'),
                'foto_kk'   => uploadToGCS($request['foto_kk'],$request['nip_id'].'_kk','employee/foto_kk'),
                'file_cv'   => uploadToGCS($request['file_cv'],$request['nip_id'].'_cv','employee/file_cv'),
            ])->all()
        );
    }

    public function updateEmployeeConfidential($id, $request)
    {
        $old = $this->findEmployeePersonal($id)->employeeCI;
        $employee = collect($request)->diffAssoc($old);
        if ($employee->has('foto_ktp')) {
            $employee->put('foto_ktp', uploadToGCS($request['foto_ktp'],$request['nip_id'].'_ktp','employee/foto_ktp'));
        }
        if ($employee->has('foto_kk')) {
            $employee->put('foto_kk', uploadToGCS($request['foto_kk'],$request['nip_id'].'_kk','employee/foto_kk'));
        }
        if ($employee->has('file_cv')) {
            $employee->put('file_cv', uploadToGCS($request['file_cv'],$request['nip_id'].'_cv','employee/file_cv'));
        }
        $this->employeeCI->update($old, $employee->all());
    }

    public function getEmployeeContracts($uuid)
    {
        $data = $this->employee->find($uuid)->employeeContractHistory;
        if ($data == []) throw new ModelNotFoundException('data tidak ditemukan');
        return $data;
    }

    public function findEmployeeContract($uuid)
    {
        return $this->employeeContract->find($uuid);
    }

    public function storeEmployeeContract($uuid, $request)
    {
        return DB::transaction(function () use ($request, $uuid) {
            $contract = $this->findEmployeeContract($uuid);
            if ($contract) {
                $this->employeeContract->delete($contract);
            }

            $employee = $this->findEmployeePersonal($uuid);            
            $data = collect($request)->merge([
                'file_terms'    => uploadToGCS($request['file_terms'],$employee->nip.'_file_terms_'.$request['start_kontrak'],'employee/file_terms'),
                'nip_id'        => $employee->nip,
                'id'            => Uuid::uuid4()->getHex(),
                'kontrak_ke'    => (count($this->employeeContract->getAll($employee->nip)) + 1),
            ]);
            
            $this->employeeContract->create($data->all());
            $this->user->setIsactive($employee->user, true);
        });
    }

    public function updateEmployeeContract($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            $data = collect($request)->diffAssoc($this->findEmployeeContract($id));
            if ($data->has('file_terms')) {
                $data->put('file_terms', uploadToGCS($request['file_terms'],$request['nip_id'].'_file_terms','employee/file_terms'));
            }
            $this->employeeContract->update($id, $data->all());
        });
    }

    public function deleteEmployeeContract($uuid)
    {
        return DB::transaction(function ()  use ($uuid) {  
            $employee = $this->findEmployeePersonal($uuid);
            $this->user->setIsactive($employee->user, false);
            $this->employeeContract->delete($uuid);          
        });
    }
    
    public function getEducations($uuid)
    {
        return $this->employeeEducation->getAll($uuid);
    }

    public function addEducation($uuid, $request) {
        if ($request['tahun_lulus'] > date('Y')) 
            throw new \Exception('tahun lulus tidak boleh lebih besar dibanding tahun sekarang',422);
        
        $history = $this->employeeEducation->getAll($uuid);
        if (count($history)) {
            foreach ($history as $data) {
                if ($request['pendidikan_terakhir'] == 'Sarjana') break;

                if ($data['pendidikan_terakhir'] == $request['pendidikan_terakhir']) {
                    throw new \Exception('pendidikan_terakhir jenjang '. $request['pendidikan_terakhir']. ' sudah ada',422);
                }

                $arr = ['Sarjana', 'SMK/SMA', 'SMP'];
                if (array_search($data['pendidikan_terakhir'], $arr) < array_search($request['pendidikan_terakhir'], $arr) && 
                    $data['tahun_lulus'] <= $request['tahun_lulus']) {
                    throw new \Exception ('tahun lulus tidak valid',422);
                }
            }
        }
        $nip = $this->employee->find($uuid)->nip;
        return $this->employeeEducation->create(collect($request->all())->put('nip_id',$nip)->all());
    }

    public function findEducation($uuid)
    {
        return $this->employeeEducation->find($uuid);
    }
    
    public function updateEducation($uuid, $request)
    {
        $last = $this->employeeEducation->find($uuid);
        $request = collect($request)->diffAssoc($last);

        if (isset($request['tahun_lulus']) && $request['tahun_lulus'] > date('Y'))
            throw new \Exception('tahun lulus tidak boleh lebih besar dibanding tahun sekarang',422);
        
        $history = $this->employeeEducation->getAll($uuid);
        if (isset($request['pendidikan_terakhir']) && count($history)) {
            foreach ($history as $data) {
                if ($request['pendidikan_terakhir'] == 'Sarjana') break;
                
                if ($data['pendidikan_terakhir'] == $request['pendidikan_terakhir']) {
                    throw new \Exception('pendidikan_terakhir jenjang '. $request['pendidikan_terakhir']. ' sudah ada',422);
                }

                $arr = ['Sarjana', 'SMK/SMA', 'SMP'];
                if (array_search($data['pendidikan_terakhir'], $arr) < array_search($request['pendidikan_terakhir'], $arr) && $data['tahun_lulus'] <= $request['tahun_lulus']) {
                    throw new \Exception ('tahun lulus tidak valid',422);
                }
            }
        }
        $this->employeeEducation->update($uuid, $request->all());
    }

    public function deleteEducation($id)
    {
        return $this->employeeEducation->delete($id);
    }
}