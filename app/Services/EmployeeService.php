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
        DB::beginTransaction();

        try {
            $employeePersonal = $this->storeEmployeePersonal($request);
            $request2 = collect($request)->merge(['nip_id' => $employeePersonal['nip']]);
            $this->storeEmployeeConfidental($request2->all());
            $this->employeeEducation->create($request2->all());
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function secondForm($uuid, $request)
    {
        DB::beginTransaction();

        try {
            $employee = $this->findEmployeePersonal($uuid, 'uuid');
            $this->updateEmployeeConfidental($employee->employeeCI->uuid, $request);
            $this->storeEmployeeContract(collect($request)->merge(['nip_id' => $employee->nip])->all());
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
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
        $data = collect($request)->merge([
            'slug' => Str::slug($request["nama"], '_') . (($count = count($this->findSlugEmployeePersonal($request["nama"]),true)) > 0 ? '_' . $count+1 : ''),
            'uuid' => Uuid::uuid4()->getHex(),
            'nip' => Carbon::now()->format('ym') //ambil tahun dan bulan
                    . ($request['jenis_kelamin'] == 'Laki-Laki' ? 1 : 2) //ambil jenis kelamin
                    . count($this->getAllEmployeePersonal(true)), //ambil jumlah karyawan
            'level_id' => $this->role->find($request['role_id'])->level_id,
        ]);

        $data->put('foto_profil', $request['foto_profil']->storeAs('employee/file_cv', $request['nip_id'].'_cv.pdf', 'gcs'));

        $this->employee->create($data->all());
        
        if ($data['divisi_id'] === 4) {
            $this->sales->create([
                'nip_id' => $data['nip'],
                'slug' => $data['slug'],
                'level_id' => isset($data['level_sales_id']) ? $data['level_sales_id'] : 0,
            ]);

        } else if ($data['divisi_id'] == 5) {
            $this->technician->create([
                'team_id' => $data['team_id'],
                'slug' => $data['slug'],
                'nip_id' => $data['nip_pgwi'],
                'is_katim' => isset($data['katim']) ? $data['katim'] : 0,
            ]);
        }
    
        $this->user->create([
            'nip_id' => $data['nip_pgwi'],
            'slug' => $data['slug'],
            'is_active' => 0,
            'is_leader' => isset($data['is_leader']) ? $data['is_leader'] : 0,
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ]);

        return $data;
    }

    public function updateEmployeePersonal($uuid, $request)
    {
        DB::beginTransaction();
        try {
            $old = $this->findEmployeePersonal($uuid, 'uuid');
            $employee = collect($request)->diffAssoc($old);
            if (isset($employee["nama"]))
                $employee->put(
                    'slug', Str::slug($employee["nama"]) . (($count = count($this->findSlugEmployeePersonal($employee["nama"]), true)) > 1 ? '-' . $count + 1 : ''),
                );
            if (isset($employee['foto_profil']))
                $employee->put('foto_profil', $request['foto_profil']->storeAs('employee/file_cv', $request['nip_id'].'_cv.pdf', 'gcs'));

            $this->employee->update($old, $employee->all());
            $this->user->update($this->user->findByNIP($employee['nip']), $employee['slug']);

            if ($old->sales() != null)
                $this->sales->update($old['nip'], ['slug' => $employee['slug']]);
            if ($old->technician() != null)
                $this->technician->update($old['nip'], ['slug' => $employee['slug']]);

            DB::commit();
            return;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function deleteEmployeePersonal($uuid)
    {
        DB::beginTransaction();

        try {
            $data = $this->findEmployeePersonal($uuid,'uuid');
            $contract = $data->employeeContract();
            if ($contract != null && $contract->end_contract > Carbon::now()) {
                throw new \Exception('tidak bisa menghapus karena kontrak belum habis');
            }
            $this->employee->delete($data);
            $this->employeeArchive->create($data);

            DB::commit();
            return;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $this->employee->delete($data);
    }

    public function getAllEmployeeConfidental()
    {        
    }

    public function findEmployeeConfidental($uuid)
    {
       return $this->employeeCI->find($uuid);
    }

    private function storeEmployeeConfidental($request)
    {
        DB::beginTransaction();
        try {
            $data = collect($request)->merge([
                'foto_ktp' => $request['foto_ktp']->storeAs('employee/foto_ktp', $request['nip_id'].'_ktp.pdf', 'gcs'),
                'foto_kk' => $request['foto_kk']->storeAs('employee/foto_kk', $request['nip_id'].'_kk.pdf', 'gcs'),
                'file_cv' => $request['file_cv']->storeAs('employee/file_cv', $request['nip_id'].'_cv.pdf', 'gcs'),
                'nip_id' => $request['nip'],
            ]);
            $this->employeeCI->create($data->all());

            if ($this->employee->find($data['nip_id'], 'nip')->employeeContract() != null) {
                $this->user->setIsactive($this->user->findByNIP($data['nip_id']), true);
            }
            DB::commit();
            return;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function updateEmployeeConfidental($id, $request)
    {
        DB::beginTransaction();
        try {
            $old = $this->findEmployeeConfidental($id);
            $data = collect($request)->diffAssoc($old);
            if (isset($data['foto_ktp'])) {
                $data->put('foto_ktp', $request['foto_ktp']->storeAs('employee/foto_ktp', $data['nip'].'_ktp.pdf', 'gcs'));
            }
            if (isset($data['foto_kk'])) {
                $data->put('foto_kk', $request['foto_kk']->storeAs('employee/foto_kk', $data['nip'].'_kk.pdf', 'gcs'));
            }
            if (isset($data['file_cv'])) {
                $data->put('file_cv', $request['file_cv']->storeAs('employee/file_cv', $data['nip'].'_cv.pdf', 'gcs'));
            }
            $this->employeeCI->update($old, $data);
            return;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function deleteEmployeeConfidental($uuid)
    {
        DB::beginTransaction();

        try {
            $information = $this->findEmployeeConfidental($uuid);
            $contract = $information->employeeContract();
            if ($contract != null && $contract->end_contract > Carbon::now()) {
                throw new \Exception('tidak bisa menghapus karena kontrak belum habis');
            }
            $this->employeeCI->delete($information);
            $this->user->setIsactive($this->user->findByNIP($information['nip_id']), true);

            DB::commit();
            return;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function getAllEmployeeContract()
    {        
    }

    public function findEmployeeContract($uuid, $table)
    {
       return $this->employeeContract->find($uuid, $table);
    }

    private function storeEmployeeContract($request)
    {
        DB::beginTransaction();
        try {
            $data = collect($request)->merge([
                'file_terms' => $request['file_terms']->storeAs('employee/file_terms', $request['nip_id'].'_terms.pdf', 'gcs'),
            ]);
            $this->employeeContract->create($data->all());

            DB::commit();
            return;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function updateEmployeeContract($id, $request)
    {
        $old = $this->findEmployeeContract($id, 'uuid');
        $data = collect($request)->diffAssoc($old);
        if (isset($data['file_terms'])) {
            $data->put('file_terms', $request['file_terms']->storeAs('employee/file_terms', $request['nip'].'_terms.pdf', 'gcs'));
        }
        $this->employeeContract->update($old, $data);
        return;
    }

    public function deleteEmployeeContract($uuid)
    {
        DB::beginTransaction();
        try {
            $contract = $this->findEmployeeContract($uuid, 'uuid');
            if ($contract['end_contract'] > Carbon::now()) {
                throw new \Exception('tidak bisa menghapus kontrak karena kontrak belum berakhir');
            }
            $this->employeeContract->delete($contract);
            $this->user->findByNIP($contract['nip_id'])->setIsactive(false);
            DB::commit();
            return;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}

?>