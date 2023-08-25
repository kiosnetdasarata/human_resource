<?php

namespace App\Services;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use App\Http\Requests\Employee\EmployeeRequest;
use App\Interfaces\EmployeeRepositoryInterface;
use Illuminate\Support\Carbon;


class EmployeeService
{
    public function __construct(private EmployeeRepositoryInterface $employeeRepositoryInterface)
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
        $slug = Str::slug($name,'_');
        return $this->employeeRepositoryInterface->findSlug($slug);
    }

    public function store($request)
    {
        $request['slug'] = Str::slug($request['nama'], '_')
        . (($count = count($this->findSlug($request['nama']))) > 0 ?
            '_' . $count+1 : '') ;
        $request['uuid'] = Uuid::uuid4()->getHex();
        $request['tgl_lahir'] = date_create_from_format('d/m/Y', $request['tgl_lahir'])->format('Y-m-d');
        $request['tgl_mulai_kerja'] = date_create_from_format('d/m/Y', $request['tgl_mulai_kerja'])->format('Y-m-d');
        $request['nip_pgwi'] = $this->generateNip($request['tgl_mulai_kerja'], $request['jk']);

        return $this->employeeRepositoryInterface->create($request);

    }

    public function update($id, $request)
    {
        if (array_key_exists('nama', $request))
            $request['slug'] = Str::slug($request['nama'])
                . (($count = count($this->findSlug($request['nama']))) > 1 ?
                    '-' . $count + 1 : '') ;

        if (array_key_exists('tgl_lahir', $request))
            $request['tgl_lahir'] = date_create_from_format('d/m/Y', $request['tgl_lahir'])->format('Y-m-d');

        if (array_key_exists('nip_pgwi', $request))
            $request['nip_pgwi'] = $this->generateNip($request['tgl_mulai_kerja'], $request['jk']);

        return $this->employeeRepositoryInterface->update($this->find($id), $request);
    }

    public function delete($data)
    {
        return $this->employeeRepositoryInterface->delete($data);
    }

    private function generateNip($tgl_kerja, $jk)
    {
        return (int)(
            date_create_from_format('Y-m-d', $tgl_kerja)->format('ym') //ambil tahun dan bulan
            . ($jk == 'Laki-Laki' ? 1 : 2) //ambil jenis kelamin
            . count($this->getAll()) //ambil jumlah karyawan
        );

    }
}

?>
