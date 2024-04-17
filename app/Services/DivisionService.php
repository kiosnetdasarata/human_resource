<?php
namespace App\Services;

use LogicException;
use Illuminate\Support\Str;
use App\Interfaces\DivisionRepositoryInterface;
use App\Interfaces\Employee\EmployeeRepositoryInterface;

class DivisionService
{
    public function __construct(
        private DivisionRepositoryInterface $division,
        private EmployeeRepositoryInterface $employee
    )
    {
        //
    }

    public function get()
    {
        return $this->division->getAll();
    }

    public function find($id)
    {
        return $this->division->find($id);
    }

    public function findSlug($id)
    {
        return $this->division->findSlug($id);
    }
    public function create($request)
    {
        $manager = $this->employee->find($request['manager_divisi'], 'nip');
        $dataDivision = collect($request)->merge([
            'nama_divisi'   => Str::title($request['nama_divisi']),
            'slug'          => Str::slug($request['nama_divisi'], '_'),
            'email'         => $manager->email,
            'no_tlpn'       => $manager->no_tlpn,
            'is_active'     => 1
        ])->all();

        return $this->division->create($dataDivision);
    }

    public function update($id, $request)
    {
        $old = $this->division->find($id);
        $dataDivision = collect($request)->diffAssoc($old);

        if ($dataDivision->has('nama_divisi')){
            $dataDivision->put('nama_divisi', Str::title($request['nama_divisi']))
                         ->put('slug', Str::slug($request['nama_divisi'], '_'));
        }

        if ($dataDivision->has('manager_divisi')) {
            $manager = $this->employee->find($request['manager_divisi'], 'nip');
            $dataDivision->put('email', $manager->email)
                         ->put('no_tlpn',$manager->no_tlpn);
        }

        if($dataDivision->has('is_active') && !$dataDivision['is_active']){
            if(!count($old->employee)) throw new LogicException('Divisi ini masih memiliki karwawan aktif');
        }

        return $this->division->update($old, $dataDivision->all());
    }
}
