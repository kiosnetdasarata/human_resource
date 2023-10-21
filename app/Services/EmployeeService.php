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
use App\Interfaces\Employee\EmployeeCIRepositoryInterface;
use App\Interfaces\Employee\EmployeeArchiveRepositoryInterface;
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
        $employee = $this->findEmployeePersonal($uuid, 'id');

        if ($employee->employeeContract != null) {
            throw new \Exception('data is exist');
        }
        return DB::transaction(function () use ($employee, $request) {
            $this->updateEmployeeConfidential($employee->employeeCI->id, $request);
            $this->storeEmployeeContract(collect($request)->merge(['nip_id' => $employee->nip])->all());
            $this->user->setIsactive($employee->user(), true);
        });
    }

    public function updateEmployee($uuid, $request)
    {
        return DB::transaction(function () use ($uuid, $request) {
            $employee = $this->findEmployeePersonal($uuid, 'id');
            $request = collect($request)->put('nip', $employee['nip']);
            $request->put('nip_id', $employee['nip']);
    
            $this->updateEmployeePersonal($uuid, $request);
            $this->updateEmployeeConfidential($employee->employeeCI->id, $request);
            $this->addEducation($employee->nip, $request);
        });
    }

    public function addEducation($nip, $request) {
        $arr = ['Sarjana', 'SMK/SMA', 'SMP'];
        $edu = $this->findEmployeePersonal($nip, 'nip')->employeeEducation;
        if (count($edu) > 0)
        foreach ($edu as $data) {
            if (array_search($data['pendidikan_terakhir'], $arr) > array_search($request['pendidikan_terakhir'], $arr) && 
                $data['tahun_lulus'] <= $request['tahun_lulus']) {
                    throw new \Exception ('tahun lulus tidak valid');
                }
            if ($request['pendidikan_terakhir'] == 'Sarjana') break;
            if ($data['pendidikan_terakhir'] == $request['pendidikan_terakhir']) {
                throw new \Exception('pendidikan_terakhir : '. $request['pendidikan_terakhir']. ' sudah ada');
            }
        }
        $this->employeeEducation->create($request->all());
    }

    public function getAllEmployeePersonal($withtrashes = false)
    {
        return $withtrashes ? $this->employee->findWithTrashes() : $this->employee->getAll();
    }

    public function findEmployeePersonal($uuid, $table)
    {
       return $this->employee->find($uuid, $table);
    }

    public function findSlugEmployeePersonal($name, $withtrashes = false)
    {
        return $withtrashes ? $this->employee->findBySlugWithTrashes($name) : $this->employee->findBySlug(Str::slug($name,'_'));
    }


    private function storeEmployeePersonal($request)
    {
        return DB::transaction(function ()  use ($request) {
            $data = collect($request)->merge([
                'slug' => Str::slug($request["nama"], '_') . (($count = count($this->findSlugEmployeePersonal($request["nama"]),true)) > 0 ? '_' . $count+1 : ''),
                'id' => Uuid::uuid4()->getHex(),
                'nip' => Carbon::now()->format('ym')
                        . ($request['jenis_kelamin'] == 'Laki-Laki' ? 1 : 2)
                        . count($this->getAllEmployeePersonal(true)),
                'level_id' => $this->role->find($request['role_id'])->level_id,
                'divisi_id' => $this->role->find($request['role_id'])->divisi_id,
                'foto_profil' => 'test dulu',
            ]);

            // $data->put('foto_profil', $request['foto_profil']->storeAs('employee/'.$data['nip'], $request['nip_id'].'_cv.pdf', 'gcs'));

            $this->employee->create($data->all());
            
            if ($data['divisi_id'] === 4) {
                $this->sales->create([
                    'nip_id' => $data['nip'],
                    'slug' => $data['slug'],
                    'id' => Uuid::uuid4()->getHex(),
                    'level_id' => isset($data['level_sales_id']) ? $data['level_sales_id'] : 0,
                ]);

            } else if ($data['divisi_id'] == 5) {
                $this->technician->create([
                    'team_id' => $data['team_id'],
                    'id' => Uuid::uuid4()->getHex(),
                    'slug' => $data['slug'],
                    'nip_id' => $data['nip'],
                    'is_katim' => isset($data['katim']) ? $data['katim'] : 0,
                ]);
            }
            return $data;
        });
    }

    public function updateEmployeePersonal($uuid, $request)
    {
        $old = $this->findEmployeePersonal($uuid, 'id');
        $employee = collect($request)->diffAssoc($old);
    
        if (($employee->has('nama'))) {
            $employee->put(
                'slug', Str::slug($employee["nama"]) . (($count = count($this->findSlugEmployeePersonal($employee["nama"]), true)) > 1 ? '-' . $count + 1 : ''),
            );
            $this->user->update($old->user, $employee['slug']);
            if ($old->sales() != null)
                $this->sales->update($old['nip'], ['slug' => $employee['slug']]);
            if ($old->technician() != null)
                $this->technician->update($old['nip'], ['slug' => $employee['slug']]);
        }
        if ($employee->has('foto_profil'))
            // $employee->put('foto_profil', $request['foto_profil']->storeAs('employee/file_cv', $request['nip_id'].'_cv.pdf', 'gcs'));
        $this->employee->update($old, $employee->all());
    }

    public function deleteEmployeePersonal($uuid)
    {
        return DB::transaction(function ()  use ($uuid) {
            $data = $this->findEmployeePersonal($uuid,'id');
            $contract = $data->employeeContract();
            if ($contract != null && $contract->end_contract > Carbon::now()) {
                throw new \Exception('tidak bisa menghapus karena kontrak belum habis');
            }
            $this->employee->delete($data);
            $this->employeeCI->delete($data);

            $this->employeeArchive->create($data);
        });
    }

    public function getAllEmployeeConfidential()
    {        
    }

    public function findEmployeeConfidential($uuid)
    {
       return $this->employeeCI->find($uuid);
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
        //     'nip_id' => $request['nip'],
        // ]);
        $this->employeeCI->create($data->all());
    }

    public function updateEmployeeConfidential($uuid, $request)
    {
        $old = $this->findEmployeeConfidential($uuid);
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

    public function deleteEmployeeConfidential($uuid)
    {
        return DB::transaction(function ()  use ($uuid) {
            $information = $this->findEmployeeConfidential($uuid);
            $contract = $information->employeeContract();
            if ($contract != null && $contract->end_contract > Carbon::now()) {
                throw new \Exception('tidak bisa menghapus karena kontrak belum habis');
            }
            $this->employeeCI->delete($information);
            $this->user->setIsactive($information->employee->user, true);
        });
    }

    public function findEmployeeContract($uuid, $table)
    {
       return $this->employeeContract->find($uuid, $table);
    }

    private function storeEmployeeContract($request)
    {
        return DB::transaction(function ()  use ($request) {
            $data = collect($request)->merge([
                'file_terms' => "aaaa",
                // $request['file_terms']->storeAs('employee/file_terms', $request['nip_id'].'_terms.pdf', 'gcs'),
                'id' => Uuid::uuid4()->getHex(),
            ]);
            $this->employeeContract->create($data->all());
        });
    }

    public function updateEmployeeContract($id, $request)
    {
        $old = $this->findEmployeeContract($id, 'id');
        $data = collect($request)->diffAssoc($old);
        if ($data->has('file_terms')) {
            // $data->put('file_terms', $request['file_terms']->storeAs('employee/file_terms', $request['nip'].'_terms.pdf', 'gcs'));
        }
        return $this->employeeContract->update($old, $data);
    }

    public function deleteEmployeeContract($uuid)
    {
        return DB::transaction(function ()  use ($uuid) {
            $contract = $this->findEmployeeContract($uuid, 'id');
            if ($contract->end_contract > Carbon::now()) {
                throw new \Exception('tidak bisa menghapus kontrak karena kontrak belum berakhir');
            }
            $this->employeeContract->delete($contract);
            $this->user->setIsactive($contract->employee->user, true);
        });
    }
}
?>