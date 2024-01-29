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
        private EmployeeEducationRepositoryInterface $employeeEducation,
        private EmployeeContractHistoryRepositoryInterface $employeeContractHistory
    )
    {}

    public function firstForm($request)
    {
        return DB::transaction(function ()  use ($request) {
            $request['tahun_lulus'] > intval(date('Y')) ??
                throw new \Exception('tahun lulus tidak boleh lebih besar dibanding tahun sekarang',422);
            $employeePersonal = $this->storeEmployeePersonal($request);
            
            $employeePersonal->put('nip_id', $employeePersonal['nip']);
            $this->storeEmployeeConfidential($employeePersonal->all());
            
            $this->employeeEducation->create($employeePersonal->all());
            
            $this->user->create([
                'nip_id' => $employeePersonal['nip'],
                'slug' => $employeePersonal['slug'],
                'id' => Uuid::uuid4()->getHex(),
                'is_active' => 0,
                'password' => 'Password1',
            ]);
        });
    }

    public function secondForm($uuid, $request)
    {
        return DB::transaction(function () use ($uuid, $request) {
            $employee = $this->employee->find($uuid);
            
            $employee->employeeContract != null ??
                throw new \Exception('data is exist',422);
            
            $this->updateEmployeeConfidential($employee->employeeCI, $request);
            $this->storeEmployeeContract($uuid, collect($request)->merge(['nip_id' => $employee->nip])->all());
            $this->user->setIsactive($employee->user(), true);
        });
    }

    public function updateEmployee($uuid, $request)
    {
        return DB::transaction(function () use ($uuid, $request) {
            $employee = $this->employee->find($uuid);
            $request = collect($request)->put('nip', $employee['nip']);
            $request->put('nip_id', $employee['nip']);

            $this->updateEmployeePersonal($uuid, $request);
            $this->updateEmployeeConfidential($employee->employeeCI, $request);

            isset($request['pendidikan_terakhir']) ??
                $this->addEducation($employee->id, $request);
        });
    }

    public function getAllEmployeePersonal($withtrashes = false)
    {
        return $withtrashes ? $this->employee->findWithTrashes() : $this->employee->getAll();
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
            $data = collect($request)->merge([
                'slug' => Str::slug($request["nama"], '_'),
                'id' => Uuid::uuid4()->getHex(),
                'nip' => now()->format('ym')
                        . ($request['jenis_kelamin'] == 'Laki-Laki' ? '1' : '0')
                        . count($this->getAllEmployeePersonal(true)),
                'foto_profil' => 'test dulu',
            ]);

            $data->put('foto_profil', uploadToGCS($request['foto_profil'], $data['nip'].'_cv.pdf','employee/'.$data['nip']));

            $this->employee->create($data->all());
            
            if ($data['role_id'] == 2) {
                $this->sales->create([
                    'id' => Uuid::uuid4()->getHex(),
                    'nip_id' => $data['nip'],
                    'slug' => Str::slug($request["nama"], '_'),
                    'no_tlpn' => $data['no_tlpn'],
                    'level_id' => $data['level_sales_id'],
                ]);
            } else if ($data['role_id'] == 3) {
                $this->technician->create([
                    'id' => Uuid::uuid4()->getHex(),
                    'nip_id' => $data['nip'],
                    'slug' => Str::slug($request["nama"], '_'),
                    'nip_id' => 0,
                ]);
            }
            return $data;
        });
    }

    public function updateEmployeePersonal($uuid, $request)
    {
        return DB::transaction(function () use ($uuid, $request) {
            $old = $this->employee->find($uuid);
            $employee = collect($request)->diffAssoc($old);
            if (isset($employee['nama'])) {
                $employee->put(
                    'slug', Str::slug($employee["nama"]),
                );
                $this->user->update($old->user, $employee['slug']);
                $old->sales() != null ??
                    $this->sales->update($old['nip'], ['slug' => $employee['slug']]);
                $old->technician() != null ??
                    $this->technician->update($old['nip'], ['slug' => $employee['slug']]);
            }
            if ($employee->has('foto_profil')) {
                $employee->put('foto_profil', uploadToGCS($request['foto_profil'],$request['nip_id'].'_cv','employee/file_cv'));
            }
            $this->employee->update($old, $employee->all());
        });
    }

    public function deleteEmployeePersonal($request, $uuid)
    {
        return DB::transaction(function ()  use ($uuid, $request) {
            $data = $this->employee->find($uuid);
            $contract = $data->employeeContract;
            if ($contract != null) {
                if ($contract->end_contract > now())
                    throw new \Exception('tidak bisa menghapus karena kontrak belum habis',422);
                
                $this->deleteEmployeeContract($uuid);
            }
            $this->employeeCI->delete($data->employeeCI);
            $this->employee->delete($data);
            $this->user->setIsactive($data->user, false);

            $data = collect($data->toArray())
                    ->merge($data->employeeCI->toArray())
                    ->merge($request)->merge([
                        'tanggal_terminate' => now(),
                        'divisi_id' => $data->role->divisi_id,
                    ]);
            $this->employeeArchive->create($data->all());
        });
    }

    private function storeEmployeeConfidential($request)
    {
        $data = collect($request)->merge([
            'id' => Uuid::uuid4()->getHex(),
            'nip_id' => $request['nip'],
            'foto_ktp' => uploadToGCS($request['foto_ktp'],$request['nip_id'].'_ktp','employee/foto_ktp'),
            'foto_kk' => uploadToGCS($request['foto_kk'],$request['nip_id'].'_kk','employee/foto_kk'),
            'file_cv' => uploadToGCS($request['file_cv'],$request['nip_id'].'_cv','employee/file_cv'),
        ]);
        
        $this->employeeCI->create($data->all());
    }

    public function updateEmployeeConfidential($employeeCI, $request)
    {
        $data = collect($request)->diffAssoc($employeeCI);
        if ($data->has('foto_ktp')) {
            $data->put('foto_ktp', uploadToGCS($request['foto_ktp'],$request['nip_id'].'_ktp','employee/foto_ktp'));
        }
        if ($data->has('foto_kk')) {
            $data->put('foto_kk', uploadToGCS($request['foto_kk'],$request['nip_id'].'_kk','employee/foto_kk'));
        }
        if ($data->has('file_cv')) {
            $data->put('file_cv', uploadToGCS($request['file_cv'],$request['nip_id'].'_cv','employee/file_cv'));
        }
        $this->employeeCI->update($employeeCI, $data->all());
    }

    public function findEmployeeContract($uuid)
    {
        $data = $this->employee->find($uuid)->employeeContract;
        if ($data == null || $data->end_kontrak < now()) {
            throw new ModelNotFoundException('file kontrak tidak ditemukan atau sudah kadaluarsa', 404);
        }
        return $data;
    }

    public function getEmployeeContracts($uuid)
    {
        $data = $this->employee->find($uuid)->employeeContractHistory;
        $data == [] ?? throw new ModelNotFoundException('data tidak ditemukan');
        return $data;
    }

    public function storeEmployeeContract($uuid, $request)
    {
        return DB::transaction(function ()  use ($request, $uuid) {
            $employee = $this->employee->find($uuid);
            if ($employee->employeeContract) {
                $this->employeeContract->delete($employee->employeeContract);
            }
            $path = uploadToGCS($request['file_terms'],$employee->nip.'_file_terms_'.$request['start_kontrak'],'employee/file_terms');
            
            $data = collect($request)->merge([
                'file_terms' => $path,
                'nip_id' => $employee->nip,
                'id' => Uuid::uuid4()->getHex(),
                'kontrak_ke' => (count($this->employeeContractHistory->find($employee->nip)) + 1),
            ]);
            $this->employeeContract->create($data->all());
            $this->employeeContractHistory->create($data->all());
            $this->user->setIsactive($employee->user, true);
        });
    }

    public function updateEmployeeContract($id, $request)
    {
        $old = $this->findEmployeeContract($id);
        $data = collect($request)->diffAssoc($old);
        if ($data->has('file_terms')) {
            $data->put('file_terms', uploadToGCS($request['file_terms'],$request['nip_id'].'_file_terms','employee/file_terms'));
        }
        return $this->employeeContract->update($old, $data->all());
    }

    public function deleteEmployeeContract($uuid)
    {
        return DB::transaction(function ()  use ($uuid) {
            $contract = $this->employee->find($uuid)->employeeContract;
            $this->employeeContract->delete($contract);
            $this->user->setIsactive($contract->employee->user, false);
        });
    }

    public function addEducation($uuid, $request) {
        $request['tahun_lulus'] > date('Y') ??
            throw new \Exception('tahun lulus tidak boleh lebih besar dibanding tahun sekarang',422);
        $arr = ['Sarjana', 'SMK/SMA', 'SMP'];
        $employee = $this->employee->find($uuid);
        $edu = $employee->employeeEducation;
        if (count($edu) > 0) {
            foreach ($edu as $data) {
                if ($request['pendidikan_terakhir'] == 'Sarjana') break;
                if (array_search($data['pendidikan_terakhir'], $arr) > array_search($request['pendidikan_terakhir'], $arr) && 
                    $data['tahun_lulus'] <= $request['tahun_lulus']) {
                    throw new \Exception ('tahun lulus tidak valid',422);
                }
                if ($data['pendidikan_terakhir'] == $request['pendidikan_terakhir']) {
                    throw new \Exception('pendidikan_terakhir jenjang '. $request['pendidikan_terakhir']. ' sudah ada',422);
                }
            }
        }
        return $this->employeeEducation->create(collect($request->all())->put('nip_id',$employee->nip)->all());
    }

    public function getEducations($uuid)
    {
        return $this->employee->find($uuid)->employeeEducation;
    }
    
    public function updateEducation($uuid, $request)
    {
        $edu = $this->employee->find($uuid)->employeeEducation[0];
        return $this->employeeEducation->update($edu, $request);
    }

    public function deleteEducation($uuid)
    {
        $edu = $this->employee->find($uuid)->employeeEducation[0];
        return $this->employeeEducation->delete($edu);
    }
}
?>