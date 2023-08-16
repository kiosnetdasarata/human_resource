<?php 

namespace App\Services;

use App\Http\Requests\Employee\EmployeeRequest;
use App\Interfaces\EmployeeRepositoryInterface;


class EmployeeService
{
    public function __construct(private EmployeeRepositoryInterface $employeeRepositoryInterface)
    {
    }

    public function getAll()
    {
        return $this->employeeRepositoryInterface->getAll();
    }

    public function find($id)
    {
        return $this->employeeRepositoryInterface->find($id);
    }

    public function store($request)
    {
        $request['tgl_lahir'] = date_create_from_format('d/m/Y', $request['tgl_lahir'])->format('Y-m-d');
        $request['tgl_mulai_kerja'] = date_create_from_format('d/m/Y', $request['tgl_mulai_kerja'])->format('Y-m-d');
        $request['nip_pgwi'] = $this->generateNip($request['tgl_mulai_kerja'], $request['jk']);
        return $this->employeeRepositoryInterface->create($request);
        
    }

    public function update($id, $request)
    {
        $employee = $this->find($id);
        $request['tgl_lahir'] = date_create_from_format('d/m/Y', $request['tgl_lahir'])->format('Y-m-d');
        $request['tgl_mulai_kerja'] = $employee->tgl_mulai_kerja;
        $request['nip_pgwi'] = $this->generateNip($request['tgl_mulai_kerja'], $request['jk']);
        return $this->employeeRepositoryInterface->update($employee, $request);
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