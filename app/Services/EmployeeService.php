<?php

namespace App\Services;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
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
            if ($request['tahun_lulus'] > Carbon::now()->year())
                throw new \Exception('tahun lulus tidak boleh lebih besar dibanding tahun sekarang');$employeePersonal = $this->storeEmployeePersonal($request);
            
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
            $employee = $this->findEmployeePersonal($uuid);
            
            if ($employee->employeeContract != null) {
                throw new \Exception('data is exist');
            }
            $this->updateEmployeeConfidential($uuid, $request);
            $this->storeEmployeeContract($uuid, collect($request)->merge(['nip_id' => $employee->nip])->all());
            $this->user->setIsactive($employee->user(), true);
        });
    }

    public function updateEmployee($uuid, $request)
    {
        return DB::transaction(function () use ($uuid, $request) {
            $employee = $this->findEmployeePersonal($uuid);
            $request = collect($request)->put('nip', $employee['nip']);
            $request->put('nip_id', $employee['nip']);
            $this->updateEmployeePersonal($uuid, $request);
            $this->updateEmployeeConfidential($employee->employeeCI->id, $request);
            if(isset($request['pendidikan_terakhir']))
                $this->addEducation($employee->id, $request);
        });
    }

    public function getAllEmployeePersonal($withtrashes = false)
    {
        return $withtrashes ? $this->employee->findWithTrashes() : $this->employee->getAll();
    }

    public function findEmployeePersonal($uuid)
    {
       $employee = $this->employee->find($uuid);
    //    if ($employee->slug == 'JANGAN_DIUBAH') throw new \Exception ('ini data testing BE, pake yang lain dulu');
       return $employee;
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
                'nip' => Carbon::now()->format('ym')
                        . ($request['jenis_kelamin'] == 'Laki-Laki' ? '01' : '02')
                        . count($this->getAllEmployeePersonal(true)),
                'foto_profil' => 'test dulu',
            ]);

            // $data->put('foto_profil', $request['foto_profil']->storeAs('employee/'.$data['nip'], $request['nip_id'].'_cv.pdf', 'gcs'));

            $this->employee->create($data->all());
            
            if ($data['role_id'] == 2) {
                $this->sales->create([
                    'nip_id' => $data['nip'],
                    'slug' => Str::slug($request["nama"], '_'),
                    'id' => Uuid::uuid4()->getHex(),
                    'no_tlpn' => $data['no_tlpn'],
                    'level_id' => $data['level_sales_id'],
                ]);
            } else if ($data['role_id'] == 3) {
                $this->technician->create([
                    'team_id' => $data['team_id'],
                    'id' => Uuid::uuid4()->getHex(),
                    'slug' => Str::slug($request["nama"], '_'),
                    'nip_id' => $data['nip'],
                    'is_katim' => $data['is_katim'],
                ]);
            }
            return $data;
        });
    }

    public function updateEmployeePersonal($uuid, $request)
    {
        return DB::transaction(function () use ($uuid, $request) {
            $old = $this->findEmployeePersonal($uuid);
            $employee = collect($request)->diffAssoc($old);
            if ($employee->has('nama')) {
                $employee->put(
                    'slug', Str::slug($employee["nama"]),
                );
                $this->user->update($old->user, $employee['slug']);
                if ($old->sales() != null)
                    $this->sales->update($old['nip'], ['slug' => $employee['slug']]);
                if ($old->technician() != null)
                    $this->technician->update($old['nip'], ['slug' => $employee['slug']]);
            }
            // if ($employee->has('foto_profil'))
                // $employee->put('foto_profil', $request['foto_profil']->storeAs('employee/file_cv', $request['nip_id'].'_cv.pdf', 'gcs'));
            $this->employee->update($old, $employee->all());
        });
    }

    public function deleteEmployeePersonal($request, $uuid)
    {
        return DB::transaction(function ()  use ($uuid, $request) {
            $data = $this->findEmployeePersonal($uuid,'id');
            $contract = $data->employeeContract;
            if ($contract != null) {
                if ($contract->end_contract > Carbon::now())
                    throw new \Exception('tidak bisa menghapus karena kontrak belum habis');
                
                $this->deleteEmployeeContract($uuid);
            }
            $this->employeeCI->delete($data->employeeCI);
            $this->employee->delete($data);
            $this->user->setIsactive($data->user, false);

            $data = collect($data->toArray())
                    ->merge($data->employeeCI->toArray())
                    ->merge($request)->merge([
                        'tanggal_terminate' => Carbon::now(),
                        'divisi_id' => $data->role->divisi_id,
                    ]);
            $this->employeeArchive->create($data->all());
        });
    }

    private function storeEmployeeConfidential($request)
    {
        $data = collect($request)->merge([
            'foto_ktp' => "anggep aja masuk",
            'foto_kk' => "anggep aja masuk",
            'file_cv' => "anggep aja masuk",
            'nip_id' => $request['nip'],
            'id' => Uuid::uuid4()->getHex(),
        ]);
        // $data = collect($request)->merge([
        //     'foto_ktp' => $request['foto_ktp']->storeAs('employee/foto_ktp', $request['nip_id'].'_ktp.pdf', 'gcs'),
        //     'foto_kk' => $request['foto_kk']->storeAs('employee/foto_kk', $request['nip_id'].'_kk.pdf', 'gcs'),
        //     'file_cv' => $request['file_cv']->storeAs('employee/file_cv', $request['nip_id'].'_cv.pdf', 'gcs'),
        // ]);
        $this->employeeCI->create($data->all());
    }

    public function updateEmployeeConfidential($uuid, $request)
    {
        $old = $this->findEmployeePersonal($uuid)->employeeCI;
        $data = collect($request)->diffAssoc($old);
        if ($data->has('foto_ktp')) {
            // $data->put('foto_ktp', $request['foto_ktp']->storeAs('employee/foto_ktp', $data['nip'].'_ktp.pdf', 'gcs'));
        }
        if ($data->has('foto_kk')) {
            // $data->put('foto_kk', $request['foto_kk']->storeAs('employee/foto_kk', $data['nip'].'_kk.pdf', 'gcs'));
        }
        if ($data->has('file_cv')) {
            // $data->put('file_cv', $request['file_cv']->storeAs('employee/file_cv', $data['nip'].'_cv.pdf', 'gcs'));
        }

        $this->employeeCI->update($old, $data->all());
    }

    public function findEmployeeContract($uuid)
    {
        $data = $this->findEmployeePersonal($uuid)->employeeContract;
        if ($data == null || $data->end_kontrak < Carbon::now()) {
            throw new ModelNotFoundException('file kontrak tidak ditemukan atau sudah kadaluarsa', 404);
        }
        return $data;
    }

    public function getEmployeeContracts($uuid)
    {
        $data = $this->findEmployeePersonal($uuid)->employeeContractHistory;
        if ($data == []) throw new ModelNotFoundException('data tidak ditemukan');
        return $data;
    }

    public function storeEmployeeContract($uuid, $request)
    {
        return DB::transaction(function ()  use ($request, $uuid) {
            $employee = $this->findEmployeePersonal($uuid);
            if ($employee->employeeContract != null) {
                $this->deleteEmployeeContract($uuid);
            }

            $data = collect($request)->merge([
                'file_terms' => "aaaa",
                'nip_id' => $employee->nip,
                // $request['file_terms']->storeAs('employee/file_terms', $request['nip_id'].'_terms.pdf', 'gcs'),
                'id' => Uuid::uuid4()->getHex(),
            ]);
            
            $this->employeeContract->create($data->all());
            $this->user->setIsactive($employee->user, true);
        });
    }

    public function updateEmployeeContract($id, $request)
    {
        $old = $this->findEmployeeContract($id);
        $data = collect($request)->diffAssoc($old);
        if ($data->has('file_terms')) {
            // $data->put('file_terms', $request['file_terms']->storeAs('employee/file_terms', $request['nip'].'_terms.pdf', 'gcs'));
        }
        return $this->employeeContract->update($old, $data);
    }

    public function deleteEmployeeContract($uuid)
    {
        return DB::transaction(function ()  use ($uuid) {
            $contract = $this->findEmployeePersonal($uuid)->employeeContract;
            $this->employeeContract->delete($contract);
            $history = collect($contract)->merge([
                'kontrak_ke' => (count($this->employeeContractHistory->find($contract->nip_id)) + 1),
                'nip_id' => $contract->nip_id,
            ]);
            $this->employeeContractHistory->create($history->all());
            $this->user->setIsactive($contract->employee->user, false);
        });
    }

    public function addEducation($uuid, $request) {
        if ($request['tahun_lulus'] > Carbon::now()->year())
            throw new \Exception('tahun lulus tidak boleh lebih besar dibanding tahun sekarang');
        $arr = ['Sarjana', 'SMK/SMA', 'SMP'];
        $edu = $this->findEmployeePersonal($uuid)->employeeEducation;
        if (count($edu) > 0)
        foreach ($edu as $data) {
            if ($request['pendidikan_terakhir'] == 'Sarjana') break;
            if (array_search($data['pendidikan_terakhir'], $arr) > array_search($request['pendidikan_terakhir'], $arr) && 
                $data['tahun_lulus'] <= $request['tahun_lulus']) {
                    throw new \Exception ('tahun lulus tidak valid');
                }
            if ($data['pendidikan_terakhir'] == $request['pendidikan_terakhir']) {
                throw new \Exception('pendidikan_terakhir jenjang '. $request['pendidikan_terakhir']. ' sudah ada');
            }
        }
        $this->employeeEducation->create($request->all());
    }
    
    public function updateEducation($uuid, $request)
    {
        $edu = $this->findEmployeePersonal($uuid)->employeeEducation[0];
        return $this->employeeEducation->update($edu, $request);
    }

    public function deleteEducation($uuid)
    {
        $edu = $this->findEmployeePersonal($uuid)->employeeEducation[0];
        return $this->employeeEducation->delete($edu);
    }
}
?>