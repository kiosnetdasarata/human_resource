<?php
namespace App\Services;

use LogicException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\DivisionRepositoryInterface;
use App\Interfaces\Employee\EmployeeRepositoryInterface;

class DivisionService
{
    public function __construct(
        private DivisionRepositoryInterface $division,
        private EmployeeRepositoryInterface $employee,
        private RoleRepositoryInterface $role,
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
        $manager = $this->employee->show($request['manager_divisi'], 'nip');
        $dataDivision = collect($request)->merge([
            'nama_divisi'   => Str::title($request['nama_divisi']),
            'slug'          => Str::slug($request['nama_divisi'], '_'),
            'email'         => $manager->email,
            'no_tlpn'       => $manager->no_tlpn,
        ])->all();

        return $this->division->create($dataDivision);
    }

    public function update($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            $old = $this->division->find($id);
            $dataDivision = collect($request)->diffAssoc($old);

            if (isset($dataDivision['nama_divisi'])){
                $dataDivision->put('nama_divisi', Str::title($request['nama_divisi']))
                             ->put('slug', Str::slug($request['nama_divisi'], '_'));
            }

            if (isset($dataDivision['manager_divisi'])) {
                $manager = $this->employee->show($request['manager_divisi'], 'nip');
                $dataDivision->put('email', $manager->email)
                             ->put('no_tlpn',$manager->no_tlpn);
            }

            if (isset($request['is_active']) && !$request['is_active']) {
                if ($old->employee) throw new LogicException('Divisi ini masih memiliki karyawan aktif.');

                foreach ($old->role as $role) {
                    $this->role->update($role, ['is_active' => 0]);
                }
            }

            return $this->division->update($old, $dataDivision->all());
        });
    }

    public function delete($id)
    {
        $data = $this->division->find($id);

        if ($data->employee) {
            throw new LogicException('Divisi ini masih memiliki karyawan aktif.');
        }

        return $this->division->delete($data);
    }
}
