<?php

namespace App\Services;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use App\Interfaces\EmployeeRepositoryInterface;
use App\Interfaces\JobTitleRepositoryInterface;

class EmployeeService
{
    public function __construct(
        private EmployeeRepositoryInterface $employeeRepositoryInterface,
        private JobTitleRepositoryInterface $jobTitleRepositoryInterface)
    {
    }

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

    public function store($request)
    {
        $employee = collect($request);
        $employee = $employee->merge([
            'slug' => Str::slug($employee["nama"], '_')
                        . (($count = count($this->findSlug($employee["nama"]))) > 0 ? '_' . $count+1 : ''),
            'uuid' => Uuid::uuid4()->getHex(),
            'nip_pgwi' => $this->generateNip($employee["tgl_mulai_kerja"], $employee["jk"]),
            'divisi_id' => $this->jobTitleRepositoryInterface->find($employee["jabatan_id"])->divisions_id,
            'district_id' => (int) ($employee["village_id"] / 1000),
            'regencie_id' => (int) ($employee["village_id"] / 1000000),
            'province_id' => (int) ($employee["village_id"] / 100000000),
        ]);
        return $this->employeeRepositoryInterface->create($employee->all());
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
               
        if (isset($employee["jk"]))
            $employee->put('jk', $this->generateNip($request['tgl_mulai_kerja'], $employee['jk']));

        if (isset($employee->job_title_id))
            $employee->put('divisi_id', $this->jobTitleRepositoryInterface->find($employee["jabatan_id"])->divisions_id);
        
        if (isset($employee["village_id"]))
            $employee->merge([
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
            date_create_from_format('Y-m-d', $tgl_kerja)->format('ym') //ambil tahun dan bulan
            . ($jk == 'Laki-Laki' ? 1 : 2) //ambil jenis kelamin
            . count($this->getAll()) //ambil jumlah karyawan
        );
    }
}

?>
