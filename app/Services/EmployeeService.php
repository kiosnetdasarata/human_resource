<?php

namespace App\Services;

use Exception;
use Ramsey\Uuid\Uuid;
use GuzzleHttp\Psr7\Query;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Interfaces\SalesRepositoryInterface;
use App\Http\Requests\Sales\StoreSalesRequest;
use App\Interfaces\EmployeeRepositoryInterface;
use App\Interfaces\JobTitleRepositoryInterface;
use App\Interfaces\TechnicianRepositoryInterface;
use App\Http\Requests\Employee\StoreEmployeeRequest;

class EmployeeService
{
    public function __construct(
        private EmployeeRepositoryInterface $employeeRepositoryInterface,
        private JobTitleRepositoryInterface $jobTitleRepositoryInterface,
        private SalesRepositoryInterface $salesRepositoryInterface,
        private TechnicianRepositoryInterface $technicianRepositoryInterface,
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
                'slug' => Str::slug($employee["nama"], '_')
                            . (($count = count($this->findSlug($employee["nama"]))) > 0 ? '_' . $count+1 : ''),
                'uuid' => Uuid::uuid4()->getHex(),
                'nip_pgwi' => $this->generateNip($employee["tgl_mulai_kerja"], $employee["jk"]),
                'divisi_id' => $this->jobTitleRepositoryInterface->find($employee["jabatan_id"])->divisions_id,
                'district_id' => (int) ($employee["village_id"] / 1000),
                'regencie_id' => (int) ($employee["village_id"] / 1000000),
                'province_id' => (int) ($employee["village_id"] / 100000000),
            ]);
            // $data = array_push([
            //     'slug' => Str::slug($employee["nama"], '_')
            //                 . (($count = count($this->findSlug($employee["nama"]))) > 0 ? '_' . $count+1 : ''),
            //     'uuid' => Uuid::uuid4()->getHex(),
            //     'nip_pgwi' => $this->generateNip($employee["tgl_mulai_kerja"], $employee["jk"]),
            //     'divisi_id' => $this->jobTitleRepositoryInterface->find($employee["jabatan_id"])->divisions_id,
            //     'district_id' => (int) ($employee["village_id"] / 1000),
            //     'regencie_id' => (int) ($employee["village_id"] / 1000000),
            //     'province_id' => (int) ($employee["village_id"] / 100000000),
            // ]);

            // dd($employee['nip_pgwi']);
            $this->employeeRepositoryInterface->create($data->all());
            
            if ($data['divisi_id'] === 4) {

                $sales = [
                    'karyawan_nip' => $data['nip_pgwi'],
                    'komisi_id' => $data['komisi_id'],
                ];
                // $sales = $request->merge([
                //     'karyawan_nip' => $data['nip_pgwi'],
                //     'komisi_id' => $data['komisi_id'],
                // ]);
                dd(((StoreSalesRequest))->rules());
                $data = Validator::make($sales, ((new StoreSalesRequest))->rules());
                    
            }
            // } else if ($data['divisi_id'] == 5) {
            //     // $technician = new StoreTechniciandata();
            //     // $technician->replace($data->all());
            //     // $this->technicianRepositoryInterface->create($technician->validated());
            // }
            
            // $password = $this->generatePassword();
            // $register = Http::post('\api\register', [
            //     'karyawan_nip' => $data['nip_pgwi'],
            //     'is_leader' => $request['is_leader'],
            //     'password' => $password,
            //     'password_confirmation' => $password,
            // ]);

            // if ($register->successful()) throw new \Exception($register['message']);

            DB::commit();
            // return $password;
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
                'slug', Str::slug($employee["nama"])
                    . (($count = count($this->findSlug($employee["nama"]))) > 1 ? '-' . $count + 1 : ''),
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

    private function generatePassword()
    {
        do {
            $randomString = Str::random(8);
        } while (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/', $randomString));

        return $randomString;
    }
}

?>