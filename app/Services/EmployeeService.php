<?php

namespace App\Services;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\SalesRepositoryInterface;
use Illuminate\Validation\ValidationException;
use App\Interfaces\EmployeeRepositoryInterface;
use App\Interfaces\JobTitleRepositoryInterface;
use App\Interfaces\TechnicianRepositoryInterface;
use App\Interfaces\StatusLevelRepositoryInterface;

class EmployeeService
{
    public function __construct(
        private EmployeeRepositoryInterface $employeeRepositoryInterface,
        private JobTitleRepositoryInterface $jobTitleRepositoryInterface,
        private StatusLevelRepositoryInterface $statusLevelRepositoryInterface,
        private SalesRepositoryInterface $salesRepositoryInterface,
        private TechnicianRepositoryInterface $technicianRepositoryInterface,
        private UserRepositoryInterface $userRepositoryInterface
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

    public function findSlug($name)
    {
        return $this->employeeRepositoryInterface->findBySlug(Str::slug($name,'_'));
    }

    public function store($employee)
    {
        DB::beginTransaction();

        try {
            $data = collect($employee)->merge([
                'slug' => Str::slug($employee["nama"], '_') . (($count = count($this->findSlug($employee["nama"]))) > 0 ? '_' . $count+1 : ''),
                'uuid' => Uuid::uuid4()->getHex(),
                'nip_pgwi' => $this->generateNip($employee["tgl_mulai_kerja"], $employee["jk"]),
                'divisi_id' => $this->jobTitleRepositoryInterface->find($employee["jabatan_id"])->divisions_id,
                'district_id' => (int) ($employee["village_id"] / 1000),
                'regencie_id' => (int) ($employee["village_id"] / 1000000),
                'province_id' => (int) ($employee["village_id"] / 100000000),
            ]);

            $this->employeeRepositoryInterface->create($data->all());
            
            if ($data['divisi_id'] === 4) {
                if (!isset($data['komisi_id'])) {
                    throw new ValidationException('komisi id is required');
                }
                $sales = collect([
                    'karyawan_id' => $data['nip_pgwi'],
                    'komisi_id' => $data['komisi_id'],
                    'uuid' => Uuid::uuid4()->getHex(),
                    'level_id' => $this->statusLevelRepositoryInterface->getCommission($data['komisi_id'])->level_id,
                ]);
                $this->salesRepositoryInterface->create($sales->all());

            } else if ($data['divisi_id'] == 5) {
                if (!isset($data['team_id'])) {
                    throw new ValidationException('team id is required');
                }
                $this->technicianRepositoryInterface->create([
                    'team_id' => $data['team_id'],
                    'employees_nip' => $data['nip_pgwi'],
                    'katim' => isset($data['katim']) ? $data['katim'] : 0,
                ]);
            }
        
            $user = $this->userRepositoryInterface->create([
                'karyawan_nip' => $data['nip_pgwi'],
                'is_leader' => isset($data['is_leader']) ? $data['is_leader'] : 0,
                'password' => 'Password1',
                'password_confirmation' => 'Password1',
            ]);
            // dd($user);
            // $register = Http::asForm()->post('http://127.0.0.1:8000/api/register', $user);

            // if (!$register->successful()) throw new \Exception($register['message']);

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function update($id, $request)
    {
        $old = $this->find($id);
        $employee = collect($request)->diffAssoc($old);
        if (isset($employee["nama"]))
            $employee->put(
                'slug', Str::slug($employee["nama"]) . (($count = count($this->findSlug($employee["nama"]))) > 1 ? '-' . $count + 1 : ''),
            );
               
        // if (isset($employee["jk"]))
        //     $employee->put('jk', $this->generateNip($old['tgl_mulai_kerja'], $employee['jk']));

        // if (isset($employee["jabatan_id"]))
        //     $employee->put('divisi_id', $this->jobTitleRepositoryInterface->find($employee["jabatan_id"])->divisions_id);
        
        if (isset($employee["village_id"]))
            $employee = $employee->merge([
                'district_id' => (int) ($employee["village_id"] / 1000),
                'regencie_id' => (int) ($employee["village_id"] / 1000000),
                'province_id' => (int) ($employee["village_id"] / 100000000),
            ]);

        return $this->employeeRepositoryInterface->update($old, $employee->all());
    }

    public function delete($data)
    {

        return $this->employeeRepositoryInterface->delete($data);
    }

    private function generateNip($tgl_kerja, $jk)
    {
        return (int) (
            date_create_from_format('Y-m-d', $tgl_kerja)->format('ymd') //ambil tahun dan bulan
            . ($jk == 'Laki-Laki' ? 1 : 2) //ambil jenis kelamin
            . count($this->getAll()) //ambil jumlah karyawan
        );
    }
}

?>